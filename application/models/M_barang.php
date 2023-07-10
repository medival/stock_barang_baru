<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_barang extends CI_Model
{

    var $table           = 'tbl_barang';
    var $table_supplier  = 'tbl_supplier';
    var $column_order    =  array(null, 'kode_barang', 'nama_barang', 'brand', 'stok', 'harga', 'active', null); //set column field database untuk datatable order
    var $column_search   =  array('kode_barang', 'nama_barang', 'brand', 'stok', 'harga', 'active'); //set column field database untuk datatable search
    var $order = array('kode_barang' => 'asc'); // default order

    function __construct()
    {
        parent::__construct();
    }

    function getAllData($table = null)
    {
        return $this->db->get($table);
    }

    function getData($table = null, $where = null)
    {
        $this->db->from($table);
        $this->db->where($where);

        return $this->db->get();
    }

    function save($table = null, $data = null)
    {
        return $this->db->insert($table, $data);
    }

    function update($table = null, $data = null, $where = null)
    {
        return $this->db->update($table, $data, $where);
    }

    private function _get_datatables_query()
    {

        $this->db->select('tbl_barang.id_supplier, nama_barang, kode_barang, nama_barang, stok, brand, harga, active');
        $this->db->from('tbl_barang');
        $this->db->join('tbl_supplier', 'tbl_barang.id_supplier = tbl_supplier.id_supplier', 'left');
        $this->db->where('tbl_barang.id_supplier = tbl_supplier.id_supplier');

        $i = 0;

        foreach ($this->column_search as $item) // loop column
        {
            if ($_POST['search']['value']) // Jika datatable mengirim POST untuk search
            {

                if ($i === 0) // first loop
                {
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

        if (isset($_POST['order'])) // Proses order
        {

            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {

            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();


        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
            $query = $this->db->get();

            return $query->result();
        }
    }

    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();

        return $query->num_rows();
    }

    function count_all()
    {
        $this->db->select('tbl_barang.id_supplier, nama_barang, kode_barang, nama_barang, brand, harga, active');
        $this->db->from('tbl_barang');
        $this->db->join('tbl_supplier', 'tbl_barang.id_supplier = tbl_supplier.id_supplier', 'left');
        $this->db->where('tbl_barang.id_supplier = tbl_supplier.id_supplier');

        return $this->db->count_all_results();
    }

    function getSpecificSupplier($kode_barang)
    {
        $where = "kode_barang = '{$kode_barang}'";
        $this->db->select('id_supplier');
        $this->db->from('tbl_barang');
        $this->db->where($where);

        $result = $this->db->get();

        if ($result->num_rows() > 0) {
            $row = $result->row();
            $id_supplier = $row->id_supplier;
            return $id_supplier;
        } else {
            echo "No data found.";
        }
    }

    function getItemFromSupplier($id_supplier)
    {
        $where = "id_supplier = '{$id_supplier}'";
        $this->db->select('tbl_barang.kode_barang, tbl_barang.id_supplier, tbl_barang.nama_barang, tbl_barang.brand, tbl_barang.stok, tbl_barang.harga, tbl_barang.active');
        $this->db->from('tbl_barang');
        $this->db->where($where);

        $result = $this->db->get();

        if ($result->num_rows() > 0) {
            return $result->result();
        } else {
            return false;
        }
    }
}
