<?php defined('BASEPATH') or exit('No direct script access allowed');

class Kasbon_m extends CI_Model
{

    var $table = 't_kasbon';
    var $column_order = array('id', 'nama', 'tanggal_jam', 'nominal', 'nominal_kredit', 'nominal_cash', 'status'); //set column field database for datatable orderable
    var $column_search = array('id', 'nama', 'tanggal_jam', 'nominal', 'nominal_kredit', 'nominal_cash', 'status'); //set column field database for datatable searchable 
    var $order = array('id' => 'desc'); // default order 

    function _get_datatables_query($nasabah = null)
    {

        $this->db->select('t_kasbon.*, t_nasabah.nama');
        $this->db->from('t_kasbon');
        $this->db->join('t_nasabah', 't_nasabah.no_cib = t_kasbon.id_nasabah');
        if ($this->session->userdata('role') == 2) {
            $this->db->where('id_nasabah', $this->session->userdata('user_user_id'));
        } else {
            if ($nasabah != 'ALL') {
                $this->db->where('t_kasbon.id_nasabah', $nasabah);
            }
        }
        $i = 0;

        foreach ($this->column_search as $item) // loop column 
        {
            if ($_POST['search']['value']) // if datatable send POST for search
            {

                if ($i === 0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            // $this->db->order_by(key($order), $order[key($order)]);
            foreach ($order as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
    }

    function get_datatables($nasabah)
    {
        $this->_get_datatables_query($nasabah);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered($nasabah)
    {
        $this->_get_datatables_query($nasabah);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all($nasabah)
    {

        $this->_get_datatables_query($nasabah);
        $query = $this->db->get();

        return $this->db->count_all_results();
    }
    public function get_filtered_nominal_sum($nasabah)
    {
        // 1. Run the base query setup, including JOINS, WHERE (user role), and the SEARCH filter.
        // NOTE: This intentionally excludes the LIMIT and ORDER BY clauses from DataTables.
        $this->_get_datatables_query($nasabah);

        // 2. Change the SELECT clause to calculate the SUM(nominal).
        // The previous _get_datatables_query($nasabah) had: $this->db->select('t_kasbon.*, t_nasabah.nama');
        // We override that SELECT with SUM before executing.
        $this->db->select('SUM(t_kasbon.nominal) as filtered_nominal_sum', FALSE);

        // 3. Execute the query
        // We use get()->row() because we only expect one result row containing the sum.
        $query = $this->db->get();
        $result = $query->row();

        // 4. Return the result, ensuring 0 is returned if the sum is NULL (i.e., no filtered records)
        return $result->filtered_nominal_sum ?? 0;
    }

    public function get_filtered_nominal_kredit_sum($nasabah)
    {
        // 1. Run the base query setup, including JOINS, WHERE (user role), and the SEARCH filter.
        // NOTE: This intentionally excludes the LIMIT and ORDER BY clauses from DataTables.
        $this->_get_datatables_query($nasabah);

        // 2. Change the SELECT clause to calculate the SUM(nominal).
        // The previous _get_datatables_query($nasabah) had: $this->db->select('t_kasbon.*, t_nasabah.nama');
        // We override that SELECT with SUM before executing.
        $this->db->select('SUM(t_kasbon.nominal_kredit) as filtered_nominal_sum', FALSE);

        // 3. Execute the query
        // We use get()->row() because we only expect one result row containing the sum.
        $query = $this->db->get();
        $result = $query->row();

        // 4. Return the result, ensuring 0 is returned if the sum is NULL (i.e., no filtered records)
        return $result->filtered_nominal_sum ?? 0;
    }

    public function get_filtered_nominal_cash_sum($nasabah)
    {
        // 1. Run the base query setup, including JOINS, WHERE (user role), and the SEARCH filter.
        // NOTE: This intentionally excludes the LIMIT and ORDER BY clauses from DataTables.
        $this->_get_datatables_query($nasabah);

        // 2. Change the SELECT clause to calculate the SUM(nominal).
        // The previous _get_datatables_query($nasabah) had: $this->db->select('t_kasbon.*, t_nasabah.nama');
        // We override that SELECT with SUM before executing.
        $this->db->select('SUM(t_kasbon.nominal_cash) as filtered_nominal_sum', FALSE);

        // 3. Execute the query
        // We use get()->row() because we only expect one result row containing the sum.
        $query = $this->db->get();
        $result = $query->row();

        // 4. Return the result, ensuring 0 is returned if the sum is NULL (i.e., no filtered records)
        return $result->filtered_nominal_sum ?? 0;
    }

    function get_segnasabah()
    {

        $this->db->select('*');
        $this->db->from('t_segnasabah');
        $query = $this->db->get();

        return $query->result();
    }

    function get_tipe()
    {

        $this->db->select('*');
        $this->db->from('t_tipenasabah');
        $query = $this->db->get();

        return $query->result();
    }

    function get_nasabah($id)
    {

        $this->db->select('*');
        $this->db->from('t_nasabah');
        $this->db->where('no_cib', $id);
        $query = $this->db->get();

        return $query->row();
    }
    function get_all_nasabah()
    {

        $this->db->select('*');
        $this->db->from('t_nasabah');
        $query = $this->db->get();

        return $query->result();
    }

    public function get_customer_credit_details($no_cib)
    {
        // Assuming 't_nasabah' is the table and 'no_cib' is the primary key/unique identifier
        // And 'kredit_limit' and 'kredit_usage' are columns in that table
        $this->db->select('kredit_limit, kredit_usage');
        $this->db->from('t_nasabah');
        $this->db->where('no_cib', $no_cib);
        $query = $this->db->get();

        // Return a single row object, or NULL if not found
        return $query->row();
    }

    public function check_token_exists($token)
    {
        $this->db->select('token');
        $this->db->from('t_kasbon');
        $this->db->where('token', $token);
        $query = $this->db->get();

        return $query->num_rows() > 0;
    }

    public function get_latest_entry($year)
    {
        $this->db->select('id');
        $this->db->from('t_kasbon'); // Change to your actual table name
        $this->db->where("RIGHT(id, 4) =", $year); // Fix the SQL syntax
        $this->db->order_by("id", "DESC");
        $this->db->limit(1);

        $query = $this->db->get();
        return $query->row(); // Return the latest row
    }

    public function save_file($data)
    {
        $this->db->insert('t_kasbon', $data);
        return $this->db->insert_id();
    }

    public function get_id_kasbon($id)
    {
        $this->db->select('*');
        $this->db->from('t_kasbon');
        $this->db->where('sub_id', $id);
        $query = $this->db->get();

        return $query->row();
    }

    public function get_kasbon_by_nasabah_and_token($token)
    {
        // SELECT * FROM t_kasbon 
        $this->db->from('t_kasbon');

        // WHERE id_nasabah = '$nasabah_id' 
        // Assuming the column name in t_kasbon is 'id_nasabah'

        // AND token = '$token'
        // Assuming the column name for the code is 'token'
        $this->db->where('token', $token);

        $this->db->limit(1); // Only need one result

        $query = $this->db->get();

        // Check if any row was returned
        if ($query->num_rows() > 0) {
            return $query->row(); // Return the single result object
        }

        return FALSE; // Return false if no match is found
    }
    public function update_data_kasbon($data, $where)
    {
        // Apply the WHERE clause (e.g., array('sub_id' => $kasbon_data->sub_id))
        $this->db->where($where);

        // Perform the update operation
        $this->db->update('t_kasbon', $data);

        // Returns TRUE/FALSE based on the query execution status
        return $this->db->affected_rows() > 0;
    }

    public function update_data_nasabah($data, $where)
    {
        // The $where array should look like: ['no_cib' => $customer_id]
        $this->db->where($where);

        // The $data array should look like: ['kredit_usage' => $new_usage_amount]
        $this->db->update('t_nasabah', $data);

        // CodeIgniter's update method returns TRUE/FALSE based on success
        // or you can check affected rows if needed:
        // return $this->db->affected_rows() > 0;

        return TRUE; // Simple return for successful query execution
    }

    public function get_kasbon_data_for_export($nasabah, $date_from, $date_to)
    {
        $this->db->select('t_kasbon.*, t_nasabah.nama');
        $this->db->from('t_kasbon');
        $this->db->join('t_nasabah', 't_nasabah.no_cib = t_kasbon.id_nasabah', 'LEFT');

        // Filter by Date Range
        $this->db->where('t_kasbon.tanggal_jam >=', $date_from);
        $this->db->where('t_kasbon.tanggal_jam <=', $date_to . ' 23:59:59');
        $this->db->where('t_kasbon.status', 1);

        // Filter by Nasabah (User)
        if ($nasabah != 'ALL') {
            $this->db->where('t_kasbon.id_nasabah', $nasabah);
        }

        $this->db->order_by('t_kasbon.tanggal_jam', 'ASC');

        return $this->db->get()->result();
    }
}
