<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_akun'])) {
    echo "<script>window.location.href='?page=login';</script>";
    exit;
}

$id_akun = $_SESSION['id_akun'];
?>


<style>
    /* HP (layar sangat kecil) */
    @media (max-width: 576px) {
        .table-responsive {
            font-size: 12px !important;
        }
    }

    /* Tablet potret (sedang) */
    @media (min-width: 577px) and (max-width: 768px) {
        .table-responsive {
            font-size: 13px !important;
        }
    }

    /* Tablet lanskap atau laptop kecil */
    @media (min-width: 769px) and (max-width: 992px) {
        .table-responsive {
            font-size: 14px !important;
        }
    }

    /* Layar besar (PC/monitor) */
    @media (min-width: 993px) {
        .table-responsive {
            font-size: 15px !important;
        }
    }
</style>
<?php
// Cek apakah user punya pesanan aktif
$sql_cek_pesanan = "SELECT COUNT(*) as jumlah FROM pesanan WHERE id_akun = ?";
$stmt_cek = $conn->prepare($sql_cek_pesanan);
$stmt_cek->bind_param("s", $id_akun);
$stmt_cek->execute();
$res_cek = $stmt_cek->get_result();
$cek = $res_cek->fetch_assoc();
$ada_pesanan = $cek['jumlah'] > 0;
$stmt_cek->close();
?>

<div class="container mt-5 py-4">
    <h3 class="text-start mb-4"><i class="bi bi-box-arrow-in-right fs-3 me-2"></i> Pesanan</h3>
    <div class="row mb-4">
        <div class="col text-end">

            <?php $tampildata; ?>

            <?php if (!$ada_pesanan): ?>
                <p class="text-xl-start">
                    Halo <strong><?= htmlspecialchars($nama_depan); ?></strong>, pesanan anda masih kosong! kami menyediakan layanan penjemputan pakaian untuk pelanggan, silahkan klik ya untuk melanjutkan?
                    <button type="button" class="badge bg-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Ya
                    </button>
                </p>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Konfirmasi</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-start">
                                Pastikan semua pakaian sudah siap dan terpisah dari barang lainnya.
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <a href="?page=pesanan&aksi=jemput" class="btn btn-success ms-2">Lanjutkan</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="container text-start">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover text-start">
                        <thead>
                            <tr>
                                <th scope="col">Nomor Transaksi</th>
                                <th scope="col">Berat (Kg)</th>
                                <th scope="col">Status</th>
                                <th scope="col">Layanan</th>
                                <th scope="col">Tanggal/ Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?= tampilDataPesanan($conn, $id_akun); ?>
                        </tbody>
                    </table>
                </div>

                <?php
                $pesananSelesai = getDataPesananSelesai($conn, $id_akun);
                ?>

                <?php if (count($pesananSelesai) > 0): ?>
                    <div class="text text-success mt-5">
                        <h5>Menunggu Pembayaran</h5>
                    </div>

                    <div class="d-flex flex-wrap gap-3">
                        <!-- kemudian loop foreach -->

                        <?php foreach ($pesananSelesai as $row): ?>
                            <?php
                            $no_transaksi = $row['no_transaksi'];
                            $deskripsi = $row['gabungan'] ?? '';
                            $berat = (float)$row['berat'];

                            // Nilai biaya diambil dari database
                            $biayaBerat = $row['biaya_berat'] ?? 0;
                            $biayaJarak = $row['biaya_layanan'] ?? null;
                            $totalBiaya = $row['total'] ?? $biayaBerat;

                            ?>

                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3">
                                <div class="card shadow-sm h-100">
                                    <div class="card-body">
                                        <p class="card-title fw-bold">
                                            Transaksi: <?= htmlspecialchars($no_transaksi) ?>
                                        </p>
                                        <p class="card-text">
                                            Berat: <?= htmlspecialchars($berat ?? '-') ?> Kg<br>
                                            Status: <?= htmlspecialchars($row['status']) ?><br>
                                            Layanan: <?= htmlspecialchars($row['layanan']) ?><br>
                                            Lokasi: <?= htmlspecialchars($row['lokasi']) ?><br>
                                            Tanggal: <?= htmlspecialchars(date("d-m-Y / H:i:s", strtotime($row['tanggal']))) ?><br>
                                        </p>

                                        <div class="info-jarak fst-italic text-end small">
                                            <em>
                                                Layanan: <?= is_numeric($biayaJarak) ? 'Rp' . number_format($biayaJarak) : '-' ?><br>
                                                Berat: Rp<?= number_format($biayaBerat) ?><br>
                                                Total: Rp<?= number_format($totalBiaya) ?>
                                            </em>
                                        </div>

                                        <hr>

                                        <div class="text-start mt-4 fs-6">
                                            <div class="row">
                                                <div class="col-3"><span class="text-danger">Note:</span></div>
                                                <div class="col-9">
                                                    <p class="mb-0">Pembayaran menggunakan scan QR dan Tunai</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>

                    </div>
                <?php else: ?>
                    <!-- <div class="text-muted">Tidak ada pesanan selesai.</div> -->
                <?php endif; ?>
            </div>

        </div>

    </div>
</div>