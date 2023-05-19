<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-sm-12 col-md-10">
        <h4 class="mb-0"><i class="fa fa-file-text"></i> Laporan Harian Barang Keluar</h4>
    </div>
</div>
<hr class="mt-0" />
<?php
if ($this->session->flashdata('alert')) {
    echo '<div class="alert alert-danger" role="alert">
    ' . $this->session->flashdata('alert') . '
  </div>';
}
?>
<div class="row">
    <div class="col-md-10 col-sm-12">
        <?= form_open('', ['class' => "form-inline"]); ?>
        <div class="form-group mx-sm-3 mb-2">
            <label for="date-picker" class="sr-only">Tanggal</label>
            <input type="text" name="tanggal" class="form-control form-control-sm" id="date-picker" placeholder="dd/mm/yyyy" value="<?= $tanggal; ?>">
        </div>
        <button type="submit" class="btn btn-primary mb-2 btn-sm" name="cari" value="Search">
            Cari Data
        </button>
        <?= form_close(); ?>
    </div>
    <div class="col-md-2 col-sm-12">
        <a href="<?= site_url('penjualan_harian/' . date('Y-m-d', strtotime(str_replace('/', '-', $tanggal)))); ?>" class="btn btn-success btn-block btn-sm" target="_blank">
            <i class="fa fa-print"></i> Cetak Laporan
        </a>
    </div>
</div>
<table class="table table-sm table-bordered mt-3">
    <thead class="thead-dark">
        <tr>
            <th scope="col">#</th>
            <th scope="col">ID </th>
            <th scope="col">Nama </th>
            <th scope="col">Nama Barang</th>
            <th scope="col">Brand</th>
            <th scope="col" class="text-center">Jumlah</th>
            <th scope="col" class="text-center">Harga Beli</th>
            <th scope="col" class="text-center">Harga Jual</th>
            <th scope="col" class="text-center">Laba</th>
            <th scope="col" class="text-center">Total Terjual</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
        $row = 1;
        if ($data->num_rows() > 0) {
            $total = 0;

            foreach ($data->result() as $dt) {
                echo '<tr>';
                if ($row == 1) :
                    echo '<td rowspan="' . $dt->row . '">' . $i++ . '</td>';
                    echo '<td rowspan="' . $dt->row . '">' . $dt->id_penjualan . '</td>';
                    echo '<td rowspan="' . $dt->row . '">' . $dt->nama_pembeli . '</td>';
                endif;
                echo '<td>' . $dt->nama_barang . '</td>';
                echo '<td>' . $dt->brand . '</td>';
                echo '<td>' . $dt->qty . '</td>';
                echo '<td><span class="float-left">Rp.</span><span class="float-right">' . number_format($dt->harga_beli, 0, ',', '.') . '</span></td>';
                echo '<td><span class="float-left">Rp.</span><span class="float-right">' . number_format($dt->harga_jual, 0, ',', '.') . '</span></td>';
                echo '<td><span class="float-left">Rp.</span><span class="float-right">' . number_format((($dt->harga_jual - $dt->harga_beli) * $dt->qty), 0, ',', '.') . '</span></td>';
                echo '<td><span class="float-left">Rp.</span><span class="float-right">' . number_format(($dt->harga * $dt->qty), 0, ',', '.') . '</span></td>';
                echo '</tr>';
                
                if ($row < $dt->row) {
                    $row++;
                } else {
                    $row = 1;
                }

                $total += ($dt->harga * $dt->qty);
                $totalLaba += (($dt->harga_jual - $dt->harga_beli) * $dt->qty);
            }

            echo '<tr>';
            echo '<td colspan="9" class="text-center"><b>Total Biaya</b></td>';
            echo '<td><b><span class="float-left">Rp.</span><span class="float-right">' . number_format($total, 0, ',', '.') . '</span></b></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="9" class="text-center"><b> Laba Hari ini </b></td>';
            echo '<td><b><span class="float-left">Rp.</span><span class="float-right">' . number_format($totalLaba, 0, ',', '.') . '</span></b></td>';
            echo '</tr>';
        } else {
            echo '<tr>';
            echo '<td colspan="10" class="text-center">Data tidak ditemukan</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>