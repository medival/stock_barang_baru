<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-sm-12 col-md-10">
        <h4 class="mb-0"><i class="fa fa-file-text"></i> Laporan Harian Laba Rugi</h4>
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
        <a href="<?= site_url('laporan/labarugihariancetak/' . date('Y-m-d', strtotime(str_replace('/', '-', $tanggal)))); ?>" class="btn btn-success btn-block btn-sm" target="_blank">
            <i class="fa fa-print"></i> Cetak Laporan
        </a>
    </div>
</div>
<table class="table table-sm table-bordered mt-3">
    <thead class="thead-dark">
        <tr>
            <th scope="col">Jumlah Barang Dibeli </th>
            <th scope="col">Jumlah Barang Terjual</th>
            <th scope="col">Total Pengeluaran</th>
            <th scope="col">Total Pemasukan</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 1;
$row = 1;
$total = 0;
$totalpembelian = 0;
$totalbarangdibeli = 0;
$datapembelian = $this->db->join('tbl_pembelian', 'tbl_detail_pembelian.id_pembelian = tbl_pembelian.id_pembelian')->where('tgl_pembelian', $date)->get('tbl_detail_pembelian')->result();
foreach ($datapembelian as $pembelian) {
    $totalpembelian += $pembelian->qty * $pembelian->harga;
    $totalbarangdibeli += $pembelian->qty;
}
$totalpenjualan = 0;
$totalbarangdijual = 0;
$datapenjualan = $this->db->join('tbl_penjualan', 'tbl_detail_penjualan.id_penjualan = tbl_penjualan.id_penjualan')->where('tgl_penjualan', $date)->get('tbl_detail_penjualan')->result();
foreach ($datapenjualan as $penjualan) {
    $totalpenjualan += $penjualan->qty * $penjualan->harga;
    $totalbarangdijual += $penjualan->qty;
}
$rugi = 0;
$laba = $totalpenjualan - $totalpembelian;
if ($laba < 0) {
    $rugi = abs($laba);
    $laba = 0;
}
?>
        <tr>
            <td><span class="float-left"><?= $totalbarangdibeli ?></span></td>
            <td><span class="float-left"><?= $totalbarangdijual ?></span></td>
            <td>
                <span class="float-left">Rp.</span><span class="float-right"><?= number_format($totalpembelian, 0, ',', '.') ?></span>
            </td>
            <td>
                <span class="float-left">Rp.</span><span class="float-right"><?= number_format($totalpenjualan, 0, ',', '.') ?></span>
            </td>
        </tr>
        <tr>
            <td colspan="8">
        </tr>
        <?php
echo '<tr>';
echo '<td colspan="3" class="text-center text-success"><b>Total Laba</b></td>';
echo '<td><b><span class="float-left">Rp.</span><span class="float-right">' . number_format($laba, 0, ',', '.') . '</span></b></td>';
echo '</tr>';
echo '<tr>';
echo '<td colspan="3" class="text-center text-danger"><b>Total Rugi</b></td>';
echo '<td><b><span class="float-left">Rp.</span><span class="float-right">' . number_format($rugi, 0, ',', '.') . '</span></b></td>';
echo '</tr>';
?>
    </tbody>
</table>