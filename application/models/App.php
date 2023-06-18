<?php

defined('BASEPATH') or exit('No direct script access allowed');

class App extends CI_Model
{
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

    public function delete($table = null, $where = null)
    {
        $this->db->where($where);
        $this->db->delete($table);

        return $this->db->affected_rows();
    }
}
