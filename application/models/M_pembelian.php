<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_pembelian extends CI_Model
{

    var $select = array('p.id_pembelian AS id_pembelian', 'tgl_pembelian', 'count(id_barang) AS jumlah', 'SUM(qty * dp.harga) AS total', 'p.id_user AS id_user', 'fullname', 's.nama_supplier AS nama_supplier', 'GROUP_CONCAT(brg.nama_barang SEPARATOR ", ") AS nama_barang'); //data yang akan diambil

    var $table           = 'tbl_pembelian p
                            JOIN tbl_detail_pembelian dp ON (p.id_pembelian = dp.id_pembelian)
                            JOIN tbl_user u ON (p.id_user = u.id_user)
                            JOIN tbl_barang brg ON (dp.id_barang = brg.kode_barang)
                            LEFT JOIN tbl_supplier s ON (s.id_supplier = brg.id_supplier)';

    var $column_order    =  array(null, 'p.id_pembelian', 'tgl_pembelian', 'jumlah', 's.nama_supplier',  'total', 'fullname', null); //set column field database untuk datatable order
    var $column_search   = array('p.id_pembelian', 'nama_barang', 'fullname', 's.nama_supplier', 'tgl_pembelian'); //set column field database untuk datatable search

    var $order = array('p.id_pembelian' => 'asc'); // default order

        

    function __construct()
    {
        parent::__construct();
    }

    function getAllData($table = null)
    {
        return $this->db->get($table);
    }

    // filter active and has with correct supplier
    function getAllDataBarangFromDistributorAndActive($table = null)
    {
        $this->db->select('kode_barang, nama_barang, brand, tbl_barang.id_supplier, tbl_supplier.nama_supplier, stok, harga, active');
        $this->db->from($table);
        $this->db->join('tbl_supplier', 'tbl_barang.id_supplier = tbl_supplier.id_supplier', 'left');
        $this->db->where('tbl_barang.id_supplier = tbl_supplier.id_supplier AND active = "Y"');

        return $this->db->get();
    }

    // display if count > 0
    function getAllDataSupplier() 
    {
        $select = 'sp.id_supplier, sp.nama_supplier, sp.alamat, sp.telp, COUNT(b.id_supplier) as jml_item';

        $table = 'tbl_supplier sp
                    LEFT JOIN tbl_barang b ON(sp.id_supplier = b.id_supplier)
                    GROUP BY sp.id_supplier, sp.nama_supplier';

        $this->db->select($select);
        $this->db->from($table);

        return $this->db->get();
        
    }

    function getData($table = null, $where = null)
    {
        $this->db->from($table);
        $this->db->where($where);

        return $this->db->get();
    }

    function getDataPembelian($id)
    {
        $select = 'p.id_pembelian AS id_pembelian, tgl_pembelian, qty, dp.harga AS harga, kode_barang, nama_barang, brand, fullname, u.id_user AS id_user, nama_supplier, b.id_supplier AS id_supplier';

        // $table = 'tbl_pembelian p
        //             LEFT JOIN tbl_detail_pembelian dp ON(p.id_pembelian = dp.id_pembelian)
        //             LEFT JOIN tbl_barang b ON(dp.id_barang = b.kode_barang)
        //             LEFT JOIN tbl_user u ON(p.id_user = u.id_user)
        //             LEFT JOIN tbl_supplier s ON(p.id_supplier = s.id_supplier)';

        $table = 'tbl_pembelian p
                    LEFT JOIN tbl_detail_pembelian dp ON(p.id_pembelian = dp.id_pembelian)
                    LEFT JOIN tbl_barang b ON(dp.id_barang = b.kode_barang)
                    LEFT JOIN tbl_user u ON(p.id_user = u.id_user)
                    LEFT JOIN tbl_supplier s ON(s.id_supplier = b.id_supplier)';

        $where = array('p.id_pembelian' => $id);

        $this->db->select($select);
        $this->db->from($table);
        $this->db->where($where);

        return $this->db->get();
    }

    function save($table = null, $data = null)
    {
        return $this->db->insert($table, $data);
    }

    function multiSave($table = null, $data = array())
    {
        $jumlah = count($data);

        if ($jumlah > 0) {
            $this->db->insert_batch($table, $data);
        }
    }

    function update($table = null, $data = null, $where = null)
    {
        return $this->db->update($table, $data, $where);
    }

    function delete($table = null, $where = null)
    {
        $this->db->where($where);
        $this->db->delete($table);

        return $this->db->affected_rows();
    }

    private function _get_datatables_query()
    {

        $this->db->select($this->select);
        $this->db->from($this->table);
        $this->db->group_by(array('p.id_pembelian', 'tgl_pembelian', 'fullname', 'brg.id_supplier', 's.nama_supplier'));
        // $this->db->group_by(array('p.id_pembelian', 'tgl_pembelian', 'p.id_user', 'fullname'));

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
        // $this->db->select('p.id_pembelian AS id_pembelian,
        //                     tgl_pembelian,
        //                     COUNT(id_barang) AS jumlah,
        //                     SUM(qty * dp.harga) AS total,
        //                     p.id_user AS id_user,
        //                     fullname,
        //                     brg.id_supplier AS id_supplier,
        //                     s.nama_supplier AS nama_supplier,
        //                     GROUP_CONCAT(brg.nama_barang SEPARATOR ", ") AS nama_barang');
        // $this->db->from('tbl_pembelian p');
        // $this->db->join('tbl_detail_pembelian dp', 'p.id_pembelian = dp.id_pembelian');
        // $this->db->join('tbl_user u', 'p.id_user = u.id_user');
        // $this->db->join('tbl_barang brg', 'dp.id_barang = brg.kode_barang');
        // $this->db->join('tbl_supplier s', 's.id_supplier = brg.id_supplier', 'left');
        // $this->db->where('brg.id_supplier IS NOT NULL');
        // $this->db->where('s.nama_supplier IS NOT NULL');
        // $this->db->group_by('p.id_pembelian, tgl_pembelian, fullname, brg.id_supplier, s.nama_supplier');
        // $this->db->order_by('p.id_pembelian', 'ASC');
        

        // $query = $this->db->get();

        // return $query->result();

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
        $this->db->select($this->select);
        $this->db->from($this->table);
        $this->db->group_by(array('p.id_pembelian', 'tgl_pembelian', 'fullname', 'brg.id_supplier', 's.nama_supplier'));

        return $this->db->count_all_results();
    }
}
