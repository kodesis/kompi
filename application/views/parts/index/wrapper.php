<?php defined('BASEPATH') or exit('No direct script access allowed');
$this->load->view('parts/index/header');
$this->load->view('parts/index/navbar');
$this->load->view($content);
$this->load->view('parts/index/footer');
$this->load->view('parts/index/js');
if (isset($content_js)) $this->load->view($content_js);
