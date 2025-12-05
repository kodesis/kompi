<?php defined('BASEPATH') or exit('No direct script access allowed');

class Tabungan_m extends CI_Model
{

    var $table = 't_tabungan';
    var $column_order = array('no_tabungan', 'nama', 'nama_tabungan', 'status_tabungan', 'no_urut', 'nominal', 'spread_rate', 'nominal_blokir', 'pos_rate', 'nolsp'); //set column field database for datatable orderable
    var $column_search = array('no_tabungan', 'nama', 'nama_tabungan', 'status_tabungan', 'no_urut', 'nominal', 'spread_rate', 'nominal_blokir', 'pos_rate', 'nolsp'); //set column field database for datatable searchable 
    var $order = array('no_urut' => 'desc'); // default order 

    function _get_datatables_query($nasabah = null)
    {

        $this->db->select('t_tabungan.*, t_nasabah.nama, t_jenistabungan.nama_tabungan');
        $this->db->from('t_tabungan');
        $this->db->join('t_nasabah', 't_tabungan.no_cib = t_nasabah.no_cib');
        $this->db->join('t_jenistabungan', 't_tabungan.jenis_tabungan = t_jenistabungan.kode_tabungan');
        if ($nasabah == null) {
            $this->db->where('t_tabungan.no_cib', $nasabah);
        }

        if ($this->session->userdata('role') == 2) {
            $this->db->where('t_tabungan.no_cib', $this->session->userdata('user_user_id'));
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

    function count_all()
    {

        $this->_get_datatables_query();
        $query = $this->db->get();

        return $this->db->count_all_results();
    }

    function get_nasabah()
    {

        $this->db->select('*');
        $this->db->from('t_nasabah');
        $query = $this->db->get();

        return $query->result();
    }

    function get_jenis_tabungan()
    {

        $this->db->select('*');
        $this->db->from('t_jenistabungan');
        $query = $this->db->get();

        return $query->result();
    }

    public function generate_next_no_tabungan()
    {
        $this->db->trans_start();

        // 1. Get the maximum existing number (PURELY NUMERIC)
        $sql = "
        SELECT 
            MAX(no_tabungan) as latest_number -- Find MAX directly
        FROM 
            t_tabungan
        FOR UPDATE
    ";

        // Execute the query
        $query = $this->db->query($sql);
        $row = $query->row();

        // 2. Calculate the new number
        // Handle NULL or 0 from an empty table
        $latest_num = (int)$row->latest_number;

        if ($latest_num === 0) {
            $new_num = 1;
        } else {
            $new_num = $latest_num + 1; // Now this should correctly increment
        }

        // 3. Set the new no_tabungan
        $new_no_tabungan = $new_num;

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return NULL;
        }

        return $new_no_tabungan;
    }

    public function generate_next_no_urut()
    {
        // Start the transaction
        $this->db->trans_start();

        // 1. Get the maximum existing number (using lock for safety)

        // NOTE: Adjust the '4' if your number starts at a different position (e.g., 'TB-001' -> number starts at position 4)
        $num_start_position = 4;

        $sql = "
            SELECT 
                MAX(CAST(SUBSTRING(no_tabungan, ?) AS UNSIGNED)) as latest_number
            FROM 
                t_tabungan
            FOR UPDATE  -- This locks the table row/index until the transaction is complete
        ";

        // Use query binding to prevent SQL injection for the position number
        $query = $this->db->query($sql, array($num_start_position));
        $row = $query->row();

        // 2. Calculate the new number
        $latest_num = (int)$row->latest_number; // Cast to integer for safety

        if ($latest_num === 0) {
            $new_num = 1; // Start at 1 if no records exist
        } else {
            $new_num = $latest_num + 1; // Increment
        }

        // 3. Format the new no_tabungan
        $prefix = ''; // Adjust your prefix here
        $num_length = 5; // Adjust the desired padding length (e.g., 001, 010, 100)

        $new_no_tabungan = $prefix . str_pad($new_num, $num_length, '0', STR_PAD_LEFT);

        // Commit the transaction (this releases the lock)
        $this->db->trans_complete();

        // Check if the transaction was successful
        if ($this->db->trans_status() === FALSE) {
            // Handle error, maybe return NULL or throw an exception
            return NULL;
        }

        return $new_no_tabungan;
    }

    function get_tabungan($id)
    {

        $this->db->select('*');
        $this->db->from('t_tabungan');
        $this->db->where('no_cib', $id);
        $query = $this->db->get();

        return $query->row();
    }


    function get_all_tabungan()
    {

        $this->db->select('t_tabungan.*, t_nasabah.nama, t_jenistabungan.nama_tabungan as nama_jenis_tabungan');
        $this->db->from('t_tabungan');
        $this->db->join('t_nasabah', 't_tabungan.no_cib = t_nasabah.no_cib');
        $this->db->join('t_jenistabungan', 't_tabungan.jenis_tabungan = t_jenistabungan.kode_tabungan');
        $query = $this->db->get();

        return $query->result();
    }


    public function list_coa_kepala_1()
    {
        return $this->db->like('no_sbb', '1', 'after')->order_by('no_sbb', 'ASC')->get('v_coa_all')->result();
    }

    public function list_coa_kepala_2()
    {
        return $this->db->like('no_sbb', '2', 'after')->order_by('no_sbb', 'ASC')->get('v_coa_all')->result();
    }

    public function add_detail($data)
    {
        return $this->db->insert('t_detail_tabungan', $data);
    }

    public function get_current_nominal($no_tabungan)
    {
        $this->db->select('nominal');
        $this->db->where('no_tabungan', $no_tabungan);
        $query = $this->db->get('t_tabungan');

        if ($query->num_rows() > 0) {
            return $query->row()->nominal;
        }
        return 0; // Return 0 if the account isn't found
    }

    public function update_nominal($no_tabungan, $new_nominal)
    {
        $data = [
            'nominal' => $new_nominal
        ];
        $this->db->where('no_tabungan', $no_tabungan);
        return $this->db->update('t_tabungan', $data);
    }

    var $table_detail = 't_detail_tabungan';
    var $column_order_detail = array('id', 'no_tabungan', 'transaksi', 'nominal', 'ket', 'tgl_transaksi', 'nama'); //set column field database for datatable orderable
    var $column_search_detail = array('id', 'no_tabungan', 'transaksi', 'nominal', 'ket', 'tgl_transaksi', 'nama'); //set column field database for datatable searchable 
    var $order_detail = array('tgl_transaksi' => 'desc'); // default order 

    function _get_datatables_query_detail($id)
    {

        $this->db->select('t_detail_tabungan.*, t_user.nama');
        $this->db->from('t_detail_tabungan');
        // $this->db->join($this->db->database . '.users', 't_detail_tabungan.user_tr = users.nip');
        $this->db->join('t_user', 't_detail_tabungan.user_tr = t_user.username');
        $this->db->where('t_detail_tabungan.no_tabungan', $id);
        // $this->db->where('t_tabungan.id_perusahaan', $this->session->userdata('user_perusahaan_id'));
        $i = 0;

        foreach ($this->column_search_detail as $item) // loop column 
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

                if (count($this->column_search_detail) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order_detail[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order_detail)) {
            $order = $this->order_detail;
            // $this->db->order_by(key($order), $order[key($order)]);
            foreach ($order as $key => $value) {
                $this->db->order_by($key, $value);
            }
        }
    }

    function get_datatables_detail($id)
    {
        $this->_get_datatables_query_detail($id);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered_detail($id)
    {
        $this->_get_datatables_query_detail($id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all_detail($id)
    {

        $this->_get_datatables_query_detail($id);
        $query = $this->db->get();

        return $this->db->count_all_results();
    }

    public function add_transaksi($data)
    {
        return $this->db->insert('t_log_transaksi', $data);
    }

    public function addJurnal($data)
    {
        return $this->db->insert('jurnal_tabungan', $data);
    }

    public function get_latest_entry($year)
    {
        $this->db->select('id');
        $this->db->from('t_detail_tabungan');

        // 🏆 Best Approach: Use LIKE (allows index use for a leading match)
        // This finds IDs starting with the year, e.g., '2025%'
        $this->db->like('id', $year, 'after');

        // OR, slightly worse performance but works:
        // $this->db->where("LEFT(id, 4) =", $year); 

        $this->db->order_by("id", "DESC");
        $this->db->limit(1);

        $query = $this->db->get();
        return $query->row();
    }
}
