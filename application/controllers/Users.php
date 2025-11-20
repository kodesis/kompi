<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
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
        $this->load->model('user_m');

        if (!$this->session->userdata('user_logged_in')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['content'] = 'webview/admin/users/users_table';
        $data['content_js'] = 'webview/admin/users/users_table_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function ajax_list()
    {
        $list = $this->user_m->get_datatables();
        $data = array();
        $no = $_POST['start'];


        foreach ($list as $cat) {

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $cat->username;
            $row[] = $cat->nama;
            $row[] = $cat->golongan;
            $row[] = $cat->fasilitas;
            $row[] = $cat->limit;
            // $row[] = '<a class="btn btn-warning m-1" href="' . base_url('users/edit/' . $cat->uid) . '">Edit</a> <a class="btn btn-danger m-1" href="' . base_url('users/delete/' . $cat->uid) . '">Delete</a>';
            $row[] = '<a class="btn btn-warning m-1" href="' . base_url('users/edit/' . $cat->username) . '">Edit</a> <a class="btn btn-danger m-1" 
   href="javascript:void(0)" 
   onclick="confirmDelete(' . $cat->username . ')">
    Delete
</a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->user_m->count_all(),
            "recordsFiltered" => $this->user_m->count_filtered(),
            "data" => $data,
        );
        echo json_encode($output);
    }

    public function add()
    {
        $data['golongan'] = $this->user_m->get_golongan();
        $data['form_data'] = $this->session->flashdata('form_data');

        $data['title'] = 'Add';
        $data['content'] = 'webview/admin/users/users_form';
        $data['content_js'] = 'webview/admin/users/users_form_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function edit($id)
    {
        $data['user'] = $this->user_m->get_user($id);
        $data['golongan'] = $this->user_m->get_golongan();

        $data['title'] = 'Edit';
        $data['content'] = 'webview/admin/users/users_form';
        $data['content_js'] = 'webview/admin/users/users_form_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }

    public function proccess_add()
    {
        // Assume input data is captured from POST. We collect this first 
        // to flash it back (old input) if validation fails.

        $data = [
            'username' => $_POST['username'] ?? '',
            'nama' => $_POST['nama'] ?? '',
            'password' => $_POST['password'] ?? '',
            'golongan' => $_POST['golongan'] ?? '',
            'fasilitas' => $_POST['fasilitas'] ?? '',
            'limit' => $_POST['limit'] ?? '',
        ];

        // --- SET VALIDATION RULES (Conceptual Framework Syntax) ---
        // In a real framework, you would typically load the validation library first.
        $this->form_validation->set_rules('username', 'Username', 'required|max_length[50]|is_unique[t_user.username]');
        $this->form_validation->set_rules('nama', 'Nama', 'required|max_length[100]');

        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]');
        $this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'trim|required|matches[password]');

        $this->form_validation->set_rules('golongan', 'Golongan', 'required');
        $this->form_validation->set_rules('fasilitas', 'Fasilitas', 'required');

        $this->form_validation->set_rules('limit', 'Limit', 'required|numeric');

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

            // Redirect back to the form view (users/add)
            redirect('users/add');
            // header('Location: ' . base_url('users/add'));
            // exit();
        } else {

            $this->db->insert('t_user', $data);
            // --- VALIDATION SUCCESS: Process the data ---

            // Example: $this->nasabah_model->save($data);
            $this->session->set_flashdata('message_name', 'User Berhasil di Tambahkan.');

            // Redirect to a success page
            redirect('users');
            exit();
        }
    }
    public function proccess_edit()
    {
        // Assume input data is captured from POST. We collect this first 
        // to flash it back (old input) if validation fails.
        $edit_data = [
            // 'username' => $_POST['username'] ?? '',
            'nama' => $_POST['nama'] ?? '',
            // 'password' => $_POST['password'] ?? '',
            'golongan' => $_POST['golongan'] ?? '',
            'fasilitas' => $_POST['fasilitas'] ?? '',
            'limit' => $_POST['limit'] ?? '',
        ];

        $password = $this->input->post('password');
        $password_confirmation = $this->input->post('password_confirmation');
        if (!empty($password) && $password == $password_confirmation) {
            // $edit_data['password'] = password_hash($password, PASSWORD_BCRYPT);
            $edit_data['password'] = $password;
        }


        $this->db->where('username', $this->input->post('username'));
        if ($this->db->update('t_user', $edit_data)) {

            $this->session->set_flashdata('message_name', 'User Berhasil di Ubah.');
            redirect('users');
        } else {

            $this->session->set_flashdata('message_error', 'User Gagal di Ubah.');
            redirect('users/edit/' . $this->input->post('username'));
        }
    }

    public function delete($id)
    {
        $this->db->where('username', $id);
        if ($this->db->delete('t_user')) {

            $this->session->set_flashdata('message_name', 'User Berhasil di Hapus.');
            redirect('users');
        } else {

            $this->session->set_flashdata('message_error', 'User Gagal di Hapus.');
            redirect('users');
        }

        // echo json_encode(array("status" => 'success', "message" => "Berhasil Menghapus Data"));

        // redirect('perusahaan/cabang');
    }
}
