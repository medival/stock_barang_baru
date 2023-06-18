<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Template
{
    public function __construct()
    {
        $this->ci = &get_instance();
    }

    public function kasir($template, $data_content = array())
    {
        $data['content']    = $this->ci->load->view($template, $data_content, true);
        $data['navbar']     = $this->ci->load->view('template/nav', $data_content, true);

        $this->ci->load->view('template/index', $data);
    }

    public function cetak($template, $data_content = array())
    {
        $data['content'] = $this->ci->load->view($template, $data_content, true);

        $this->ci->load->view('template/index_cetak', $data);
    }
}
