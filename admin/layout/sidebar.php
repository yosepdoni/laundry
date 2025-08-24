<?php
$result = mysqli_query($conn, "SELECT COUNT(*) AS pesanan_baru FROM pesanan WHERE status = 'Menunggu Penjemputan'");
$row = mysqli_fetch_assoc($result);
$pesananBaru = $row['pesanan_baru'];
?>

<!DOCTYPE html>
<html
    lang="en"
    class="light-style layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="../assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Tresia Laundry Admin</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
    <style>
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus,
        .form-control:focus,
        .form-select:focus {
        border-color: #28a745 !important; /* hijau */
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important; /* efek glow hijau */
        outline: none !important;
        }
        /* Seluruh teks di halaman dibuat hitam */
        body, h1, h2, h4, h5, h6, p, span, a, li, div, td, th, label, strong, em {
        color: black !important;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        select {
        color: black !important;
        }

        /* Untuk placeholder juga hitam */
        ::placeholder {
        color: black !important;
        opacity: 1; /* agar tidak transparan */
        }

        /* Teks option yang muncul di select (dropdown) */
        select option {
        color: black !important;
        }

        .btn {
        background-color: #28a745; 
        border-color: #28a745 !important;
        color: white !important;                
        }

        .btn:hover {
            background-color: darkslategrey !important;
        }

        /* Menu aktif = putih */
        #layout-menu .menu-item.active > .menu-link {
        background-color: darkgray !important;
        }

        /* Teks menu = hitam */
        #layout-menu .menu-link div,
        #layout-menu .menu-link i,
        #layout-menu .menu-header-text,
        #layout-menu .app-brand-text {
        color: black !important;
        }
 </style>


    <script src="../assets/vendor/js/helpers.js"></script>

    <script src="../assets/js/config.js"></script>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="#" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="../assets/images/logo.png" alt="Logo" width="30" height="30" class="me-2">
                        </span>
                        <span class="app-brand-text fs-5 menu-text fw-bolder ms-2">Tresia Laundry</span>
                    </a>

                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item">
                        <a href="?page=home" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Analytics">Beranda</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Menu Navigasi</span>
                    </li>
                    <li class="menu-item">
                        <a href="?page=pesanan" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-dock-top"></i>
                            <div data-i18n="Account Settings">Pesanan</div>
                            <span class="flex-shrink-0 badge badge-center rounded-pill bg-dark text-white w-px-20 h-px-20 ms-5"><?= $pesananBaru ?></span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="?page=laporan" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-file"></i>
                            <div data-i18n="Authentications">Laporan</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="?page=pengaturan" class="menu-link">
                            <i class="bx bx-cog me-2"></i>
                            <div data-i18n="Misc">Pengaturan</div>
                        </a>
                    </li>
                </ul>
            </aside>
            <!-- / Menu -->