<?php
defined('BASEPATH') or exit('No direct script access allowed');

function tanggal_indo($tgl)
{
    $bulan  = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    $exp    = explode('-', date('d-m-Y', strtotime($tgl)));

    return $exp[0] . ' ' . $bulan[(int) $exp[1]] . ' ' . $exp[2];
}
?>
<img src="<?= base_url('assets/img/ok.png'); ?>" class="logo" />
<h6 class="display-5 text-center mt-2 mb-0">Laporan Bulanan Laba & Rugi</h6>
<p class="text-center display-6 mt-0"><?= 'Bulan ' . ucwords($bulan) . ' Tahun ' . $tahun; ?></p>
<hr class="mt-0" />
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
        $datapembelian = $this->db->join('tbl_pembelian', 'tbl_detail_pembelian.id_pembelian = tbl_pembelian.id_pembelian')->where('month(tgl_pembelian)', $month)->where('year(tgl_pembelian)', $year)->get('tbl_detail_pembelian')->result();
        foreach ($datapembelian as $pembelian) {
            $totalpembelian += $pembelian->qty * $pembelian->harga;
            $totalbarangdibeli += $pembelian->qty;
        }
        $totalpenjualan = 0;
        $totalbarangdijual = 0;
        $datapenjualan = $this->db->join('tbl_penjualan', 'tbl_detail_penjualan.id_penjualan = tbl_penjualan.id_penjualan')->where('month(tgl_penjualan)', $month)->where('year(tgl_penjualan)', $year)->get('tbl_detail_penjualan')->result();
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