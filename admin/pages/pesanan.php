<?php
if (!defined('APP')) die('Akses ditolak!');
?>
<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h5 class="fw-bold py-3 mb-4"><span>Beranda /</span> Pesanan</h5>

        <div class="card">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span>Pesanan</span>
                <a href="?page=tambah_pesanan" class="btn">+ Data</a>
            </h5>
            <div class="table-responsive text-nowrap">

                <form method="post" action="">
                    <table class="table table-bordered table-hover text-center mb-5">
                        <thead class="table-white">
                            <tr>
                                <th>Id Akun</th>
                                <th>No Transaksi</th>
                                <th>nama</th>
                                <th>Layanan</th>
                                <th>Berat (Kg)</th> <!-- Tambah label berat -->
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $dataPesanan = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY tanggal DESC");
                            while ($row = mysqli_fetch_assoc($dataPesanan)) {
                                $idAkun = $row['id_akun'];
                                $getNama = mysqli_query($conn, "SELECT nama FROM pengguna WHERE id_akun = '$idAkun'");
                                $namaRow = mysqli_fetch_assoc($getNama);
                                $getWa = mysqli_query($conn, "SELECT no_hp FROM akun WHERE id_akun = '$idAkun'");
                                $waRow = mysqli_fetch_assoc($getWa);
                                $noHp = preg_replace('/[^0-9]/', '', $waRow['no_hp']); // bersihkan karakter selain angka


                                // Options untuk dropdown layanan
                                $layananOptions = ['Belum Diketahui', 'Antar', 'Jemput', 'Antar Jemput', 'Tidak Ada'];

                                // Options untuk dropdown status
                                $statusOptions = ['Menunggu Penjemputan', 'Sedang Diproses', 'Selesai', 'Diantar Menuju Alamat', 'sukses'];
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['id_akun']) ?></td>
                                    <td><?= htmlspecialchars($row['no_transaksi']) ?></td>
                                    <td><?= htmlspecialchars($namaRow['nama'] ?? '-') ?></td>
                                    <td>
                                        <select name="layanan[<?= htmlspecialchars($row['no_transaksi']) ?>]" class="form-select">
                                            <?php
                                            foreach ($layananOptions as $opt) {
                                                $selected = ($row['layanan'] === $opt) ? 'selected' : '';
                                                echo "<option value=\"$opt\" $selected>$opt</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input
                                            type="number"
                                            name="berat[<?= htmlspecialchars($row['no_transaksi']) ?>]"
                                            value="<?= htmlspecialchars($row['berat']) ?>"
                                            min="0"
                                            step="0.01"
                                            class="form-control"
                                            required>
                                    </td>
                                    <td>
                                        <select name="status[<?= htmlspecialchars($row['no_transaksi']) ?>]" class="form-select">
                                            <?php
                                            foreach ($statusOptions as $opt) {
                                                $selected = ($row['status'] === $opt) ? 'selected' : '';
                                                echo "<option value=\"$opt\" $selected>$opt</option>";
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td><?= htmlspecialchars(date("d-m-Y H:i:s", strtotime($row['tanggal']))) ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <button class="dropdown-item" type="submit" name="update" value="<?= htmlspecialchars($row['no_transaksi']) ?>" class="btn btn-sm btn-primary">
                                                    <p><i class="bx bx-edit-alt me-1">Perbaharui</i></p>
                                                </button>
                                                <a class="dropdown-item"
                                                    href="https://wa.me/62<?= htmlspecialchars(ltrim($noHp, '0')) ?>?text=<?= urlencode(
                                                                                                                                "Halo pelanggan yang terhormat " . ($namaRow['nama'] ?? 'Pelanggan') . ",\n\n" .
                                                                                                                                    "Terima kasih telah menggunakan layanan Tresia Laundry.\n" .
                                                                                                                                    "Untuk melihat status terbaru dari pesanan Anda, silakan klik tautan berikut:\n\n" .
                                                                                                                                    "https://tresialaundry.shop/tracking.php?no_transaksi=" . $row['no_transaksi'] . "\n\n" .
                                                                                                                                    "Jika ada pertanyaan lebih lanjut, silakan hubungi kami."
                                                                                                                            ) ?>"
                                                    target="_blank">
                                                    <i class="bx bxl-whatsapp me-1"></i> WA
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </form>

                <?php
                // Proses update jika tombol simpan ditekan
                if (isset($_POST['update'])) {
                    $no_transaksi = $_POST['update'];
                    $layananBaru = $_POST['layanan'][$no_transaksi] ?? null;
                    $statusBaru = $_POST['status'][$no_transaksi] ?? null;
                    $beratBaru = $_POST['berat'][$no_transaksi] ?? null;

                    if ($layananBaru !== null && $statusBaru !== null && $beratBaru !== null) {
                        $layananBaru = mysqli_real_escape_string($conn, $layananBaru);
                        $statusBaru = mysqli_real_escape_string($conn, $statusBaru);
                        $beratBaru = floatval($beratBaru);
                        $no_transaksi_safe = mysqli_real_escape_string($conn, $no_transaksi);

                        // ===========================
                        // AMBIL LOKASI & BERAT
                        // ===========================
                        $sqlLokasi = "SELECT lokasi FROM pesanan WHERE no_transaksi = '$no_transaksi_safe'";
                        $resLokasi = mysqli_query($conn, $sqlLokasi);
                        $lokasiRow = mysqli_fetch_assoc($resLokasi);
                        $lokasi = $lokasiRow['lokasi'];


                        function hitungJarak($lat1, $lon1, $lat2, $lon2)
                        {
                            $R = 6371;
                            $dLat = deg2rad($lat2 - $lat1);
                            $dLon = deg2rad($lon2 - $lon1);
                            $a = sin($dLat / 2) * sin($dLat / 2) +
                                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                                sin($dLon / 2) * sin($dLon / 2);
                            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                            return $R * $c;
                        }

                        // ===========================
                        // AMBIL DATA PENGATURAN
                        // ===========================
                        $sqlPengaturan = "SELECT * FROM pengaturan LIMIT 1";
                        $resPengaturan = mysqli_query($conn, $sqlPengaturan);
                        $pengaturan = mysqli_fetch_assoc($resPengaturan);

                        // Ambil nilai-nilai dari database pengaturan
                        $tarifPerKg = floatval($pengaturan['biaya_kg']);
                        $tarifDasar = floatval($pengaturan['biaya_km']);
                        $tarifPerKm = floatval($pengaturan['biaya_lebih_km']);

                        // Pecah koordinat jadi originLat dan originLon
                        $originLat = $originLon = 0;
                        $koordinatAsal = explode(',', $pengaturan['lokasi']);
                        if (count($koordinatAsal) == 2) {
                            $originLat = floatval(trim($koordinatAsal[0]));
                            $originLon = floatval(trim($koordinatAsal[1]));
                        }


                        function hitungBiayaJarak($jarakKm)
                        {
                            global $tarifDasar, $tarifPerKm;

                            if ($jarakKm <= 1) return $tarifDasar;
                            if ($jarakKm <= 5) return round($tarifDasar + $tarifPerKm * ($jarakKm - 1));
                            return null;
                        }

                        $coords = explode(',', $lokasi);
                        $latUser = $lonUser = null;

                        if (count($coords) == 2) {
                            $latUser = floatval(trim($coords[0]));
                            $lonUser = floatval(trim($coords[1]));
                        }

                        $biayaBerat = $beratBaru > 0 ? $beratBaru * $tarifPerKg : 0;

                        $jarakKm = $biayaJarak = null;

                        if ($latUser && $lonUser) {
                            $jarakKm = hitungJarak($originLat, $originLon, $latUser, $lonUser);
                            $biayaJarak = hitungBiayaJarak($jarakKm);
                            if (is_numeric($biayaJarak)) {
                                if ($layananBaru == 'Antar' || $layananBaru == 'Jemput') {
                                    $biayaJarak = round($biayaJarak / 2);
                                }
                            }
                        }

                        // ===========================
                        // TOTAL BIAYA
                        // ===========================
                        if ($layananBaru == 'Tidak Ada') {
                            $biayaJarak = 0;
                            $totalBiaya = $biayaBerat;
                        } else {
                            $BiayaLayanan = is_numeric($biayaJarak) ? $biayaBerat + $biayaJarak : $biayaBerat;
                            $totalBiaya = $BiayaLayanan;
                        }

                        // ===========================
                        // UPDATE KE TABLE PESANAN
                        // ===========================
                        $updateQuery = "UPDATE pesanan 
                            SET layanan = '$layananBaru',
                                status = '$statusBaru',
                                berat = $beratBaru,
                                jarak = " . ($jarakKm ?? 'NULL') . ", 
                                biaya_layanan = " . ($biayaJarak ?? 'NULL') . ", 
                                biaya_berat = $biayaBerat,
                                total = $totalBiaya
                            WHERE no_transaksi = '$no_transaksi_safe'";

                        if (mysqli_query($conn, $updateQuery)) {
                            echo "<script>
                                alert('Data berhasil diperbarui');
                                window.location.href = '?page=pesanan';
                            </script>";
                        } else {
                            echo "<script>alert('Gagal memperbarui data');</script>";
                        }
                    }
                }
                // ====================================================
                // INSERT KE RIWAYAT_TRANSAKSI JIKA STATUS = 'sukses'
                // ====================================================
                function formatTanggalWaktuIndonesia($datetime)
                {
                    return date('d-m-Y H:i', strtotime($datetime));
                }

                if (isset($_POST['update'])) {
                    $no_transaksi_safe = mysqli_real_escape_string($conn, $_POST['update']);
                    $selectQuery = "SELECT id_akun, status, total, berat, layanan, alamat, tanggal FROM pesanan WHERE no_transaksi = '$no_transaksi_safe'";
                    $resCheck = mysqli_query($conn, $selectQuery);

                    if ($resCheck && $dataCheck = mysqli_fetch_assoc($resCheck)) {
                        if (strtolower($dataCheck['status']) === 'sukses') {
                            $no_transaksi = $no_transaksi_safe;
                            $id_akun = $dataCheck['id_akun'];

                            // Format tanggal dan waktu
                            $tanggalWaktuFormatted = formatTanggalWaktuIndonesia($dataCheck['tanggal']);

                            // Buat deskripsi dari data pesanan
                            $deskripsi = "Berat {$dataCheck['berat']} Kg,\n"
                                . "layanan {$dataCheck['layanan']},\n";

                            // Cek apakah alamat ada dan tidak kosong
                            if (!empty($dataCheck['alamat'])) {
                                $deskripsi .= "alamat {$dataCheck['alamat']},\n";
                            }
                            $deskripsi .= "tanggal dan waktu dipesan $tanggalWaktuFormatted";


                            $totalBiaya = $dataCheck['total'];

                            date_default_timezone_set('Asia/Jakarta');
                            $cekStmt = $conn->prepare("SELECT 1 FROM riwayat_transaksi WHERE no_transaksi = ?");
                            $cekStmt->bind_param("s", $no_transaksi);
                            $cekStmt->execute();
                            $cekStmt->store_result();

                            if ($cekStmt->num_rows === 0) {
                                $tanggal = date('Y-m-d');

                                $insertStmt = $conn->prepare("INSERT INTO riwayat_transaksi (id_akun, no_transaksi, deskripsi, pembayaran, tanggal_bayar) VALUES (?, ?, ?, ?, ?)");
                                $insertStmt->bind_param("sssis", $id_akun, $no_transaksi, $deskripsi, $totalBiaya, $tanggal);


                                if ($insertStmt->execute()) {
                                    // Hapus dari tabel pesanan jika berhasil
                                    $hapusStmt = $conn->prepare("DELETE FROM pesanan WHERE no_transaksi = ? AND status = 'sukses'");
                                    $hapusStmt->bind_param("s", $no_transaksi);
                                    $hapusStmt->execute();
                                    $hapusStmt->close();
                                }
                                $insertStmt->close();
                            }
                            $cekStmt->close();
                        }
                    }
                }

                ?>

            </div>
        </div>
        <!--/ Bootstrap Table with Header Dark -->
    </div>
    <!-- / Content -->

    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->