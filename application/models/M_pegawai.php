<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_pegawai extends CI_Model
{
    public $table           = 'tbl_user';
    public $column_order    =  array(null, 'username', 'fullname', 'active', 'last_login', null); //set column field database untuk datatable order
    public $column_search   =  array('username', 'fullname', 'active', 'last_login'); //set column field database untuk datatable search
    public $order = array('id_user' => 'asc'); // default order

    public function __construct()
    {
        parent::__construct();
    }

    public function getAllData($table = null)
    {
        return $this->db->get($table);
    }

    public function getData($table = null, $where = null)
    {
        $this->db->from($table);
        $this->db->where($where);

        return $this->db->get();
    }

    public function save($table = null, $data = null)
    {
        return $this->db->insert($table, $data);
    }

    public function update($table = null, $data = null, $where = null)
    {
        return $this->db->update($table, $data, $where);
    }

    private function _get_datatables_query()
    {

        $this->db->from($this->table);
        $this->db->where(['level' => 'pegawai']);

        $i = 0;

        foreach ($this->column_search as $item) { // loop column
            if ($_POST['search']['value']) { // Jika datatable mengirim POST untuk search

                if ($i === 0) { // first loop
                    $this->db->group_start(); // open bracket.

                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) { //last loop
                    $this->db->group_end(); //close bracket
                }
            }
            $i++;
        }

        if (isset($_POST['order'])) { // Proses order

            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } elseif (isset($this->order)) {

            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();

        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
            $query = $this->db->get();

            return $query->result();
        }
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();

        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        $this->db->where(['level' => 'pegawai']);

        return $this->db->count_all_results();
    }
}
