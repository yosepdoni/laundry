    <html lang="id">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Tresia Laundry | Solusi Laundry Terpercaya</title>

        <!-- Deskripsi untuk Google -->
        <meta name="description" content="Solusi laundry terpercaya untuk Anda yang mengutamakan kebersihan, kenyamanan, dan ketepatan waktu.">
        <meta name="author" content="Tresia Laundry">
        <meta name="robots" content="index, follow">

        <!-- Ikon browser/tab -->
        <link rel="icon" type="image/x-icon" href="https://tresialaundry.shop/assets/img/favicon/favicon.ico" />
        <link rel="apple-touch-icon" href="https://tresialaundry.shop/assets/img/favicon/apple-touch-icon.png">
        <link rel="shortcut icon" href="https://tresialaundry.shop/assets/img/favicon/favicon.ico" type="image/x-icon">

        <!-- Canonical URL -->
        <link rel="canonical" href="https://tresialaundry.shop/">

        <!-- Open Graph untuk sosial media -->
        <meta property="og:title" content="Tresia Laundry | Solusi Laundry Terpercaya">
        <meta property="og:description" content="Layanan laundry cepat, bersih, dan terpercaya. Antar jemput tersedia di daerah Anda.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="https://tresialaundry.shop/">
        <meta property="og:image" content="https://tresialaundry.shop/assets/img/favicon/android-chrome.png">

        <!-- Twitter Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="Tresia Laundry | Solusi Laundry Terpercaya">
        <meta name="twitter:description" content="Layanan laundry cepat, bersih, dan terpercaya. Antar jemput tersedia di daerah Anda.">
        <meta name="twitter:image" content="https://tresialaundry.shop/assets/img/favicon/android-chrome.png">

        <!-- Warna tema browser -->
        <meta name="theme-color" content="#28c76f">

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
            crossorigin="anonymous">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
            crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    </head>

    <body>
        <main>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
                <div class="container-fluid">
                    <a class="navbar-brand" href="./">
                        <img src="./assets/images/logo.png" alt="Logo" width="30" height="30" class="me-2">Tresia
                        Laundry
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                        <ul class="navbar-nav text-end">
                            <li class="nav-item">
                                <?php if (isset($_SESSION['no_hp']) && isset($_SESSION['status']) && $_SESSION['status'] != 'admin'): ?>
                                    <a class="nav-link" href="?page=pesanan">Pesanan</a>
                                <?php else: ?>
                                <?php endif; ?>
                            </li>
                        </ul>
                        <ul class="navbar-nav text-end">
    <?php if (isset($_SESSION['no_hp']) && isset($_SESSION['status']) && $_SESSION['status'] != 'admin'): ?>
        <li class="nav-item">
            <div class="dropdown">
                <?php
                $no_hp = $_SESSION['no_hp'];
                $last_digits = substr($no_hp, -4);
                $hidden_part = str_repeat('*', strlen($no_hp) - 4);
                ?>
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownb"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle"></i>
                    <?= $hidden_part . $last_digits ?>
                </button>

                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownb">
                    <li><a class="dropdown-item" href="?page=akun"><i class="bi bi-gear"></i> Akun</a></li>
                    <li><a class="dropdown-item" href="?page=riwayat_transaksi"><i class="bi bi-clock-history"></i> Riwayat Transaksi</a></li>
                    <li><a class="dropdown-item" onclick="logout()" style="cursor: pointer;"><i class="bi bi-box-arrow-in-left"></i> Logout</a></li>
                </ul>
            </div>
        </li>
    <?php else: ?>
         <li class="nav-item">
            <a href="tracking.php" class="nav-link text-light">
                </i> Lacak Pesanan
            </a>
        </li>
        <li class="nav-item">
            <a href="?page=login" class="nav-link text-light">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
        </li>
    <?php endif; ?>
</ul>

                    </div>
                </div>
            </nav>