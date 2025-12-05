<?php defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_m extends CI_Model
{
    public function get_monthly_tabungan_summary_current_year()
    {
        // Awal tahun (e.g., '2025-01-01')
        $start_date = date('Y') . '-01-01';
        // Akhir bulan ini
        $end_date = date('Y-m-t'); // t gives the last day of the current month

        // Pilih bulan (nama singkat) dan SUM nominal
        $this->db->select("DATE_FORMAT(tgl_transaksi, '%b') AS month_label, SUM(t_detail_tabungan.nominal) AS total_nominal", FALSE);
        $this->db->from('t_detail_tabungan');
        $this->db->join('t_tabungan', 't_tabungan.no_tabungan = t_detail_tabungan.no_tabungan');

        // Filter data dari awal tahun hingga akhir bulan ini
        $this->db->where("tgl_transaksi >=", $start_date);
        $this->db->where("tgl_transaksi <=", $end_date);

        if ($this->session->userdata('role') == 2) {
            $this->db->where('no_cib', $this->session->userdata('user_user_id'));
        }

        // Kelompokkan hasilnya berdasarkan tahun dan bulan
        $this->db->group_by("YEAR(tgl_transaksi), MONTH(tgl_transaksi)");
        $this->db->order_by("tgl_transaksi", "ASC");

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_yearly_tabungan_summary()
    {
        // Calculate the start year (5 years ago from the current year)
        $start_year = date('Y') - 4; // To include the current year, we only go back 4 full years (e.g., 2025 - 4 = 2021)

        // Select the year and the sum of 'nominal'
        $this->db->select("YEAR(tgl_transaksi) AS year, SUM(t_detail_tabungan.nominal) AS total_nominal", FALSE);
        $this->db->from('t_detail_tabungan');
        $this->db->join('t_tabungan', 't_tabungan.no_tabungan = t_detail_tabungan.no_tabungan');

        // Filter data to only include records from the start year up to the current year
        $this->db->where("YEAR(tgl_transaksi) >=", $start_year);
        if ($this->session->userdata('role') == 2) {
            $this->db->where('no_cib', $this->session->userdata('user_user_id'));
        }
        // Group the results by year
        $this->db->group_by("YEAR(tgl_transaksi)");

        // Order by year ascending
        $this->db->order_by("YEAR(tgl_transaksi)", "ASC");

        $query = $this->db->get();

        // Return the result array
        return $query->result_array();
    }


    public function get_monthly_kredit_summary()
    {
        // Awal tahun (e.g., '2025-01-01')
        $start_of_year = date('Y-01-01');

        $this->db->select('
        SUM(nominal) AS nominal, 
        DATE_FORMAT(tanggal_jam, "%M-%Y") AS month_key
    ');
        $this->db->from('t_kasbon');
        $this->db->where('status', '1');
        if ($this->session->userdata('role') == 2) {
            $this->db->where('id_nasabah', $this->session->userdata('user_user_id'));
        }

        // 🎯 PERUBAHAN UTAMA: Filter dimulai dari Januari 1 tahun ini
        $this->db->where('tanggal_jam >=', $start_of_year);

        $this->db->group_by('month_key');
        return $this->db->get()->result_array();
    }
}
