<?php
ob_start();
date_default_timezone_set('Asia/Jakarta');
$tanggal = date('Y-m-d H:i:s');

// Ambil data akun
$id_akun = $_SESSION['id_akun'] ?? null;

$sql = "SELECT lokasi, alamat, nama FROM pengguna WHERE id_akun = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_akun);
$stmt->execute();
$stmt->bind_result($lokasi, $alamat, $nama);
$hasData = $stmt->fetch();
$stmt->close();

// Validasi
$lokasi_valid = $hasData && !empty($lokasi) && strlen(trim($lokasi)) >= 3;
$alamat_valid = $hasData && !empty($alamat) && strlen(trim($alamat)) >= 5;
$nama_depan = $hasData && !empty($nama) ? explode(' ', trim($nama))[0] : null;
$nama_valid = !empty($nama_depan);

if (isset($_GET['aksi']) && $_GET['aksi'] == 'jemput') {
    if (!$lokasi_valid || !$alamat_valid || !$nama_valid) {
        $_SESSION['toast_type'] = 'error';
        $_SESSION['toast_message'] = 'Lengkapi akun kamu!.';
        $_SESSION['show_toast'] = true;

        echo "<script>window.location.href='?page=akun';</script>";
        exit;
    } else {
        // Cek jarak ke lokasi dari tabel pengaturan
        $sqlPengaturan = "SELECT lokasi FROM pengaturan LIMIT 1";
        $resultPengaturan = $conn->query($sqlPengaturan);
        $lokasiPengaturan = null;

        if ($resultPengaturan && $rowPengaturan = $resultPengaturan->fetch_assoc()) {
            $lokasiPengaturan = $rowPengaturan['lokasi'];
        }

        if (!empty($lokasiPengaturan) && !empty($lokasi)) {
            list($latUser, $lonUser) = array_map('floatval', explode(',', $lokasi));
            list($latOrigin, $lonOrigin) = array_map('floatval', explode(',', $lokasiPengaturan));

            // Fungsi hitung jarak
            function hitungJarak($lat1, $lon1, $lat2, $lon2) {
                $R = 6371;
                $dLat = deg2rad($lat2 - $lat1);
                $dLon = deg2rad($lon2 - $lon1);
                $a = sin($dLat / 2) * sin($dLat / 2) +
                    cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                    sin($dLon / 2) * sin($dLon / 2);
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                return $R * $c;
            }

            $jarakKmCek = hitungJarak($latOrigin, $lonOrigin, $latUser, $lonUser);
            $batasJangkauan = 10; // km

            if ($jarakKmCek > $batasJangkauan) {
                $_SESSION['toast_type'] = 'warning';
                $_SESSION['toast_message'] = 'Jarak Anda berada di luar jangkauan layanan!.';
                $_SESSION['show_toast'] = true;

                echo "<script>window.location.href='?page=pesanan';</script>";
                exit;
            }
        }

        // No transaksi
        $no_transaksi = 'TRS' . date('YmdHis') . $id_akun;

        // Nilai awal
        $berat = 0;

        // Fungsi hitung biaya jarak
        function hitungBiayaJarak($jarakKm) {
            $tarifDasar = 10000;
            $tarifPerKm = 5000;

            if ($jarakKm <= 1) return $tarifDasar;
            if ($jarakKm <= 5) return round($tarifDasar + $tarifPerKm * ($jarakKm - 1));
            return null;
        }

        $originLat = -0.00154;
        $originLon = 110.88970;
        $tarifPerKg = 15000;

        $coords = explode(',', $lokasi);
        $latUser = $lonUser = null;

        if (count($coords) == 2) {
            $latUser = floatval(trim($coords[0]));
            $lonUser = floatval(trim($coords[1]));
        }

        $biayaBerat = $berat > 0 ? $berat * $tarifPerKg : 0;

        $jarakKm = $biayaJarak = null;

        if ($latUser && $lonUser) {
            $jarakKm = hitungJarak($originLat, $originLon, $latUser, $lonUser);
            $biayaJarak = hitungBiayaJarak($jarakKm);
            if (is_numeric($biayaJarak)) {
                $biayaJarak = round($biayaJarak / 2); // diskon 50%
            }
        }

        $BiayaLayanan = is_numeric($biayaJarak) ? $biayaBerat + $biayaJarak : $biayaBerat;

        // waktu indonesia
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d H:i:s');
        // Simpan pesanan ke database
        $sql_insert = "INSERT INTO pesanan (no_transaksi, id_akun, layanan, berat, lokasi, alamat, status, biaya_layanan, tanggal) 
                       VALUES (?, ?, 'Jemput', ?, ?, ?, 'Menunggu Penjemputan', ?, '$tanggal')";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ssissi", $no_transaksi, $id_akun, $berat, $lokasi, $alamat, $BiayaLayanan);

        if (!$stmt_insert->execute()) {
            echo "Gagal insert: " . $stmt_insert->error;
            exit;
        }
        $stmt_insert->close();

        $_SESSION['toast_type'] = 'success';
        $_SESSION['toast_message'] = 'Permintaan penjemputan berhasil!';
        $_SESSION['show_toast'] = true;

        echo "<script>window.location.href='?page=pesanan';</script>";
        exit;
    }
}

$tampildata = ob_get_clean();
