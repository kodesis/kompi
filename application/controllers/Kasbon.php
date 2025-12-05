<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kasbon extends CI_Controller
{

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/userguide3/general/urls.html
     */

    public function __construct()
    {
        parent::__construct();
        $this->load->model('kasbon_m');
        $this->load->model('nasabah_m');

        $this->load->library('Api_Whatsapp');

        if (!$this->session->userdata('user_logged_in')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['nasabah'] = $this->kasbon_m->get_all_nasabah();
        $data['content'] = 'webview/admin/kasbon/kasbon_table';
        $data['content_js'] = 'webview/admin/kasbon/kasbon_table_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function get_nasabah_detail()
    {
        // 1. Check if the request is an AJAX request
        if ($this->input->is_ajax_request()) {

            // 2. Get the customer ID ('no_cib') sent via POST from the AJAX call
            $no_cib = $this->input->post('no_cib');

            // 3. Call the Model to fetch the data
            // The model method 'get_nasabah' already exists and returns a single row object (or NULL).
            $nasabah_detail = $this->kasbon_m->get_nasabah($no_cib);

            // 4. Prepare and send the JSON response
            if ($nasabah_detail) {
                // Success: Return the needed fields (limit_kredit and usage_kredit)
                echo json_encode([
                    'success' => true,
                    'data' => [
                        // Ensure these property names match the columns in your 't_nasabah' table
                        'limit_kredit' => $nasabah_detail->kredit_limit,
                        'usage_kredit' => $nasabah_detail->kredit_usage,
                    ]
                ]);
            } else {
                // Failure: Customer not found
                echo json_encode([
                    'success' => false,
                    'message' => 'Nasabah not found.'
                ]);
            }
        } else {
            // Optional: Block non-AJAX direct access
            show_404();
        }
    }

    public function ajax_list()
    {
        $nasabah = $this->input->post('nasabah');

        $list = $this->kasbon_m->get_datatables($nasabah);
        $data = array();
        $no = $_POST['start'];


        foreach ($list as $cat) {
            $date_string = $cat->tanggal_jam;
            $timestamp = strtotime($date_string);

            // 1. Define the Indonesian month names
            $bulan = array(
                1 => 'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );

            // 2. Format the date components
            $tanggal = date('d', $timestamp);      // Day (e.g., '12')
            $bulan_index = (int)date('m', $timestamp); // Month number (e.g., 1)
            $tahun = date('Y', $timestamp);        // Year (e.g., '2025')

            // 3. Assemble the final date string
            $formatted_date = $tanggal . ' ' . $bulan[$bulan_index] . ' ' . $tahun;

            $no++;
            $row = array();
            $row[] = $cat->id;
            // $row[] = $cat->no_cib;
            $row[] = $cat->nama;
            // $row[] = $cat->tanggal_jam;
            $row[] = $formatted_date;
            $row[] = 'Rp.' . number_format($cat->nominal);
            $row[] = 'Rp.' . number_format($cat->nominal_kredit);
            $row[] = 'Rp.' . number_format($cat->nominal_cash);

            $status_html = ''; // Initialize the variable for the status display

            if ($cat->status == 1) {
                // Status is 1 (Terverifikasi / Verified)
                // Use a Bootstrap badge/button class like btn-success for green
                $status_html = '<span class="badge badge-success">Terverifikasi</span>';

                // If you specifically need a clickable button:
                // $status_html = '<button class="btn btn-success btn-sm">Terverifikasi</button>';

            } else {
                // Status is 0 (Belum Diverifikasi / Not Verified)
                // Use a Bootstrap badge/button class like btn-danger for red
                $status_html = '<span class="badge badge-danger">Belum Diverifikasi</span>';

                // If you specifically need a clickable button:
                // $status_html = '<button class="btn btn-danger btn-sm">Belum Diverifikasi</button>';
            }

            // Add the generated HTML to the row
            $row[] = $status_html;

            $data[] = $row;
        }

        $total_nominal = $this->kasbon_m->get_filtered_nominal_sum($nasabah);
        $total_nominal_kredit = $this->kasbon_m->get_filtered_nominal_kredit_sum($nasabah);
        $total_nominal_cash = $this->kasbon_m->get_filtered_nominal_cash_sum($nasabah);

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->kasbon_m->count_all($nasabah),
            "recordsFiltered" => $this->kasbon_m->count_filtered($nasabah),
            "total_nominal_sum" => number_format($total_nominal, 0, ',', '.'), // Format and include in response
            "total_nominal_kredit_sum" => number_format($total_nominal_kredit, 0, ',', '.'), // Format and include in response
            "total_nominal_cash_sum" => number_format($total_nominal_cash, 0, ',', '.'), // Format and include in response
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add()
    {
        $data['nasabah'] = $this->kasbon_m->get_all_nasabah();
        $data['form_data'] = $this->session->flashdata('form_data');

        $data['title'] = 'Add';
        $data['content'] = 'webview/admin/kasbon/kasbon_form';
        $data['content_js'] = 'webview/admin/kasbon/kasbon_form_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }
    public function get_kredit_info()
    {
        // Check if the request is an AJAX request (optional, but good practice)
        // if (!$this->input->is_ajax_request()) {
        //     exit('No direct script access allowed');
        // }

        // 1. Get the customer ID from the URI segment (e.g., kasbon/get_kredit_info/12345)
        $nasabah_no_cib = $this->uri->segment(3); // Adjust segment number based on your route configuration

        // Check for a valid ID
        if (empty($nasabah_no_cib)) {
            echo json_encode(['success' => false, 'message' => 'No customer ID provided.']);
            return;
        }

        // 3. Call a model method to fetch the required credit data
        $kredit_data = $this->kasbon_m->get_customer_credit_details($nasabah_no_cib);

        // 4. Process the result and send JSON response
        if ($kredit_data) {
            // Data found successfully
            $response = [
                'success' => true,
                'data'    => [
                    // Format values (e.g., remove currency formatting if you'll re-format in JS)
                    'limit' => number_format($kredit_data->kredit_limit, 0, ',', '.'),
                    'usage' => number_format($kredit_data->kredit_usage, 0, ',', '.')
                ]
            ];
        } else {
            // Data not found
            $response = [
                'success' => false,
                'message' => 'Credit data not found for this customer.'
            ];
        }

        // Set the content type header and output the JSON response
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    private function generate_unique_kasbon_token()
    {
        do {
            // Generate a new 6-digit number
            $token = strval(random_int(100000, 999999));

            // Check if the token exists in the database using a model function
            $exists = $this->kasbon_m->check_token_exists($token);
        } while ($exists); // Repeat loop if token exists

        return $token;
    }

    public function save_kasbon()
    {
        $date = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');
        $id_nasabah = $this->input->post('nasabah');
        $nasabah = $this->kasbon_m->get_nasabah($id_nasabah);

        // Clean and convert nominal inputs
        $nominal = (int) str_replace('.', '', $this->input->post('nominal'));
        $nominal_kredit = (int) str_replace('.', '', $this->input->post('nominal_kredit'));
        $nominal_cash = (int) str_replace('.', '', $this->input->post('nominal_cash'));

        // --- 🔑 ENSURE UNIQUE TOKEN START ---
        $token = $this->generate_unique_kasbon_token();
        // --- 🔑 ENSURE UNIQUE TOKEN END ---

        // Get the current year and generate the unique ID (rest of your existing logic)
        $current_year = date('Y');
        $latest_entry = $this->kasbon_m->get_latest_entry($current_year);

        if ($latest_entry) {
            $latest_number = (int) substr($latest_entry->id, 0, 6);
            $new_number = str_pad($latest_number + 1, 6, '0', STR_PAD_LEFT);
        } else {
            $new_number = "000001"; // Start from 000001 if no previous entry
        }

        $new_id = $new_number . $current_year;

        // Save the new data
        $sub_id = $this->kasbon_m->save_file([
            'id'             => $new_id, // New generated ID
            'tanggal_jam'    => $date,
            'id_nasabah'     => $id_nasabah,
            'nominal'        => $nominal,
            'nominal_kredit' => $nominal_kredit,
            'nominal_cash'   => $nominal_cash,
            'token'          => $token, // The guaranteed unique token
            'id_kasir'       => $this->session->userdata('user_user_id'),
            'status'         => 0
        ]);

        $msg = "Kode verifikasi Anda adalah: " . $token . " \n Gunakan kode ini untuk melengkapi proses verifikasi Kasbon anda.";
        $response = $this->api_whatsapp->wa_notif($msg, $nasabah->no_telp);

        if (!$response) {
            echo json_encode(["status" => FALSE, "error" => "Failed to send WhatsApp message"]);
            exit;
        } elseif (isset($response['error'])) {
            echo json_encode(["status" => FALSE, "error" => $response['error']]);
            exit;
        }

        // If successful
        echo json_encode(["status" => TRUE, "sub_id" => $sub_id, "Telp" => $nasabah->no_telp]);
    }

    public function verifikasi()
    {
        $data['title'] = 'Verifikasi';
        $data['content'] = 'webview/admin/kasbon/kasbon_verifikasi';
        $data['content_js'] = 'webview/admin/kasbon/kasbon_verifikasi_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function proses_verifikasi()
    {
        $date = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $kode = $this->input->post('kode');

        $kasbon_data = $this->kasbon_m->get_kasbon_by_nasabah_and_token($kode);

        if ($kasbon_data) {
            $data_update = [
                'token'            => null,
                'status'            => 1,
            ];
            $this->kasbon_m->update_data_kasbon($data_update, array('sub_id' => $kasbon_data->sub_id));

            // $anggota = $this->nasabah_m->get_nasabah($kasbon_data->id_nasabah);
            // $usage_kredit = $anggota->usage_kredit + $kasbon_data->nominal_kredit;
            $target_year = date('Y'); // Example: Get the current year
            $target_month = date('m'); // Example: Get the current month (01-12)

            $this->db->select_sum('nominal_kredit');
            $this->db->from('t_kasbon');
            $this->db->join('t_nasabah', 't_nasabah.no_cib = t_kasbon.id_nasabah');
            $this->db->where('id_nasabah', $kasbon_data->id_nasabah);
            $this->db->where('t_kasbon.status', '1');

            // 🌟 ADD YEAR CONDITION 🌟
            // Assuming the date column is named 'tanggal_nota'
            $this->db->where("YEAR(t_kasbon.tanggal_jam)", $target_year);

            // 🌟 ADD MONTH CONDITION 🌟
            $this->db->where("MONTH(t_kasbon.tanggal_jam)", $target_month);
            $query = $this->db->get();
            $result = $query->row();
            // $total_kredit = $result->usage_kredit;
            $usage_kredit = $result->nominal_kredit;

            $this->kasbon_m->update_data_nasabah(['kredit_usage' => $usage_kredit], ['no_cib' => $kasbon_data->id_nasabah]);

            // echo json_encode(array("status" => TRUE, "title" => $title));
            // $this->nota_management->update_data($data_update, array('Id' => $id_edit));
            echo json_encode(array("status" => TRUE, "id_nasabah" => $kasbon_data->id_nasabah));
        } else {
            echo json_encode(array("status" => FALSE, "Pesan" => 'Token Salah'));
        }
    }

    public function export_kasbon_by_date()
    {
        // Mengambil data dengan filter 'Belum Dibayar'
        $nasabah = $this->input->post('nasabah');
        $tanggal_dari = $this->input->post('tanggal_dari');
        $tanggal_sampai = $this->input->post('tanggal_sampai');

        // echo $nasabah;
        // echo $tanggal_dari;
        // echo $tanggal_sampai;
        $list = $this->kasbon_m->get_kasbon_data_for_export($nasabah, $tanggal_dari, $tanggal_sampai);

        // var_dump($list);
        // Load library PhpSpreadsheet secara manual
        // Baris ini memuat semua kelas yang dibutuhkan, jadi baris di bawahnya tidak diperlukan
        require APPPATH . 'third_party/autoload.php';
        // Hapus baris ini karena tidak diperlukan dan bisa menyebabkan masalah
        require APPPATH . 'third_party/psr/simple-cache/src/CacheInterface.php';

        // Membuat objek Spreadsheet baru
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Mengatur header kolom
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'Tanggal Transaksi');
        // $sheet->setCellValue('D1', 'Keterangan');
        $sheet->setCellValue('D1', 'Nominal');
        $sheet->setCellValue('E1', 'Nominal Kredit');
        $sheet->setCellValue('F1', 'Nominal Cash');

        // --- Initialize Total Variables ---
        $total_nominal = 0;
        $total_kredit = 0;
        $total_cash = 0;
        // ----------------------------------

        // ------------------------------------------------------------------
        // --- NEW HEADER STYLING SECTION ---

        // 1. Define the style array
        $headerStyle = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                // Use FILL_SOLID
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                // Hex code for Sky Blue (e.g., Light Sky Blue or similar)
                'startColor' => [
                    'argb' => 'FF87CEEB', // Hex code for light sky blue (FF is for opacity)
                ],
            ],
            'borders' => [ // Optional: Add borders for better visibility
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        // 2. Apply the style to the header range (A1 to F1)
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // --- END NEW HEADER STYLING SECTION ---
        // ------------------------------------------------------------------


        $row = 2;
        $no = 1;
        foreach ($list as $data) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data->nama);

            // Logika untuk menampilkan tanggal yang sudah diformat atau '-'
            if (empty($data->tanggal_jam)) {
                $sheet->setCellValue('C' . $row, '-');
            } else {
                $sheet->setCellValue('C' . $row, date('d F Y H:i:s', strtotime($data->tanggal_jam)));
            }

            $sheet->setCellValue('D' . $row, $data->nominal);
            $sheet->setCellValue('E' . $row, $data->nominal_kredit);
            $sheet->setCellValue('F' . $row, $data->nominal_cash);

            $total_nominal += (float)$data->nominal;
            $total_kredit += (float)$data->nominal_kredit;
            $total_cash += (float)$data->nominal_cash;
            // ----------------------------------------------
            $row++;
        }
        // --- ADD TOTAL ROW SECTION ---
        $highestColumn = $sheet->getHighestColumn();

        // Define the row number for the total (one row after the last data row)
        $total_row = $row;

        // Merge the first three cells for the "Total" label
        $sheet->mergeCells('A' . $total_row . ':C' . $total_row);
        $sheet->setCellValue('A' . $total_row, 'TOTAL KESELURUHAN');

        // Apply styling to the total row (e.g., bold)
        $styleArray = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFCC00']], // Optional: yellow background
            'borders' => [ // Optional: Add borders for better visibility
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A' . $total_row . ':' . $highestColumn . $total_row)->applyFromArray($styleArray);

        // Write the calculated sums
        $sheet->setCellValue('D' . $total_row, $total_nominal);
        $sheet->setCellValue('E' . $total_row, $total_kredit);
        $sheet->setCellValue('F' . $total_row, $total_cash);

        // --- END OF TOTAL ROW SECTION ---

        // --- Add this section to auto-size the columns ---
        foreach (range('A', $highestColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        // --- End of new section ---

        require APPPATH . 'third_party/autoload_zip.php';

        // Gunakan Xlsx writer untuk menyimpan file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Atur header untuk mengunduh file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Export_Kasbon_' . date('Ymd_His') . '.xlsx"');
        header('Cache-Control: max-age=0');

        // OUTPUT FILE KE BROWSER
        // Baris ini adalah yang terpenting! Anda harus memanggilnya.
        $writer->save('php://output');

        // Hentikan eksekusi skrip setelah file selesai di-output
        exit();
    }
}
