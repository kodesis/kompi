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
            $row[] = $cat->nominal;
            $row[] = $cat->spread_rate;
            $row[] = $cat->nominal_blokir;
            $row[] = $cat->pos_rate;
            $row[] = $cat->nolsp;

            $row[] = '<a class="btn btn-primary m-1" href="' . base_url('tabungan/detail_tabungan/' . $cat->no_tabungan) . '">Detail</a> <a class="btn btn-warning m-1" href="' . base_url('tabungan/edit/' . $cat->no_cib) . '">Edit</a> <a class="btn btn-danger m-1" 
   href="javascript:void(0)" 
   onclick="confirmDelete(' . $cat->no_cib . ')">
    Delete
</a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->tabungan_m->count_all(),
            "recordsFiltered" => $this->tabungan_m->count_filtered($nasabah),
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
            redirect('tabungan');
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

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->tabungan_m->count_all_detail($id),
            "recordsFiltered" => $this->tabungan_m->count_filtered_detail($id),
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
}
