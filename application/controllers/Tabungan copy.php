<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tabungan extends CI_Controller
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
        $this->load->model('tabungan_m');

        if (!$this->session->userdata('user_logged_in')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['nasabah'] = $this->tabungan_m->get_nasabah();
        $data['content'] = 'webview/admin/tabungan/tabungan_table';
        $data['content_js'] = 'webview/admin/tabungan/tabungan_table_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function ajax_list()
    {
        $nasabah = $this->input->post('nasabah');

        $list = $this->tabungan_m->get_datatables($nasabah);
        $data = array();
        $no = $_POST['start'];


        foreach ($list as $cat) {

            $no++;
            $row = array();
            // $row[] = $no;
            $row[] = $cat->no_urut;
            $row[] = $cat->no_tabungan;
            $row[] = $cat->nama;
            $row[] = $cat->nama_tabungan;
            $row[] = $cat->status_tabungan;
            $row[] = 'Rp.' . number_format($cat->nominal);
            $row[] = $cat->spread_rate;
            $row[] = $cat->nominal_blokir;
            $row[] = $cat->pos_rate;
            $row[] = $cat->nolsp;

            if ($this->session->userdata('role') == 1) {
                $row[] = '<a class="btn btn-primary m-1" href="' . base_url('tabungan/detail_tabungan/' . $cat->no_tabungan) . '">Detail</a> <a class="btn btn-warning m-1" href="' . base_url('tabungan/edit/' . $cat->no_cib) . '">Edit</a> <a class="btn btn-danger m-1" 
   href="javascript:void(0)" 
   onclick="confirmDelete(' . $cat->no_cib . ')">
    Delete
</a>';
            } else {

                $row[] = '<a class="btn btn-primary m-1" href="' . base_url('tabungan/detail_tabungan/' . $cat->no_tabungan) . '">Detail</a>';
            }


            $data[] = $row;
        }

        $total_nominal = $this->tabungan_m->get_filtered_nominal_sum($nasabah);


        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->tabungan_m->count_all(),
            "recordsFiltered" => $this->tabungan_m->count_filtered($nasabah),
            "total_nominal_sum" => number_format($total_nominal, 0, ',', '.'), // Format and include in response
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add()
    {
        $data['new_tabungan_number'] = $this->tabungan_m->generate_next_no_tabungan();
        $data['new_no_urut'] = $this->tabungan_m->generate_next_no_urut();
        $data['jenis_tabungan'] = $this->tabungan_m->get_jenis_tabungan();
        $data['nasabah'] = $this->tabungan_m->get_nasabah();
        $data['form_data'] = $this->session->flashdata('form_data');

        $data['title'] = 'Add';
        $data['content'] = 'webview/admin/tabungan/tabungan_form';
        $data['content_js'] = 'webview/admin/tabungan/tabungan_form_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function edit($id)
    {
        $data['tabungan'] = $this->tabungan_m->get_tabungan($id);
        $data['nasabah'] = $this->tabungan_m->get_nasabah();
        $data['jenis_tabungan'] = $this->tabungan_m->get_jenis_tabungan();

        $data['title'] = 'Edit';
        $data['content'] = 'webview/admin/tabungan/tabungan_form';
        $data['content_js'] = 'webview/admin/tabungan/tabungan_form_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function proccess_add()
    {
        // Assume input data is captured from POST. We collect this first 
        // to flash it back (old input) if validation fails.
        $data = [
            'no_tabungan' => $_POST['no_tabungan'] ?? '',
            'no_cib' => $_POST['no_cib'] ?? '',
            'jenis_tabungan' => $_POST['jenis_tabungan'] ?? '',
            'status_tabungan' => $_POST['status_tabungan'] ?? '',
            'no_urut' => $_POST['no_urut'] ?? '',
            'nominal' => $_POST['nominal'] ?? '',
            'spread_rate' => $_POST['spread_rate'] ?? '',
            'nominal_blokir' => $_POST['nominal_blokir'] ?? '',
            'pos_rate' => $_POST['pos_rate'] ?? '',
            'nolsp' => $_POST['nolsp'] ?? '',
        ];

        // --- SET VALIDATION RULES (Conceptual Framework Syntax) ---
        // In a real framework, you would typically load the validation library first.
        $this->form_validation->set_rules('no_tabungan', 'Nomor Tabungan', 'required|max_length[8]');
        // $this->form_validation->set_rules('alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('no_urut', 'Nomor Urut', 'required|numeric|max_length[5]'); // Assuming 16 digits
        $this->form_validation->set_rules('nominal', 'Nominal', 'numeric');
        $this->form_validation->set_rules('spread_rate', 'Spread Rate', 'required|numeric');
        $this->form_validation->set_rules('nominal_blokir', 'Nominal Blokir', 'required|numeric');
        $this->form_validation->set_rules('pos_rate', 'Pos Rate', 'required|numeric');
        $this->form_validation->set_rules('nolsp', 'Nomor LSP', 'required|numeric');
        // $this->form_validation->set_rules('tgl_pendaftaran', 'Tgl Pendaftaran', 'required|valid_date');
        $this->form_validation->set_rules('no_cib', 'Nasabah', 'required');
        $this->form_validation->set_rules('jenis_tabungan', 'Jenis Tabungan', 'required');

        // Run the validation
        if ($this->form_validation->run() == FALSE) {
            // --- VALIDATION FAILED: Use Flash Data to store errors and old input ---

            // Retrieve all validation errors as an array
            $errors = $this->form_validation->error_array();

            // Store errors in session flash data
            $this->session->set_flashdata('form_errors', $errors);

            // $this->session->set_flashdata('message_error', $errors);


            // Store all submitted data (old input) in session flash data
            $this->session->set_flashdata('form_data', $data);

            // Redirect back to the form view (tabungan/add)
            redirect('tabungan/add');
            // header('Location: ' . base_url('tabungan/add'));
            // exit();
        } else {

            $this->db->insert('t_tabungan', $data);
            // --- VALIDATION SUCCESS: Process the data ---

            // Example: $this->tabungan_model->save($data);
            $this->session->set_flashdata('message_name', 'Tabungan Berhasil di Tambahkan.');

            // Redirect to a success page
            redirect('tabungan/add');
            exit();
        }
    }
    public function proccess_edit()
    {
        // Assume input data is captured from POST. We collect this first 
        // to flash it back (old input) if validation fails.
        $edit_data = [
            'no_cib' => $_POST['no_cib'] ?? '',
            'jenis_tabungan' => $_POST['jenis_tabungan'] ?? '',
            'status_tabungan' => $_POST['status_tabungan'] ?? '',
            'no_urut' => $_POST['no_urut'] ?? '',
            'nominal' => $_POST['nominal'] ?? '',
            'spread_rate' => $_POST['spread_rate'] ?? '',
            'nominal_blokir' => $_POST['nominal_blokir'] ?? '',
            'pos_rate' => $_POST['pos_rate'] ?? '',
            'nolsp' => $_POST['nolsp'] ?? '',
        ];


        $this->db->where('no_tabungan', $this->input->post('no_tabungan'));
        if ($this->db->update('t_tabungan', $edit_data)) {

            $this->session->set_flashdata('message_name', 'Tabungan Berhasil di Ubah.');
            redirect('tabungan');
        } else {

            $this->session->set_flashdata('message_error', 'Tabungan Gagal di Ubah.');
            redirect('tabungan/edit/' . $this->input->post('no_tabungan'));
        }
    }

    public function delete($id)
    {
        $this->db->where('no_tabungan', $id);
        if ($this->db->delete('t_tabungan')) {

            $this->session->set_flashdata('message_name', 'Tabungan Berhasil di Hapus.');
            redirect('tabungan');
        } else {

            $this->session->set_flashdata('message_error', 'Tabungan Gagal di Hapus.');
            redirect('tabungan');
        }

        // echo json_encode(array("status" => 'success', "message" => "Berhasil Menghapus Data"));

        // redirect('perusahaan/cabang');
    }

    public function detail_tabungan()
    {
        $data['nasabah'] = $this->tabungan_m->get_nasabah();
        $data['content'] = 'webview/admin/tabungan/simpanan_table';
        $data['content_js'] = 'webview/admin/tabungan/simpanan_table_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function ajax_list_detail($id)
    {

        $list = $this->tabungan_m->get_datatables_detail($id);
        $data = array();
        $no = $_POST['start'];


        foreach ($list as $cat) {
            $date_string = $cat->tgl_transaksi;
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
            // $row[] = $no;
            $row[] = $no;
            $row[] = $cat->transaksi;
            // if ($cat->transaksi == 1) {
            //   $row[] = 'Setor';
            // } else if ($cat->transaksi == 2) {
            //   $row[] = 'Kredit';
            // } else {
            //   $row[] = '';
            // }
            $row[] = 'Rp.' . number_format($cat->nominal);
            $row[] = $cat->ket;
            $row[] = $formatted_date;
            $row[] = $cat->nama;

            $data[] = $row;
        }

        $total_nominal = $this->tabungan_m->get_filtered_nominal_sum_detail($id);

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->tabungan_m->count_all_detail($id),
            "recordsFiltered" => $this->tabungan_m->count_filtered_detail($id),
            "total_nominal_sum" => number_format($total_nominal, 0, ',', '.'), // Format and include in response

            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add_simpanan()
    {
        $data['new_tabungan_number'] = $this->tabungan_m->generate_next_no_tabungan();
        $data['new_no_urut'] = $this->tabungan_m->generate_next_no_urut();
        $data['jenis_tabungan'] = $this->tabungan_m->get_jenis_tabungan();
        $data['nasabah'] = $this->tabungan_m->get_nasabah();
        $data['form_data'] = $this->session->flashdata('form_data');

        $data['title'] = 'Add';
        $data['content'] = 'webview/admin/tabungan/simpanan_form';
        $data['content_js'] = 'webview/admin/tabungan/simpanan_form_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function transaksi_simpanan()
    {
        // $data['tabungan'] = $this->tabungan_m->get_all_tabungan();


        // 1. Get the raw data from the model
        $raw_tabungan_data = $this->tabungan_m->get_all_tabungan();

        // 2. Transform the data into Select2 format (id and text)
        $select2_tabungan_data = [];
        foreach ($raw_tabungan_data as $tabungan_item) {
            $select2_tabungan_data[] = [
                'id' => $tabungan_item->no_tabungan, // This is the value
                'text' => $tabungan_item->no_tabungan . ' - ' . $tabungan_item->nama // This is what the user sees
            ];
        }

        $data['tabungan'] = $select2_tabungan_data;

        // 3. Pass the transformed data to the view
        $data['debit'] = $this->tabungan_m->list_coa_kepala_1();
        $data['kredit'] = $this->tabungan_m->list_coa_kepala_2();

        $data['title'] = 'Transaksi Simpanan';
        $data['content'] = 'webview/admin/tabungan/simpanan_form';
        $data['content_js'] = 'webview/admin/tabungan/simpanan_form_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    private function _parse_rupiah($rupiah)
    {
        // Hilangkan Rp, titik, dan ganti koma dengan titik
        $rupiah = str_replace(['Rp', '.', ' '], '', $rupiah);
        return floatval(str_replace(',', '.', $rupiah));
    }

    private function update_saldo_coa($akun_no, $jumlah, $tipe)
    {
        $substr_coa = substr($akun_no, 0, 1);
        if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
            $table = "t_coa_sbb";
            $kolom = "no_sbb";
        } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
            $table = "t_coalr_sbb";
            $kolom = "no_lr_sbb";
        }

        $query = $this->db->query(
            "SELECT posisi, nominal FROM $table WHERE " . $kolom . " = ? FOR UPDATE",
            [$akun_no]
        );

        $row = $query->row();
        if (!$row) return FALSE;

        $posisi = $row->posisi;
        $nominal = $row->nominal;

        if ($posisi == 'AKTIVA') {
            if ($tipe == 'debit') {
                $nominal += $jumlah;
            } else { // kredit
                $nominal -= $jumlah;
            }
        } elseif ($posisi == 'PASIVA') {
            if ($tipe == 'debit') {
                $nominal -= $jumlah;
            } else { // kredit
                $nominal += $jumlah;
            }
        }

        // Update saldo
        $this->db->where(($table == 't_coa_sbb') ? 'no_sbb' : 'no_lr_sbb', $akun_no);
        $this->db->update($table, ['nominal' => $nominal]);
    }

    private function get_saldo_coa($akun_no)
    {
        $substr_coa = substr($akun_no, 0, 1);
        if ($substr_coa == "1" || $substr_coa == "2" || $substr_coa == "3") {
            $table = "t_coa_sbb";
            $kolom = "no_sbb";
        } else if ($substr_coa == "4" || $substr_coa == "5" || $substr_coa == "6" || $substr_coa == "7" || $substr_coa == "8" || $substr_coa == "9") {
            $table = "t_coalr_sbb";
            $kolom = "no_lr_sbb";
        }

        $row = $this->db->select('nominal')
            ->where($kolom, $akun_no)
            ->get($table)
            ->row();

        return $row->nominal;
    }

    private function posting($coa_debit, $coa_kredit, $keterangan, $nominal, $tanggal, $tipe_transaksi, $id_invoice = NULL, $base64_data = NULL, $nama_data = NULL)
    {
        // Update coa debit 
        $update_saldo_debit = $this->update_saldo_coa($coa_debit, $nominal, 'debit');
        // Update coa kredit
        $update_saldo_kredit = $this->update_saldo_coa($coa_kredit, $nominal, 'kredit');


        // Ambil saldo debit
        $saldo_debit = $this->get_saldo_coa($coa_debit);
        // Ambil saldo kredit
        $saldo_kredit = $this->get_saldo_coa($coa_kredit);

        $dt_jurnal = [
            'tanggal' => $tanggal,
            'transaksi' => $tipe_transaksi,
            'akun_debit' => $coa_debit,
            'jumlah_debit' => $nominal,
            'akun_kredit' => $coa_kredit,
            'jumlah_kredit' => $nominal,
            'saldo_debit' => $saldo_debit,
            'saldo_kredit' => $saldo_kredit,
            'keterangan' => $keterangan,
            'created_by' => $this->session->userdata('nip'),
            'id_invoice' => ($id_invoice) ? $id_invoice : '',
            'nama_file' => $nama_data,
            'file' => $base64_data
        ];

        $this->tabungan_m->addJurnal($dt_jurnal);

        // $data_transaksi = [
        //   'user_id' => $this->session->userdata('nip'),
        //   'tgl_trs' => date('Y-m-d H:i:s'),
        //   'nominal' => $nominal,
        //   'debet' => $coa_debit,
        //   'kredit' => $coa_kredit,
        //   'keterangan' => trim($keterangan),
        //   'id_cabang' => $this->session->userdata('kode_cabang'),
        //   'id_company' => $this->session->userdata('user_perusahaan_id')
        // ];

        // $this->tabungan_m->add_transaksi($data_transaksi);
    }


    private function posting_detail_tabungan($no_tabungan, $nominal, $tanggal_transaksi, $keterangan, $tipe_transaksi)
    {
        $data_detail = [
            'no_tabungan' => $no_tabungan,
            'transaksi' => $tipe_transaksi,
            'nominal' => $nominal,
            'tgl_transaksi' => $tanggal_transaksi,
            'user_tr' => $this->session->userdata('username'),
            'ket' => trim($keterangan),
            'saldo' => null,
            'nosp' => 0,
            'sp' => 0,
            // 'id_cabang' => $this->session->userdata('kode_cabang'),
            // 'id_company' => $this->session->userdata('user_perusahaan_id')
        ];

        $this->tabungan_m->add_detail($data_detail);
    }


    private function update_tabungan($no_tabungan, $nominal)
    {
        // 1. Get current nominal
        $current_nominal = $this->tabungan_m->get_current_nominal($no_tabungan);

        // 2. Calculate the new nominal
        // Assuming the passed $nominal is the amount to be added (deposit)
        $new_nominal = $current_nominal + $nominal;

        // 3. Update the main t_tabungan table
        $this->tabungan_m->update_nominal($no_tabungan, $new_nominal);
    }

    public function process_transaksi_tabungan()
    {
        $keterangan = trim($this->input->post('input_keterangan'));
        $no_tabungan = $this->input->post('no_tabungan');
        $tipe_transaksi = $this->input->post('tipe_transaksi');

        // $tanggal_transaksi = $this->input->post('tanggal');
        // Set your desired timezone (e.g., 'Asia/Jakarta' for WIB)
        date_default_timezone_set('Asia/Jakarta');

        $tanggal_transaksi = date('Y-m-d H:i:s');

        $base64_data = null; // Initialize the variable to hold the Base64 string
        $file_name = null;   // <--- New variable to hold the file name

        // else {
        //   echo "Gak Masuk";
        //   exit();
        // }

        $this->db->trans_start(); // Mulai transaksi
        $id_invoice = NULL;

        if ($tipe_transaksi == "Setor") {
            $coa_debit  = $this->input->post('neraca_debit');
            $coa_kredit = $this->input->post('neraca_kredit');

            if ($coa_debit == $coa_kredit) {
                $this->session->set_flashdata('message_error', 'CoA Debit dan Kredit tidak boleh sama');
                redirect('tabungan/transaksi_simpanan');
            }

            // $nominal = preg_replace('/[^a-zA-Z0-9\']/', '', $this->input->post('input_nominal'));
            $nominal = $this->_parse_rupiah($this->input->post('input_nominal'));
            // $this->posting($coa_debit, $coa_kredit, $keterangan, $nominal, $tanggal_transaksi, $tipe_transaksi, $id_invoice, $base64_data, $file_name);

            $this->posting_detail_tabungan($no_tabungan, $nominal, $tanggal_transaksi, $keterangan, $tipe_transaksi);
            $this->update_tabungan($no_tabungan, $nominal);

            $this->db->trans_complete(); // Selesaikan transaksi

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('message_error', 'Transaksi gagal, silakan coba lagi.');
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('message_name', 'Transaksi berhasil.');
            }

            // redirect('tabungan/transaksi_simpanan');
        } else if ($tipe_transaksi == "Kredit") {
            $coa_debit  = $this->input->post('neraca_debit');
            $coa_kredit = $this->input->post('neraca_kredit');

            if ($coa_debit == $coa_kredit) {
                $this->session->set_flashdata('message_error', 'CoA Debit dan Kredit tidak boleh sama');
                redirect('tabungan/transaksi_simpanan');
            }

            // $nominal = preg_replace('/[^a-zA-Z0-9\']/', '', $this->input->post('input_nominal'));
            $nominal = $this->_parse_rupiah($this->input->post('input_nominal'));
            $this->posting($coa_debit, $coa_kredit, $keterangan, $nominal, $tanggal_transaksi, $id_invoice, $base64_data, $file_name);

            $this->posting_detail_tabungan($no_tabungan, $nominal, $tanggal_transaksi, $keterangan, $tipe_transaksi);
            $this->update_tabungan($no_tabungan, $nominal);

            $this->db->trans_complete(); // Selesaikan transaksi

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $this->session->set_flashdata('message_error', 'Transaksi gagal, silakan coba lagi.');
            } else {
                $this->db->trans_commit();
                $this->session->set_flashdata('message_name', 'Transaksi berhasil.');
            }

            // redirect('tabungan/transaksi_simpanan');
        }
        redirect('tabungan/transaksi_simpanan');
    }

    public function process_insert_excel_simpanan()
    {
        $this->load->library('upload');
        require APPPATH . 'third_party/autoload.php';
        require APPPATH . 'third_party/psr/simple-cache/src/CacheInterface.php';
        set_time_limit(300); // 300 seconds = 5 minutes
        $config['upload_path'] = FCPATH . 'uploads/simpanan';
        $config['allowed_types'] = 'xls|xlsx|csv';
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('file_excel')) {
            $error = $this->upload->display_errors();
            echo json_encode(['status' => false, 'message' => $error]);
            return;
        }

        $file_data = $this->upload->data();
        $file_path = $file_data['full_path'];

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
            $worksheet = $spreadsheet->getActiveSheet();

            $this->db->trans_begin(); // Start transaction

            $dataInsert = [];

            // === Start your ID generator ===
            $current_year = date('Y');
            $latest_entry = $this->tabungan_m->get_latest_entry($current_year);

            if ($latest_entry) {
                // Assuming $latest_entry->id is in the format YYYY##### (e.g., 202500005)
                // Extract the sequence number part (last 5 digits)
                $sequence_number_str = substr($latest_entry->id, -5);
                $latest_sequence_number = (int) $sequence_number_str;
            } else {
                // If no entry exists for the current year, start the sequence at 1
                $latest_sequence_number = 0;
            }
            // === End your ID generator ===

            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                // Skip header
                if ($rowIndex == 1 || $rowIndex == 2) continue;

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                $rowData = [];
                foreach ($cellIterator as $cell) {
                    // $rowData[] = $cell->getValue();
                    $rowData[] = $cell->getCalculatedValue(); // <-- Use this for formula results
                }

                // Generate next ID
                $latest_sequence_number++;
                $new_sequence_number_padded = str_pad($latest_sequence_number, 5, '0', STR_PAD_LEFT);
                $new_id = $current_year . $new_sequence_number_padded;
                // --- Corrected column mapping based on your template ---
                // A = No (Ignored)
                // B = Nomor Anggota -> rowData[1]
                // C = Nominal -> rowData[2]
                // D = Keterangan -> rowData[3]
                // E = Kode Tipe Simpanan -> rowData[4]
                // F = Tanggal Bayar -> rowData[5]
                // G = Sampai Dengan -> rowData[6]

                $no_tabungan = isset($rowData[1]) ? $rowData[1] : null;

                // echo $nomor_anggota;
                // echo $nomor_anggota;
                // Find id_anggota from database
                $tabungan = $this->db->get_where('t_tabungan', ['no_tabungan' => $no_tabungan])->row();

                // --- THE UPDATED LOGIC IS HERE ---
                $hasError = false;
                if (!$tabungan) {
                    // Rollback transaction immediately on error
                    $this->db->trans_rollback();
                    echo json_encode([
                        'status' => false,
                        'message' => 'Tabungan Tidak Di Temukan pada baris ' . $rowIndex . '.'
                    ]);
                    $hasError = true;
                    break; // Exit the loop
                }
                // --- END OF UPDATED LOGIC ---

                $id_tabungan = $tabungan->no_tabungan;

                $tipe_transaksi = isset($rowData[2]) ? $rowData[2] : null;

                if (isset($rowData[3])) {
                    $nominal = (float)str_replace(',', '', $rowData[3]);
                } else {
                    echo json_encode([
                        'status' => false,
                        'message' => 'Nominal Tidak Di Temukan pada baris ' . $rowIndex . '.'
                    ]);
                    $hasError = true;
                    break; // Exit the loop
                }
                if (isset($rowData[4])) {
                    $keterangan = strtoupper($rowData[4]);
                } else {
                    $keterangan = "IURAN BULAN " . strtoupper($bulan_nama) . " " . $tahun;

                    // echo json_encode([
                    //     'status' => false,
                    //     'message' => 'Keterangan Tidak Di Temukan pada baris ' . $rowIndex . '.'
                    // ]);
                    // $hasError = true;
                    // break; // Exit the loop
                }

                $column_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(6);

                // Get the cell object using the column letter and row index
                $cell = $worksheet->getCell($column_letter . $rowIndex);

                // Now, get the calculated value from the cell
                $tanggal_excel = isset($rowData[5]) ? $cell->getCalculatedValue() : null;

                // $tanggal_excel = isset($rowData[5]) ? $rowData[5] : null;

                $tanggal_bayar = null;
                if (is_numeric($tanggal_excel)) {
                    // Excel date serial to Y-m-d
                    $tanggal_bayar = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($tanggal_excel));
                } elseif (!empty($tanggal_excel)) {
                    // Already a valid date string
                    $tanggal_bayar = date('Y-m-d', strtotime($tanggal_excel));
                } else {
                    // If no date is found, you might want to set a default.
                    // For this example, let's set it to the current time.
                    // $tanggal_bayar = date('Y-m-d H:i:s');
                    echo json_encode([
                        'status' => false,
                        'message' => 'Tanggal Transaksi Tidak Di Temukan pada baris ' . $rowIndex . '.'
                    ]);
                    $hasError = true;
                    break; // Exit the loop
                }
                $date_for_keterangan_tanggal_bayar = new DateTime($tanggal_bayar);


                $column_letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(7);

                // Get the cell object using the column letter and row index
                $cell = $worksheet->getCell($column_letter . $rowIndex);

                // Now map Excel columns to database fields
                $dataInsert[] = [
                    'id'          => $new_id, // **USE GENERATED ID**
                    'no_tabungan'  => $id_tabungan,
                    'transaksi'  => $tipe_transaksi,
                    'nominal'     =>  $nominal,
                    'tgl_transaksi'   => $tanggal_bayar,
                    'user_tr' => $this->session->userdata('user_user_id'),
                    'ket'    => $keterangan,
                    // 'id_koperasi' => $this->session->userdata('id_koperasi'),
                ];
            }

            if (!$hasError) {
                if (!empty($dataInsert)) {
                    $this->db->insert_batch('t_detail_tabungan', $dataInsert); // Bulk insert

                    $updateAnggota = [];

                    foreach ($dataInsert as $item) {
                        $no_tabungan = $item['no_tabungan'];
                        // $this->db->select('no_cib');
                        // $this->db->where('no_tabungan', $no_tabungan);
                        // $tabungan = $this->db->get('t_tabungan')->row();
                        // $id_nasabah = $tabungan->no_cib;
                        // Hitung total nominal terbaru dari tabel iuran
                        $this->db->select_sum('nominal');
                        $this->db->where('no_tabungan', $no_tabungan);
                        $total = $this->db->get('t_detail_tabungan')->row();

                        $this->db->from('t_tabungan');
                        $this->db->where('no_tabungan', $no_tabungan);
                        $tabungan_now = $this->db->get()->row();

                        // $tanggal_simpanan_terakhir = $tabungan_now->tanggal_simpanan_terakhir;

                        // if ($item['sampai_dengan']  > $tanggal_simpanan_terakhir) {
                        //     $tanggal_simpanan_terakhir = $item['sampai_dengan'];
                        // }

                        $updateTabungan[] = [
                            'no_tabungan' => $no_tabungan,
                            'nominal' => $total->nominal ?? 0,
                            // 'tanggal_simpanan_terakhir' => $tanggal_simpanan_terakhir,
                        ];
                    }

                    if (!empty($updateTabungan)) {
                        $this->db->update_batch('t_tabungan', $updateTabungan, 'no_tabungan');
                    } else {
                        echo json_encode(['status' => false, 'message' => 'Gagal Update Iuran Koperasi']);
                    }
                }
            }

            if (!$hasError && $this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo json_encode(['status' => false, 'message' => 'Database error while inserting data']);
            } else if (!$hasError) {
                $this->db->trans_commit();
                echo json_encode(['status' => true, 'message' => 'Excel data inserted successfully']);
            }
        } catch (Exception $e) {
            $this->db->trans_rollback(); // Ensure rollback on any exception
            echo json_encode(['status' => false, 'message' => $e->getMessage()]);
        } finally {
            if (file_exists($file_path)) unlink($file_path);
        }
    }

    public function export_tabungan_by_date()
    {
        // Mengambil data dengan filter 'Belum Dibayar'
        $nasabah = $this->input->post('nasabah');

        // echo $nasabah;
        // echo $tanggal_dari;
        // echo $tanggal_sampai;
        $list = $this->tabungan_m->get_tabungan_data_for_export($nasabah);

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
        $sheet->setCellValue('C1', 'No Handphone');
        // $sheet->setCellValue('D1', 'Keterangan');
        $sheet->setCellValue('D1', 'Jumlah');

        // --- Initialize Total Variables ---
        $total_nominal = 0;
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
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        // --- END NEW HEADER STYLING SECTION ---
        // ------------------------------------------------------------------


        $row = 2;
        $no = 1;
        foreach ($list as $data) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data->nama);
            $sheet->setCellValue('C' . $row, $data->no_telp);
            $sheet->setCellValue('D' . $row, $data->nominal);

            $total_nominal += (float)$data->nominal;
            // ----------------------------------------------
            $row++;
        }
        // --- ADD TOTAL ROW SECTION ---
        $highestColumn = $sheet->getHighestColumn();

        // Define the row number for the total (one row after the last data row)
        $total_row = $row;

        // Merge the first three cells for the "Total" label
        $sheet->mergeCells('A' . $total_row . ':C' . $total_row);
        $sheet->setCellValue('A' . $total_row, 'TOTAL');

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
        header('Content-Disposition: attachment; filename="Export_Tabungan_' . date('Ymd_His') . '.xlsx"');
        header('Cache-Control: max-age=0');

        // OUTPUT FILE KE BROWSER
        // Baris ini adalah yang terpenting! Anda harus memanggilnya.
        $writer->save('php://output');

        // Hentikan eksekusi skrip setelah file selesai di-output
        exit();
    }

    public function export_detail_tabungan_by_date()
    {
        // Mengambil data dengan filter 'Belum Dibayar'
        $nasabah = $this->input->post('nasabah');
        $tanggal_dari = $this->input->post('tanggal_dari');
        $tanggal_sampai = $this->input->post('tanggal_sampai');

        // echo $nasabah;
        // echo $tanggal_dari;
        // echo $tanggal_sampai;
        $list = $this->tabungan_m->get_detail_tabungan_data_for_export($nasabah, $tanggal_dari, $tanggal_sampai);

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
        $sheet->setCellValue('C1', 'No Tabungan');
        $sheet->setCellValue('D1', 'Tipe Transaksi');
        $sheet->setCellValue('E1', 'Tanggal Transaksi');
        $sheet->setCellValue('F1', 'Keterangan');
        $sheet->setCellValue('G1', 'Nominal');

        // --- Initialize Total Variables ---
        $total_nominal = 0;
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
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

        // --- END NEW HEADER STYLING SECTION ---
        // ------------------------------------------------------------------


        $row = 2;
        $no = 1;
        foreach ($list as $data) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $data->nama);
            $sheet->setCellValue('C' . $row, $data->no_tabungan);
            $sheet->setCellValue('D' . $row, $data->transaksi);
            // Logika untuk menampilkan tanggal yang sudah diformat atau '-'
            if (empty($data->tgl_transaksi)) {
                $sheet->setCellValue('E' . $row, '-');
            } else {
                $sheet->setCellValue('E' . $row, date('d F Y H:i:s', strtotime($data->tgl_transaksi)));
            }
            $sheet->setCellValue('F' . $row, $data->ket);
            $sheet->setCellValue('G' . $row, $data->nominal);

            $total_nominal += (float)$data->nominal;
            // ----------------------------------------------
            $row++;
        }
        // --- ADD TOTAL ROW SECTION ---
        $highestColumn = $sheet->getHighestColumn();

        // Define the row number for the total (one row after the last data row)
        $total_row = $row;

        // Merge the first three cells for the "Total" label
        $sheet->mergeCells('A' . $total_row . ':F' . $total_row);
        $sheet->setCellValue('A' . $total_row, 'TOTAL');

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
        $sheet->setCellValue('G' . $total_row, $total_nominal);

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
        header('Content-Disposition: attachment; filename="Export_Detail_Tabungan_' . date('Ymd_His') . '.xlsx"');
        header('Cache-Control: max-age=0');

        // OUTPUT FILE KE BROWSER
        // Baris ini adalah yang terpenting! Anda harus memanggilnya.
        $writer->save('php://output');

        // Hentikan eksekusi skrip setelah file selesai di-output
        exit();
    }
}
