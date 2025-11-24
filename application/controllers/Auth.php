<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
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
	public function index()
	{

		if ($this->session->userdata('user_logged_in')) {
			redirect('dashboard');
		}
		$data['content'] = 'webview/login';
		// $data['content_js'] = 'webview/home/home_js';
		$this->load->view('parts/index/wrapper', $data);
	}

	public function process_login()
	{

		if ($this->session->userdata('user_logged_in')) {
			redirect('dashboard');
		}

		$this->load->model('Auth_m', 'login');

		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$active     = 1;

		$user = $this->login->user_login($username, $password);

		// var_dump($user);
		if (!empty($user)) {
			$this->session->set_userdata([
				'user_user_id'   => $user->no_cib,
				'name'  => $user->nama,
				'username'      => $user->username,
				// 'golongan'      => $user->golongan,
				// 'fasilitas'      => $user->fasilitas,
				// 'limit'      => $user->limit,
				// 'role'      => $role,
				'user_logged_in' => true
			]);
			// echo json_encode(array("status" => 'Success'));
			$this->session->set_flashdata('message_name', 'Anda Berhasil Login');

			redirect('dashboard');
			// if ($user->role_id == 1) {
			// 	echo json_encode(array("status" => 'admin'));
			// } else if ($user->role_id == 2) {
			// 	echo json_encode(array("status" => 'user'));
			// }
		} else {
			$this->session->set_flashdata('message_error', 'Gagal Login');

			redirect('auth');
			// echo json_encode(array("status" => 'Gagal Cari'));
		}
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect('auth');
	}
}
