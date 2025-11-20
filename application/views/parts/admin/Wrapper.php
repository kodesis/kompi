<?php defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('parts/admin/Header');
$this->load->view('parts/admin/Sidebar');
$this->load->view('parts/admin/TopNavbar');
$this->load->view($content);
// $this->load->view('layouts/_parts/nav_bottom');
$this->load->view('parts/admin/Footer');
$this->load->view('parts/admin/JS');
if (isset($content_js)) $this->load->view($content_js);
