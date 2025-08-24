<?php
if (!defined('APP')) die('Akses ditolak!');

// Hitung total pendapatan dan transaksi dari riwayat_transaksi
$query = mysqli_query($conn, "SELECT SUM(pembayaran) AS total_pembayaran, COUNT(id_riwayat_transaksi) AS total_transaksi FROM riwayat_transaksi");
$result = mysqli_fetch_assoc($query);
$total_pendapatan = $result['total_pembayaran'];
$total_transaksi = $result['total_transaksi'];

// Hitung total akun dengan status 'pengguna'
$query_akun = mysqli_query($conn, "SELECT COUNT(id_akun) AS total_akun FROM akun WHERE status = 'pengguna'");
$result_akun = mysqli_fetch_assoc($query_akun);
$total_akun = $result_akun['total_akun'];

// Hitung total pesanan dari tabel pesanan
$query_pesanan = mysqli_query($conn, "SELECT COUNT(no_transaksi) AS total_pesanan FROM pesanan");
$result_pesanan = mysqli_fetch_assoc($query_pesanan);
$total_pesanan = $result_pesanan['total_pesanan'];
?>



    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-lg-8 col-md-5 order-1">
                    <div class="row">
                        <div class="col-lg-6 col-md-12 col-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <span class="d-block mb-1">Pendapatan</span>
                                    <h3 class="card-title mb-2">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12 col-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <span>Pelanggan</span>
                                    <h3 class="card-title text-nowrap mb-1"><?= $total_akun ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total Revenue -->
               
                <!--/ Total Revenue -->
                <div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
                    <div class="row">
                        <div class="col-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <span class="d-block mb-1">Jumlah Transaksi</span>
                                    <h3 class="card-title text-nowrap mb-2"><?= $total_transaksi ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <span class="d-block mb-1">Pesanan</span>
                                    <h3 class="card-title mb-2"><?= $total_pesanan ?></h3>
                                </div>
                            </div>
                        </div>
            
                    </div>
                </div>
            </div>
        </div>
        <!-- / Content -->