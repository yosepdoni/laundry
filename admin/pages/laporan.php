<?php
if (!defined('APP')) die('Akses ditolak!');

// Atur zona waktu ke Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

// Ambil input filter
$filter = $_GET['filter'] ?? '';
$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

$where = '';
if ($filter === 'harian' && $dari) {
    $sampai = $dari; // samakan, karena hanya satu tanggal
    $where = "WHERE DATE(r.tanggal_bayar) = '$dari'";
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
?>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h5 class="fw-bold py-3 mb-4">
            <span>Laporan /</span> Laporan
        </h5>

        <form method="GET" action="pages/cetak_laporan.php" target="_blank" class="mb-3 row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Filter Waktu:</label>
                <select name="filter" id="filter" class="form-select" onchange="toggleFields()">
                    <option value="">Semua</option>
                    <option value="harian" <?= $filter == 'harian' ? 'selected' : '' ?>>Harian (Hari Ini)</option>
                    <option value="tanggal" <?= $filter == 'tanggal' ? 'selected' : '' ?>>Tanggal</option>
                    <option value="bulanan" <?= $filter == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
                    <option value="tahunan" <?= $filter == 'tahunan' ? 'selected' : '' ?>>Tahunan</option>
                </select>
            </div>

            <div class="col-md-3" id="dari-group">
                <label class="form-label">Dari:</label>
                <input name="dari" id="dari" class="form-control" value="<?= $dari ?>">
            </div>

            <div class="col-md-3" id="sampai-group">
                <label class="form-label">Sampai:</label>
                <input name="sampai" id="sampai" class="form-control" value="<?= $sampai ?>">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </form>

        <script>
            function toggleFields() {
                const filter = document.getElementById('filter').value;
                const dari = document.getElementById('dari');
                const sampai = document.getElementById('sampai');
                const dariGroup = document.getElementById('dari-group');
                const sampaiGroup = document.getElementById('sampai-group');

                // Reset semua tampilan & type
                dariGroup.style.display = 'none';
                sampaiGroup.style.display = 'none';
                dari.type = 'date';
                sampai.type = 'date';
                dari.placeholder = '';
                sampai.placeholder = '';

                // Kondisi tampil sesuai filter
                if (filter === 'tanggal') {
                    dari.type = 'date';
                    sampai.type = 'date';
                    dariGroup.style.display = 'block';
                    sampaiGroup.style.display = 'block';
                } else if (filter === 'bulanan') {
                    dari.type = 'month';
                    sampai.type = 'month';
                    dariGroup.style.display = 'block';
                    sampaiGroup.style.display = 'block';
                } else if (filter === 'tahunan') {
                    dari.type = 'number';
                    sampai.type = 'number';
                    dari.placeholder = 'Tahun Awal';
                    sampai.placeholder = 'Tahun Akhir';
                    dariGroup.style.display = 'block';
                    sampaiGroup.style.display = 'block';
                } else if (filter === 'harian') {
                    dari.type = 'date';
                    dariGroup.style.display = 'block';
                    sampaiGroup.style.display = 'none';

                    // Saat pilih tanggal, salin ke "sampai"
                    dari.addEventListener('input', function() {
                        sampai.value = dari.value;
                    });
                }
            }

            window.addEventListener('DOMContentLoaded', toggleFields);
        </script>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
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
                                $no_transaksi = htmlspecialchars($row['no_transaksi']);
                                $nama = htmlspecialchars($row['nama']);
                                $deskripsi = htmlspecialchars($row['deskripsi']);
                                $pembayaran = "Rp" . number_format($row['pembayaran'], 0, ',', '.');
                                $tanggal = date('d-m-Y', strtotime($row['tanggal_bayar']));
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $no_transaksi ?></td>
                                    <td><?= $nama ?></td>
                                    <td><?= $deskripsi ?></td>
                                    <td class="text-end"><?= $pembayaran ?></td>
                                    <td><?= $tanggal ?></td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if ($no === 1): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>