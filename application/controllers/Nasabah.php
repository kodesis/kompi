<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Nasabah extends CI_Controller
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
        $this->load->model('nasabah_m');

        if (!$this->session->userdata('user_logged_in')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['content'] = 'webview/admin/nasabah/nasabah_table';
        $data['content_js'] = 'webview/admin/nasabah/nasabah_table_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function ajax_list()
    {
        $list = $this->nasabah_m->get_datatables();
        $data = array();
        $no = $_POST['start'];


        foreach ($list as $cat) {

            $no++;
            $row = array();
            $row[] = $no;
            // $row[] = $cat->no_cib;
            $row[] = $cat->nama;
            $row[] = $cat->alamat;
            $row[] = $cat->no_ktp;
            $row[] = $cat->no_telp;
            $row[] = $cat->ahli_waris;
            $row[] = $cat->kode_pos;
            $row[] = $cat->nama_ibu_kandung;
            $row[] = $cat->pekerjaan;
            $row[] = $cat->kode_ao;
            $row[] = $cat->nama_panggilan;
            $row[] = $cat->tgl_lahir;
            $row[] = $cat->tempat_lahir;
            $row[] = $cat->kota;
            $row[] = $cat->tgl_pendaftaran;
            $row[] = $cat->tipe_nasabah;
            $row[] = $cat->nama_segmen;
            $row[] = $cat->warga_negara;

            $delete_url = base_url('nasabah/delete/' . $cat->no_cib);

            $row[] = '<a class="btn btn-warning m-1" href="' . base_url('nasabah/edit/' . $cat->no_cib) . '">Edit</a> <a class="btn btn-danger m-1" 
   href="javascript:void(0)" 
   onclick="confirmDelete(' . $cat->no_cib . ')">
    Delete
</a>';

            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->nasabah_m->count_all(),
            "recordsFiltered" => $this->nasabah_m->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add()
    {
        $data['segnasabah'] = $this->nasabah_m->get_segnasabah();
        $data['tipe'] = $this->nasabah_m->get_tipe();
        $data['form_data'] = $this->session->flashdata('form_data');

        $data['title'] = 'Add';
        $data['content'] = 'webview/admin/nasabah/nasabah_form';
        $data['content_js'] = 'webview/admin/nasabah/nasabah_form_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function edit($id)
    {
        $data['nasabah'] = $this->nasabah_m->get_nasabah($id);
        $data['segnasabah'] = $this->nasabah_m->get_segnasabah();
        $data['tipe'] = $this->nasabah_m->get_tipe();

        $data['title'] = 'Edit';
        $data['content'] = 'webview/admin/nasabah/nasabah_form';
        $data['content_js'] = 'webview/admin/nasabah/nasabah_form_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function proccess_add()
    {
        // Assume input data is captured from POST. We collect this first 
        // to flash it back (old input) if validation fails.
        $plain_password = $_POST['password'] ?? '';

        // 2. Check if the password is not empty before attempting to hash it
        $hashed_password = '';
        if (!empty($plain_password)) {
            // 3. Hash the password securely using the PASSWORD_DEFAULT algorithm (recommended)
            $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
        }


        $data = [
            'username' => $_POST['username'] ?? '',
            'password' => $hashed_password,
            'nama' => $_POST['nama'] ?? '',
            'alamat' => $_POST['alamat'] ?? '',
            'no_ktp' => $_POST['no_ktp'] ?? '',
            'no_telp' => $_POST['no_telp'] ?? '',
            'ahli_waris' => $_POST['ahli_waris'] ?? '',
            'kode_pos' => $_POST['kode_pos'] ?? '',
            'nama_ibu_kandung' => $_POST['nama_ibu_kandung'] ?? '',
            'pekerjaan' => $_POST['pekerjaan'] ?? '',
            'kode_ao' => $_POST['kode_ao'] ?? '',
            'nama_panggilan' => $_POST['nama_panggilan'] ?? '',
            'tgl_lahir' => $_POST['tgl_lahir'] ?? '',
            'tempat_lahir' => $_POST['tempat_lahir'] ?? '',
            'kota' => $_POST['kota'] ?? '',
            'tgl_pendaftaran' => $_POST['tgl_pendaftaran'] ?? '',
            'tipe_nasabah' => $_POST['tipe_nasabah'] ?? '',
            'segmen_nasabah' => $_POST['segmen_nasabah'] ?? '',
            'warga_negara' => $_POST['warga_negara'] ?? '',
            'kredit_limit' => $_POST['kredit_limit'] ?? 0,
            'role' => $_POST['role'] ?? '2',
        ];

        // --- SET VALIDATION RULES (Conceptual Framework Syntax) ---
        // In a real framework, you would typically load the validation library first.
        $this->form_validation->set_rules('username', 'Username', 'required|max_length[100]|is_unique[t_nasabah.username]');
        $this->form_validation->set_rules(
            'password',
            'Password',
            'required|min_length[8]|max_length[100]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/]'
        );
        $this->form_validation->set_rules('nama', 'Nama', 'required|max_length[100]');
        // $this->form_validation->set_rules('alamat', 'Alamat', 'required');
        $this->form_validation->set_rules('no_ktp', 'Nomor KTP', 'required|numeric|exact_length[16]'); // Assuming 16 digits
        $this->form_validation->set_rules('no_telp', 'No. Telp', 'numeric|max_length[15]');
        $this->form_validation->set_rules('kode_ao', 'Kode AO', 'required');
        // $this->form_validation->set_rules('tgl_pendaftaran', 'Tgl Pendaftaran', 'required|valid_date');
        $this->form_validation->set_rules('tipe_nasabah', 'Tipe Nasabah', 'required');
        $this->form_validation->set_rules('segmen_nasabah', 'Segmen Nasabah', 'required');

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

            // Redirect back to the form view (nasabah/add)
            redirect('nasabah/add');
            // header('Location: ' . base_url('nasabah/add'));
            // exit();
        } else {

            $this->db->insert('t_nasabah', $data);
            // --- VALIDATION SUCCESS: Process the data ---

            // Example: $this->nasabah_model->save($data);
            $this->session->set_flashdata('message_name', 'Nasabah Berhasil di Tambahkan.');

            // Redirect to a success page
            redirect('nasabah');
            exit();
        }
    }
    public function proccess_edit()
    {
        // Assume input data is captured from POST. We collect this first 
        // to flash it back (old input) if validation fails.
        $edit_data = [
            'username' => $_POST['username'],
            'nama' => $_POST['nama'],
            'alamat' => $_POST['alamat'],
            'no_ktp' => $_POST['no_ktp'],
            'no_telp' => $_POST['no_telp'],
            'ahli_waris' => $_POST['ahli_waris'],
            'kode_pos' => $_POST['kode_pos'],
            'nama_ibu_kandung' => $_POST['nama_ibu_kandung'],
            'pekerjaan' => $_POST['pekerjaan'],
            'kode_ao' => $_POST['kode_ao'],
            'nama_panggilan' => $_POST['nama_panggilan'],
            'tgl_lahir' => $_POST['tgl_lahir'],
            'tempat_lahir' => $_POST['tempat_lahir'],
            'kota' => $_POST['kota'],
            'tgl_pendaftaran' => $_POST['tgl_pendaftaran'],
            'tipe_nasabah' => $_POST['tipe_nasabah'],
            'segmen_nasabah' => $_POST['segmen_nasabah'],
            'warga_negara' => $_POST['warga_negara'],
            'kredit_limit' => $_POST['kredit_limit'],
            'role' => $_POST['role'],
        ];

        if (isset($_POST['password']) && !empty($_POST['password'])) {

            // 2. Hash the new password securely
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            // 3. Append the HASHED password to the $edit_data array
            $edit_data['password'] = $hashed_password;
        }

        $this->db->where('no_cib', $this->input->post('no_cib'));
        if ($this->db->update('t_nasabah', $edit_data)) {

            $this->session->set_flashdata('message_name', 'Nasabah Berhasil di Ubah.');
            redirect('nasabah');
        } else {

            $this->session->set_flashdata('message_error', 'Nasabah Gagal di Ubah.');
            redirect('nasabah/edit/' . $this->input->post('no_cib'));
        }
    }

    public function delete($id)
    {
        $this->db->where('no_cib', $id);
        if ($this->db->delete('t_nasabah')) {

            $this->session->set_flashdata('message_name', 'Nasabah Berhasil di Hapus.');
            redirect('nasabah');
        } else {

            $this->session->set_flashdata('message_error', 'Nasabah Gagal di Hapus.');
            redirect('nasabah');
        }

        // echo json_encode(array("status" => 'success', "message" => "Berhasil Menghapus Data"));

        // redirect('perusahaan/cabang');
    }
}
