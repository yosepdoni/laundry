<?php
// koneksi database
include './db/config.php';

$default_title = "Lacak Pesanan - Tresia Laundry";
$default_desc = "Cek status pesanan laundry Anda dengan cepat dan mudah.";
$default_image = "https://tresialaundry.shop/assets/img/favicon/android-chrome.png";
$default_url = "https://tresialaundry.shop/tracking.php";

$title = $default_title;
$description = $default_desc;
$image = $default_image;
$url = $default_url;

// Cek apakah ada no_transaksi
if (isset($_GET['no_transaksi'])) {
    $no_transaksi = mysqli_real_escape_string($conn, $_GET['no_transaksi']);

    $query = mysqli_query($conn, "
        SELECT pesanan.*, pengguna.nama 
        FROM pesanan 
        LEFT JOIN pengguna ON pesanan.id_akun = pengguna.id_akun 
        WHERE pesanan.no_transaksi = '$no_transaksi'
    ");

    if ($data = mysqli_fetch_assoc($query)) {
        $nama = htmlspecialchars($data['nama']);
        $layanan = htmlspecialchars($data['layanan']);
        $status = htmlspecialchars($data['status']);

        $title = "Pesanan $no_transaksi ($status) | Tresia Laundry";
        $description = "Pesanan atas nama $nama menggunakan layanan $layanan sedang dalam status: $status.";
        $url = "https://tresialaundry.shop/tracking.php?no_transaksi=" . urlencode($no_transaksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- SEO Description -->
  <meta name="description" content="<?= $description ?>">

  <!-- Open Graph (untuk Facebook, WhatsApp) -->
  <meta property="og:title" content="<?= $title ?>">
  <meta property="og:description" content="<?= $description ?>">
  <meta property="og:type" content="article">
  <meta property="og:url" content="<?= $url ?>">
  <meta property="og:image" content="<?= $image ?>">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= $title ?>">
  <meta name="twitter:description" content="<?= $description ?>">
  <meta name="twitter:image" content="<?= $image ?>">

  <!-- Favicon -->
  <link rel="icon" href="https://tresialaundry.shop/assets/img/favicon/favicon.ico" type="image/x-icon">

  <!-- CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
<div class="container py-5">
   <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0">🔍 Lacak Pesanan Anda</h3>
    <a href="./" class="btn btn-outline-secondary">
      <i class="bi bi-house-door"></i> Beranda
    </a>
  </div>

  <!-- Form Pencarian -->
  <form method="GET" action="">
    <div class="input-group mb-4">
      <input type="text" class="form-control" name="no_transaksi" placeholder="Masukkan No Transaksi..." required>
      <button class="btn btn-primary" type="submit">Cari</button>
    </div>
  </form>

  <?php
  if (isset($_GET['no_transaksi'])) {
      // Gunakan ulang data yang sudah diambil di atas
      if (isset($data)) {
          echo "<div class='alert alert-info'>Hasil untuk No Transaksi: <strong>" . htmlspecialchars($no_transaksi) . "</strong></div>";

          $tanggal = date("d-m-Y H:i", strtotime($data['tanggal']));
  ?>
          <div class="card mb-3 shadow-sm">
            <div class="card-body">
              <h5 class="card-title">No Transaksi: <?= htmlspecialchars($data['no_transaksi']) ?></h5>
              <ul class="list-unstyled">
                <li><strong>Nama:</strong> <?= $nama ?></li>
                <li><strong>Layanan:</strong> <?= $layanan ?></li>
                <li><strong>Berat:</strong> <?= floatval($data['berat']) ?> Kg</li>
                <li><strong>Jarak:</strong> <?= floatval($data['jarak']) ?> Km</li>
                <li><strong>Biaya Layanan:</strong> Rp<?= number_format($data['biaya_layanan'], 0, ',', '.') ?></li>
                <li><strong>Biaya Berat:</strong> Rp<?= number_format($data['biaya_berat'], 0, ',', '.') ?></li>
                <li><strong>Total:</strong> <span class="text-success fw-bold">Rp<?= number_format($data['total'], 0, ',', '.') ?></span></li>
                <li><strong>Status:</strong> <span class="text-primary"><?= $status ?></span></li>
                <li><strong>Tanggal:</strong> <?= $tanggal ?></li>
              </ul>
            </div>
          </div>
  <?php
      } else {
          echo "<div class='alert alert-warning'>No transaksi <strong>" . htmlspecialchars($no_transaksi) . "</strong> tidak ditemukan.</div>";
      }
  }
  ?>
</div>
     <footer class="pt-4 mt-2 mb-2 border-top text-center text-muted small fixed-bottom bg-white">
            <div class="container">
                <p class="mb-1 text-dark">Copyright &copy; Tresia Laundry 2025. All rights reserved.</p>
            </div>
        </footer>

        </div>
        </main>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

        </body>

        </html>
