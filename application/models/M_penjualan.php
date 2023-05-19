<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_penjualan extends CI_Model
{
    public $select = array('p.id_penjualan AS id_penjualan', 'tgl_penjualan', 'count(id_barang) AS jumlah_jenis_barang', 'SUM(qty * dp.harga) AS total', 'p.id_user AS id_user', 'fullname', 'nama_pembeli',  'GROUP_CONCAT(brg.nama_barang SEPARATOR ", ") AS nama_barang'); //data yang akan diambil

    public $table           = 'tbl_penjualan p
                            JOIN tbl_detail_penjualan dp ON(p.id_penjualan = dp.id_penjualan)
                            JOIN tbl_barang brg ON(dp.id_barang = brg.kode_barang)
                            JOIN tbl_user u ON(p.id_user = u.id_user)';

    public $column_order    =  array(null, 'p.id_penjualan', 'tgl_penjualan', 'nama_pembeli', 'jumlah_jenis_barang', 'nama_barang', 'total', 'fullname', null); //set column field database untuk datatable order
    public $column_search   =  array('p.id_penjualan', 'tgl_penjualan', 'nama_pembeli', 'fullname'); //set column field database untuk datatable search
    public $order = array('p.id_penjualan' => 'asc'); // default order

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

    public function getDataPenjualan($id)
    {
        $select = 'p.id_penjualan AS id_penjualan, tgl_penjualan, dp.harga AS harga, dp.qty AS qty, b.kode_barang AS kode_barang, nama_barang, brand, fullname, u.id_user AS id_user, nama_pembeli';

        $table = 'tbl_penjualan p
                    LEFT JOIN tbl_detail_penjualan dp ON(p.id_penjualan = dp.id_penjualan)
                    LEFT JOIN tbl_barang b ON(dp.id_barang = b.kode_barang)
                    LEFT JOIN tbl_user u ON(p.id_user = u.id_user)';

        $where = array('p.id_penjualan' => $id);

        $group = 'p.id_penjualan, tgl_penjualan, dp.harga, b.kode_barang, nama_barang, brand, fullname, u.id_user, nama_pembeli';

        $this->db->select($select);
        $this->db->from($table);
        $this->db->where($where);
        $this->db->group_by($group);

        return $this->db->get();
    }

    public function save($table = null, $data = null)
    {
        return $this->db->insert($table, $data);
    }

    public function multiSave($table = null, $data = array())
    {
        $jumlah = count($data);

        if ($jumlah > 0) {
            $this->db->insert_batch($table, $data);
        }
    }

    public function update($table = null, $data = null, $where = null)
    {
        return $this->db->update($table, $data, $where);
    }

    public function delete($table = null, $where = null)
    {
        $this->db->where($where);
        $this->db->delete($table);

        return $this->db->affected_rows();
    }

    private function _get_datatables_query()
    {

        $this->db->select($this->select);
        $this->db->from($this->table);
        $this->db->group_by(array('p.id_penjualan', 'tgl_penjualan', 'p.id_user', 'fullname', 'nama_pembeli'));

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
        $this->db->select($this->select);
        $this->db->from($this->table);
        $this->db->group_by(array('p.id_penjualan', 'tgl_penjualan', 'p.id_user', 'fullname', 'nama_pembeli'));

        return $this->db->count_all_results();
    }
}
