<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
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
        $this->load->model('dashboard_m');

        if (!$this->session->userdata('user_logged_in')) {
            redirect('auth');
        }
    }

    public function index()
    {

        if (!$this->session->userdata('user_logged_in')) {
            redirect('auth');
        }

        if ($this->session->userdata('role') == '1') {

            $this->db->from('t_nasabah');
            $this->db->where('role', '2');
            $total_anggota = $this->db->get()->num_rows();
            $data['total_anggota'] = (float)$total_anggota;

            $this->db->select('sum(nominal) as nominal');
            $this->db->from('t_tabungan');
            $this->db->where('status_tabungan', 'Aktif');
            $total_tabungan = $this->db->get()->row();
            $data['total_tabungan'] = (float)$total_tabungan->nominal;

            $this->db->select('sum(nominal) as nominal');
            $this->db->from('t_kasbon');
            $this->db->where('status', '1');
            $total_kasbon = $this->db->get()->row();
            $data['total_kasbon'] = (float)$total_kasbon->nominal;
            // --- Revised Date Generation and Data Merging ---

            // ========================================
            // Tabungan Bulanan di Current Tahun
            // ========================================
            $monthly_tabungan_data = $this->dashboard_m->get_monthly_tabungan_summary_current_year();
            $current_month = (int)date('m');
            $all_months = [];
            $month_names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            for ($i = 0; $i < $current_month; $i++) {
                $all_months[$month_names[$i]] = 0; // Inisialisasi data ke 0
            }

            // 2. Isi data yang ada dari hasil query
            foreach ($monthly_tabungan_data as $row) {
                $all_months[$row['month_label']] = $row['total_nominal'];
            } // 3. Konversi ke format JSON untuk Chart.js
            $data['tabungan_bulanan_labels'] = json_encode(array_keys($all_months));
            $data['tabungan_bulanan_data'] = json_encode(array_values($all_months));


            // ========================================
            // Tabungan Tahunan
            // ========================================
            $data['yearly_tabungan'] = $this->dashboard_m->get_yearly_tabungan_summary();
            $data['chart_labels_yearly_tabungan'] = array_column($data['yearly_tabungan'], 'year');
            $data['chart_data_yearly_tabungan'] = array_column($data['yearly_tabungan'], 'total_nominal');



            // ========================================
            // Kasbon Bulanan di Current Tahun
            // ========================================
            // 1. Fetch the actual data from the database (Same query as before, but without the specific date WHERE clause)
            // Data diambil dari Model yang sudah diperbarui
            $kasbon_data_db = $this->dashboard_m->get_monthly_kredit_summary();

            // 1. Convert DB results into a lookup array (Key = Full Month Name-Year, Value = Nominal)
            $kasbon_map = [];
            foreach ($kasbon_data_db as $row) {
                $kasbon_map[$row['month_key']] = (float)$row['nominal'];
            }

            // 2. Generate the chronological template (January 1st to the current month)
            $labels = [];
            $data_values = [];

            // Tentukan periode waktu: Januari 1 tahun ini hingga hari pertama bulan ini
            $start_of_year = new DateTime(date('Y-01-01'));
            $end_of_period = new DateTime('first day of next month'); // Untuk memastikan loop mencakup bulan saat ini

            // Clone start_of_year untuk loop
            $current_month = clone $start_of_year;

            // Loop dari Januari tahun ini hingga bulan depan (sehingga mencakup bulan saat ini)
            while ($current_month < $end_of_period) {

                $label_display = $current_month->format('M'); // e.g., 'Dec'
                $month_key = $current_month->format('F-Y'); // e.g., 'December-2025' (Match DB format)

                // Check if we have data for this month (fill with 0 if missing)
                $nominal = isset($kasbon_map[$month_key]) ? $kasbon_map[$month_key] : 0;

                $labels[] = $label_display;
                $data_values[] = $nominal;

                // Advance to the next month for the next loop iteration
                $current_month->add(new DateInterval('P1M'));
            }

            // 3. Encode data for JavaScript
            $data['kasbon_labels'] = json_encode($labels);
            $data['kasbon_data'] = json_encode($data_values);
        } else if ($this->session->userdata('role') == '2') {

            $this->db->select('sum(nominal) as nominal');
            $this->db->from('t_tabungan');
            $this->db->where('status_tabungan', 'Aktif');
            $this->db->where('no_cib', $this->session->userdata('user_user_id'));
            $total_tabungan = $this->db->get()->row();
            $data['total_tabungan'] = (float)$total_tabungan->nominal;

            $this->db->select('sum(nominal) as nominal');
            $this->db->from('t_kasbon');
            $this->db->where('status', '1');
            $this->db->where('id_nasabah', $this->session->userdata('user_user_id'));
            $total_kasbon = $this->db->get()->row();
            $data['total_kasbon'] = (float)$total_kasbon->nominal;            // --- Revised Date Generation and Data Merging ---

            // ========================================
            // Tabungan Bulanan di Current Tahun
            // ========================================
            $monthly_tabungan_data = $this->dashboard_m->get_monthly_tabungan_summary_current_year();
            $current_month = (int)date('m');
            $all_months = [];
            $month_names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            for ($i = 0; $i < $current_month; $i++) {
                $all_months[$month_names[$i]] = 0; // Inisialisasi data ke 0
            }

            // 2. Isi data yang ada dari hasil query
            foreach ($monthly_tabungan_data as $row) {
                $all_months[$row['month_label']] = $row['total_nominal'];
            } // 3. Konversi ke format JSON untuk Chart.js
            $data['tabungan_bulanan_labels'] = json_encode(array_keys($all_months));
            $data['tabungan_bulanan_data'] = json_encode(array_values($all_months));


            // ========================================
            // Tabungan Tahunan
            // ========================================
            $data['yearly_tabungan'] = $this->dashboard_m->get_yearly_tabungan_summary();
            $data['chart_labels_yearly_tabungan'] = array_column($data['yearly_tabungan'], 'year');
            $data['chart_data_yearly_tabungan'] = array_column($data['yearly_tabungan'], 'total_nominal');



            // ========================================
            // Kasbon Bulanan di Current Tahun
            // ========================================
            // 1. Fetch the actual data from the database (Same query as before, but without the specific date WHERE clause)
            // Data diambil dari Model yang sudah diperbarui
            $kasbon_data_db = $this->dashboard_m->get_monthly_kredit_summary();

            // 1. Convert DB results into a lookup array (Key = Full Month Name-Year, Value = Nominal)
            $kasbon_map = [];
            foreach ($kasbon_data_db as $row) {
                $kasbon_map[$row['month_key']] = (float)$row['nominal'];
            }

            // 2. Generate the chronological template (January 1st to the current month)
            $labels = [];
            $data_values = [];

            // Tentukan periode waktu: Januari 1 tahun ini hingga hari pertama bulan ini
            $start_of_year = new DateTime(date('Y-01-01'));
            $end_of_period = new DateTime('first day of next month'); // Untuk memastikan loop mencakup bulan saat ini

            // Clone start_of_year untuk loop
            $current_month = clone $start_of_year;

            // Loop dari Januari tahun ini hingga bulan depan (sehingga mencakup bulan saat ini)
            while ($current_month < $end_of_period) {

                $label_display = $current_month->format('M'); // e.g., 'Dec'
                $month_key = $current_month->format('F-Y'); // e.g., 'December-2025' (Match DB format)

                // Check if we have data for this month (fill with 0 if missing)
                $nominal = isset($kasbon_map[$month_key]) ? $kasbon_map[$month_key] : 0;

                $labels[] = $label_display;
                $data_values[] = $nominal;

                // Advance to the next month for the next loop iteration
                $current_month->add(new DateInterval('P1M'));
            }

            // 3. Encode data for JavaScript
            $data['kasbon_labels'] = json_encode($labels);
            $data['kasbon_data'] = json_encode($data_values);
        }
        $data['content'] = 'webview/admin/dashboard';
        $data['content_js'] = 'webview/admin/dashboard_js';
        $this->load->view('parts/admin/Wrapper', $data);
    }
}
