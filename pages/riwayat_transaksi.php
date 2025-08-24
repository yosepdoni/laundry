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

<div class="container mt-5 py-4">
    <h3 class="text-start mb-4"><i class="bi bi-box-arrow-in-right fs-3 me-2"></i> Riwayat Transaksi</h3>
    <div class="row mb-4">
        <div class="col text-end">
            <?php
            ob_start();  // Mulai output buffering

            $id_akun = $_SESSION['id_akun'] ?? null;
            $tampildata = ob_get_clean();
            ?>

            <div class="container text-start">
                <?= tampilDataRiwayatTransaksi($conn, $id_akun); ?>
            </div>