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
<h6 class="display-5 text-center mt-2 mb-0">Laporan Harian Barang Keluar</h6>
<p class="text-center display-6 mt-0"><?= tanggal_indo($tanggal); ?></p>
<hr class="mt-0" />
<table class="table table-sm table-bordered mt-3">
    <thead>
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
            echo '<td colspan="9" class="text-center">Data tidak ditemukan</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
</table>