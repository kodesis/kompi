<?php defined('BASEPATH') or exit('No direct script access allowed');

class Nasabah_m extends CI_Model
{

    var $table = 't_nasabah';
    var $column_order = array('no_cib', 'nama', 'alamat', 'no_ktp', 'no_telp', 'ahli_waris', 'kode_pos', 'nama_ibu_kandung', 'pekerjaan', 'kode_ao', 'nama_panggilan', 'tgl_lahir', 'tempat_lahir', 'kota', 'tgl_pendaftaran', 'tipe_nasabah', 'nama_segmen', 'warga_negara'); //set column field database for datatable orderable
    var $column_search = array('no_cib', 'nama', 'alamat', 'no_ktp', 'no_telp', 'ahli_waris', 'kode_pos', 'nama_ibu_kandung', 'pekerjaan', 'kode_ao', 'nama_panggilan', 'tgl_lahir', 'tempat_lahir', 'kota', 'tgl_pendaftaran', 'tipe_nasabah', 'nama_segmen', 'warga_negara'); //set column field database for datatable searchable 
    var $order = array('no_cib' => 'desc'); // default order 

    function _get_datatables_query()
    {

        $this->db->select('*');
        $this->db->from('t_nasabah');
        $this->db->join('t_segnasabah', 't_nasabah.segmen_nasabah = t_segnasabah.kode_segmen');
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
}
