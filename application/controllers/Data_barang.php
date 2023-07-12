<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Data_barang extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //load library
        $this->load->library(['template', 'form_validation']);
        //load model
        $this->load->model('m_barang');
        $this->load->model('m_supplier');

        header('Last-Modified:' . gmdate('D, d M Y H:i:s') . 'GMT');
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
    }

    public function index()
    {
        //cek apakah user yang login adalah admin atau bukan
        //jika bukan maka alihkan ke dashboard
        $this->is_admin();

        $data = [
            'title' => 'Data Barang'
        ];

        $this->template->kasir('data_barang/index', $data);
    }

    public function tambah_data()
    {
        //cek apakah user yang login adalah admin atau bukan
        //jika bukan maka alihkan ke dashboard
        $this->is_admin();

        if ($this->input->post('submit', true) == 'submit') {
            //set rules form validasi
            $this->form_validation->set_rules(
                'kode',
                'Kode Barang',
                'required|min_length[3]|max_length[10]|is_unique[tbl_barang.kode_barang]',
                array(
                    'required' => '{field} wajib diisi',
                    'min_length' => '{field} minimal 3 karakter',
                    'max_length' => '{field} maksimal 20 karakter',
                    'is_unique' => 'Kode sudah terdaftar'
                )
            );

            $this->form_validation->set_rules(
                'supplier',
                'Supplier',
                'required',
                array(
                    'required' => '{field} wajib dipilih',
                )
            );

            $this->form_validation->set_rules(
                'nama_barang',
                'Nama Barang',
                'required|min_length[3]|max_length[255]',
                array(
                    'required' => '{field} wajib diisi',
                    'min_length' => '{field} minimal 3 karakter',
                    'max_length' => '{field} maksimal 255 karakter'
                )
            );

            $this->form_validation->set_rules(
                'brand',
                'Nama Brand',
                'required|min_length[2]',
                array(
                    'required' => '{field} wajib diisi',
                    'min_length' => '{field} minimal 2 karakter'
                )
            );

            $this->form_validation->set_rules(
                'harga',
                'Harga Jual',
                "required|regex_match[/^[0-9.]+$/]",
                array(
                    'required' => '{field} wajib diisi',
                    'regex_match' => '{field} hanya boleh angka'
                )
            );

            //jika data sudah valid maka lakukan proses penyimpanan
            if ($this->form_validation->run() == true) {
                //masukkan data ke variable array
                $supplier = $this->security->xss_clean($this->input->post('supplier', true));
                $kode_barangx = $this->security->xss_clean($this->input->post('kode', true));
                $month=date('m');
                $year=date('Y');
                $kode_barang = $kode_barangx . $supplier . $month . $year;

                $simpan = array(
                    'kode_barang' => $kode_barang,
                    'id_supplier' => $supplier,
                    'nama_barang' => $this->security->xss_clean($this->input->post('nama_barang', true)),
                    'brand' => $this->security->xss_clean($this->input->post('brand', true)),
                    'harga' => str_replace('.', '', $this->security->xss_clean($this->input->post('harga', true)))
                );

                //simpan ke database
                $save = $this->m_barang->save('tbl_barang', $simpan);

                if ($save) {
                    $this->session->set_flashdata('success', 'Data Barang berhasil ditambah...');

                    redirect('barang');
                }
            }
        }

        $data = [
            'title' => 'Tambah Data Barang',
            'supplier' => $this->m_supplier->getAllData('tbl_supplier'),
        ];

        $this->template->kasir('data_barang/form_tambah', $data);
    }

    public function edit_data($kode_barang = '')
    {

        //cek apakah user yang login adalah admin atau bukan
        //jika bukan maka alihkan ke dashboard
        $this->is_admin();

        //cek uri
        if ($kode_barang == '') {
            $this->session->set_flashdata('error', 'Data tidak valid...');

            redirect('barang');
        }

        //ambil data barang
        $barang = $this->m_barang->getData('tbl_barang', ['kode_barang' => $kode_barang]);

        //validasi jumlah data
        if ($barang->num_rows() !== 1) {
            $this->session->set_flashdata('error', 'Data tidak valid...');

            redirect('barang');
        }

        //ketika button diklik
        if ($this->input->post('update', true) == 'Update') {
            //cek apakah user merubah kode barang atau tidak
            $b = $barang->row();
            if ($b->kode_barang == $this->security->xss_clean($this->input->post('ID', true))) {
                $rules_kode_barang = 'required|min_length[3]|max_length[20]';
            } else {
                $rules_kode_barang = 'required|min_length[3]|max_length[20]|is_unique[tbl_barang.kode_barang]';
            }
            //set rules form validasi
            $this->form_validation->set_rules(
                'ID',
                'Kode Barang',
                $rules_kode_barang,
                array(
                    'required' => '{field} wajib diisi',
                    'min_length' => '{field} minimal 3 karakter',
                    'max_length' => '{field} maksimal 20 karakter',
                    'is_unique' => 'Kode sudah terdaftar'
                )
            );

            $this->form_validation->set_rules(
                'supplier',
                'Supplier',
                'required',
                array(
                    'required' => '{field} wajib dipilih',
                )
            );

            $this->form_validation->set_rules(
                'nama_barang',
                'Nama Barang',
                'required|min_length[3]|max_length[255]',
                array(
                    'required' => '{field} wajib diisi',
                    'min_length' => '{field} minimal 3 karakter',
                    'max_length' => '{field} maksimal 255 karakter'
                )
            );

            $this->form_validation->set_rules(
                'brand',
                'Nama Brand',
                'required|min_length[2]',
                array(
                    'required' => '{field} wajib diisi',
                    'min_length' => '{field} minimal 2 karakter'
                )
            );

            $this->form_validation->set_rules(
                'harga',
                'Harga Jual',
                "required|regex_match[/^[0-9.]+$/]",
                array(
                    'required' => '{field} wajib diisi',
                    'regex_match' => '{field} hanya boleh angka'
                )
            );

            $this->form_validation->set_rules(
                'status',
                'Status',
                "required|min_length[1]|max_length[1]|regex_match[/^[YN]+$/]",
                array(
                    'required' => '{field} wajib diisi',
                    'min_length' => '{field} hanya boleh 1 karakter',
                    'max_length' => '{field} hanya boleh 1 karakter',
                    'regex_match' => 'Input {field} tidak valid'
                )
            );

            //jika validasi berhasil
            if ($this->form_validation->run() == true) {

                // ambil tahun di kode barang
                $year = substr($kode_barang, -4);
                // ambil bulan di kode barang
                $month = substr($kode_barang, -6, 2);
                // check current supplier
                $current_id_supplier = $this->m_barang->getSpecificSupplier($kode_barang);

                $id_barang_without_kodex=$current_id_supplier . $month . $year;

                // get kodex which is kode barang
                $kodex = str_replace($id_barang_without_kodex, '', $kode_barang);

                $id_supplier = $this->security->xss_clean($this->input->post('supplier', true));

                // new kode barang
                $new_kode_barang = $kodex . $id_supplier . $month . $year;

                echo $new_kode_barang;

                // masukkan data ke variable array
                $update = array(
                    'kode_barang' => $new_kode_barang,
                    'id_supplier' => $id_supplier,
                    'nama_barang' => $this->security->xss_clean($this->input->post('nama_barang', true)),
                    'brand' => $this->security->xss_clean($this->input->post('brand', true)),
                    'harga' => str_replace('.', '', $this->security->xss_clean($this->input->post('harga', true))),
                    'active' => $this->security->xss_clean($this->input->post('status', true))
                );

                //simpan ke database
                $up = $this->m_barang->update('tbl_barang', $update, ['kode_barang' => $this->security->xss_clean($this->input->post('ID', true))]);

                if ($up) {
                    $this->session->set_flashdata('success', 'Data Barang berhasil diperbarui...');

                    redirect('barang');
                }
            }
        }

        $data = [
            'title' => 'Edit Data Barang',
            'supplier' => $this->m_supplier->getAllData('tbl_supplier'),
            'barang' => $barang->row()
        ];

        $this->template->kasir('data_barang/form_edit', $data);
    }

    public function stok()
    {
        //cek pegawai
        if (!$this->session->userdata('level') || $this->session->userdata('level') != 'pegawai') {
            redirect('dashboard');
        }

        $data = [
            'title' => 'Data Stok Barang'
        ];

        $this->template->kasir('data_barang/stok', $data);
    }

    public function ajax_barang()
    {
        $this->is_admin();
        //cek apakah request berupa ajax atau bukan, jika bukan maka redirect ke home
        if ($this->input->is_ajax_request()) {
            //ambil list data
            $list = $this->m_barang->get_datatables();
            //siapkan variabel array
            $data = array();
            $no = $_POST['start'];

            foreach ($list as $i) {

                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $i->kode_barang;
                $row[] = $i->id_supplier;
                $row[] = $i->nama_barang;
                $row[] = $i->brand;
                $row[] = $i->stok;
                $row[] = '<span class="float-left">Rp.</span><span class="float-right">' . number_format($i->harga, 0, ',', '.') . ',-</span>';
                $row[] = ($i->active == 'Y') ? 'Aktif' : 'Tidak Aktif';
                $row[] = '<a href="' . site_url('edit_barang/' . $i->kode_barang) . '" class="btn btn-warning btn-sm text-white">Edit</a>';

                $data[] = $row;
            }

            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_barang->count_all(),
                "recordsFiltered" => $this->m_barang->count_filtered(),
                "data" => $data
            );
            //output to json format
            echo json_encode($output);
        } else {
            redirect('dashboard');
        }
    }

    public function ajax_stok_barang()
    {
        //cek pegawai
        if (!$this->session->userdata('level') || $this->session->userdata('level') != 'pegawai') {
            redirect('dashboard');
        }
        //cek apakah request berupa ajax atau bukan, jika bukan maka redirect ke home
        if ($this->input->is_ajax_request()) {
            //ambil list data
            $list = $this->m_barang->get_datatables();
            //siapkan variabel array
            $data = array();
            $no = $_POST['start'];

            foreach ($list as $i) {

                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $i->kode_barang;
                $row[] = $i->nama_barang;
                $row[] = $i->brand;
                $row[] = $i->stok;
                $row[] = '<span class="float-left">Rp.</span><span class="float-right">' . number_format($i->harga, 0, ',', '.') . ',-</span>';

                $data[] = $row;
            }

            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_barang->count_all(),
                "recordsFiltered" => $this->m_barang->count_filtered(),
                "data" => $data
            );
            //output to json format
            echo json_encode($output);
        } else {
            redirect('dashboard');
        }
    }

    private function is_admin()
    {
        if (!$this->session->userdata('level') || $this->session->userdata('level') != 'admin') {
            redirect('dashboard');
        }
    }
}
