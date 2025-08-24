<?php
session_start();
define('APP', true);
include 'db/config.php';
include 'layout/navbar.php';
include 'utils/toast.php';
include_once './backend/tampil_data_table.php';
include_once './backend/tampil_data_table_riwayat_transaksi.php';
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if ($page == 'home') {
    include 'pages/home.php';
} elseif ($page == 'login') {
    include 'pages/login.php';
} elseif ($page == 'register') {
    include 'pages/register.php';
} elseif ($page == 'akun') {

    require_once './backend/akun.php';
    include 'pages/akun.php';
} elseif ($page == 'pesanan') {
    require_once './backend/pesanan.php';
    include 'pages/pesanan.php';
    
} elseif ($page == 'riwayat_transaksi') {
    include 'pages/riwayat_transaksi.php';
} else {
    echo "<p>Halaman tidak ditemukan.</p>";
}

include 'layout/footer.php';
