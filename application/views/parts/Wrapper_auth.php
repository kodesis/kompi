<?php defined('BASEPATH') or exit('No direct script access allowed');
// $this->load->view('_parts/Header');
// $this->load->view('_parts/Sidebar');
// $this->load->view('_parts/TopNavbar');
$this->load->view($content);
// $this->load->view('layouts/_parts/nav_bottom');
// $this->load->view('_parts/Footer');
// $this->load->view('_parts/JS');
if (isset($content_js)) $this->load->view($content_js);
