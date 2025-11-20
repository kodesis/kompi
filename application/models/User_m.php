<?php defined('BASEPATH') or exit('No direct script access allowed');

class User_m extends CI_Model
{

    var $table = 't_user';
    var $column_order = array('username', 'nama', 'golongan', 'fasilitas', 'limit'); //set column field database for datatable orderable
    var $column_search = array('username', 'nama', 'golongan', 'fasilitas', 'limit'); //set column field database for datatable searchable 
    var $order = array('username' => 'desc'); // default order 

    function _get_datatables_query()
    {

        $this->db->select('*');
        $this->db->from('t_user');
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

    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all()
    {

        $this->_get_datatables_query();
        $query = $this->db->get();

        return $this->db->count_all_results();
    }

    function get_golongan()
    {

        $this->db->select('*');
        $this->db->from('t_kategori');
        $query = $this->db->get();

        return $query->result();
    }

    function get_user($id)
    {

        $this->db->select('*');
        $this->db->from('t_user');
        $this->db->where('username', $id);
        $query = $this->db->get();

        return $query->row();
    }
}
