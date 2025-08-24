<?php
include '../../db/config.php';

// Set zona waktu ke WIB
date_default_timezone_set('Asia/Jakarta');

// Bulan dalam Bahasa Indonesia
$bulanIndo = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];

// Fungsi format bulan YYYY-MM → Bahasa Indonesia
function formatBulanIndonesia($ym)
{
    global $bulanIndo;
    list($tahun, $bulan) = explode('-', $ym);
    return $bulanIndo[$bulan] . ' ' . $tahun;
}

// Fungsi format tanggal YYYY-MM-DD → Bahasa Indonesia
function formatTanggalIndonesia($tanggal)
{
    global $bulanIndo;
    $tgl = date('d', strtotime($tanggal));
    $bln = date('m', strtotime($tanggal));
    $thn = date('Y', strtotime($tanggal));
    return "$tgl " . $bulanIndo[$bln] . " $thn";
}

// Ambil filter
$filter = $_GET['filter'] ?? '';
$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

$where = '';
if ($filter === 'harian') {
    $today = date('Y-m-d');
    $where = "WHERE DATE(r.tanggal_bayar) = '$today'";
} elseif ($filter === 'tanggal' && $dari && $sampai) {
    $where = "WHERE DATE(r.tanggal_bayar) BETWEEN '$dari' AND '$sampai'";
} elseif ($filter === 'bulanan' && $dari && $sampai) {
    $where = "WHERE DATE_FORMAT(r.tanggal_bayar, '%Y-%m') BETWEEN '$dari' AND '$sampai'";
} elseif ($filter === 'tahunan' && $dari && $sampai) {
    $where = "WHERE YEAR(r.tanggal_bayar) BETWEEN '$dari' AND '$sampai'";
}

// Ambil data transaksi
$query = mysqli_query($conn, "
    SELECT r.no_transaksi, r.deskripsi, r.pembayaran, r.tanggal_bayar, p.nama
    FROM riwayat_transaksi r
    JOIN pengguna p ON r.id_akun = p.id_akun
    $where
    ORDER BY r.id_riwayat_transaksi DESC
");

// Total pembayaran
$totalQuery = mysqli_query($conn, "SELECT SUM(r.pembayaran) AS total FROM riwayat_transaksi r $where");
$totalRow = mysqli_fetch_assoc($totalQuery);
$totalPembayaran = $totalRow['total'] ?? 0;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Tresia Laundry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        td.text-end {
            text-align: right;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-start;;">
        <!-- Logo -->
        <div>
            <img src="../../assets/img/favicon/logo-laporan.png" alt="Logo" style="height: 50px;">
        </div>

        <!-- Judul -->
        <div style="text-align: center; flex-grow: 1;">
            <h2 style="margin-top: -5px;">Tresia Laundry</h2>
            <p style="margin-top: 5px;">Jln. Bangkam Desa Bukit Batu, Kec. Sungaui Kunyit, Kab. Mempawah, Kalbar Telp. 0812-5471-6593</p>
        </div>

        <!-- Cetakan dan Filter -->
        <div style="text-align: right; margin-top: 10px">
            Cetakan: <?= date('d-m-Y') ?><br>
            <p style="margin-top: 1px;">
                <?php if ($filter === 'harian'): ?>
                    Laporan: <?= date('d-m-Y') ?>
                <?php elseif ($filter === 'tanggal' && $dari && $sampai): ?>
                    Laporan: <?= date('d-m-Y', strtotime($dari)) ?> - <?= date('d-m-Y', strtotime($sampai)) ?>
                <?php elseif ($filter === 'bulanan' && $dari && $sampai): ?>
                    Laporan: <?= formatBulanIndonesia($dari) ?> - <?= formatBulanIndonesia($sampai) ?>
                <?php elseif ($filter === 'tahunan' && $dari && $sampai): ?>
                    Laporan: <?= $dari ?> - <?= $sampai ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
    <hr>

    <!-- Tabel Transaksi -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Transaksi</th>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th>Pembayaran</th>
                <th>Tanggal Bayar</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($query)) :
                $pembayaran = "Rp" . number_format($row['pembayaran'], 0, ',', '.');
                $tanggal = date('d-m-Y', strtotime($row['tanggal_bayar']));
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['no_transaksi']) ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                    <td class="text-end"><?= $pembayaran ?></td>
                    <td><?= $tanggal ?></td>
                </tr>
            <?php endwhile; ?>
            <?php if ($no === 1): ?>
                <tr>
                    <td colspan="6" style="text-align:center;">Tidak ada data ditemukan.</td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="2"><strong>Total Pembayaran</strong></td>
                    <td colspan="4" style="text-align: right;"><strong>Rp<?= number_format($totalPembayaran, 0, ',', '.') ?></strong></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Footer -->
    <div style="display: flex; justify-content: space-between;">
        <div>
            <p><strong>Laporan Transaksi</strong></p>
        </div>
        <div style="text-align: center;">
            <p>Mempawah, <?= formatTanggalIndonesia(date('Y-m-d')) ?></p>
            <br><br>
            <p style="margin-top: 60px;">(...........................................)</p>
        </div>
    </div>

</body>

</html>