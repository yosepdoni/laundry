<?php
session_start();
define('APP', true);
// Cek apakah yang login adalah admin
if (!isset($_SESSION['status']) || $_SESSION['status'] !== 'admin') {
    // Redirect pengguna biasa ke halaman utama
    echo "<script>alert('Akses ditolak!'); window.location.href = '/laundry';</script>";
    exit;
}


include '../db/config.php';
include 'layout/sidebar.php';
include 'layout/navbar.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if ($page == 'home') {
    include 'pages/dashboard.php';
} elseif ($page == 'login') {
    include 'pages/login.php';
} elseif ($page == 'pengaturan') {
    include 'pages/pengaturan.php';
} elseif ($page == 'pesanan') {
    include 'pages/pesanan.php';
} elseif ($page == 'tambah_pesanan') {
    include 'pages/tambah_pesanan.php';
} elseif ($page == 'laporan') {
    include 'pages/laporan.php';
} else {
    echo "<p>Halaman tidak ditemukan.</p>";
}

include 'layout/footer.php';
