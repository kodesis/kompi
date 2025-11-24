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
        $data['content'] = 'webview/admin/kasbon/kasbon_table';
        $data['content_js'] = 'webview/admin/kasbon/kasbon_table_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function ajax_list()
    {
        $list = $this->kasbon_m->get_datatables();
        $data = array();
        $no = $_POST['start'];


        foreach ($list as $cat) {

            $no++;
            $row = array();
            $row[] = $cat->id;
            // $row[] = $cat->no_cib;
            $row[] = $cat->nama;
            $row[] = $cat->tanggal_jam;
            $row[] = $cat->nominal;
            $row[] = $cat->nominal_kredit;
            $row[] = $cat->nominal_cash;

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->kasbon_m->count_all(),
            "recordsFiltered" => $this->kasbon_m->count_filtered(),
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
}
