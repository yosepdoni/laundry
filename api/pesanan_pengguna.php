
<?php

include_once "../db/config.php"; // koneksi database kamu
header('Content-Type: application/json');

$no_transaksi = $_GET['no_transaksi'] ?? null;

if (!$no_transaksi) {
    http_response_code(400);
    echo json_encode(["error" => "Parameter no_transaksi diperlukan"]);
    exit;
}

// ======================= FUNGSI HELPER =========================
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

function hitungBiayaJarak($jarakKm)
{
    $tarifDasar = 10000;
    $tarifPerKm = 5000;
    if ($jarakKm <= 1) return $tarifDasar;
    if ($jarakKm <= 5) return round($tarifDasar + $tarifPerKm * ($jarakKm - 1));
    return null;
}

// ======================= AMBIL DATA PESANAN =========================
$sql = "SELECT p.no_transaksi, p.id_akun, p.berat, p.status, p.layanan, p.tanggal, u.lokasi, u.alamat
        FROM pesanan p
        LEFT JOIN pengguna u ON p.id_akun = u.id_akun
        WHERE p.no_transaksi = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $no_transaksi);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $berat = (float) $row['berat'];
    $layanan = strtolower(trim($row['layanan']));
    $tanggal = date("d-m-Y/ H:i:s", strtotime($row['tanggal']));
    $lokasi = $row['lokasi'] ?? '-';
    $alamat = $row['alamat'] ?? '-';
    $originLat = -0.00154;
    $originLon = 110.88970;
    $tarifPerKg = 15000;

    $biayaBerat = $berat > 0 ? $berat * $tarifPerKg : 0;

    $coords = explode(',', $lokasi);
    $latUser = $lonUser = null;

    if (count($coords) === 2) {
        $latUser = floatval(trim($coords[0]));
        $lonUser = floatval(trim($coords[1]));
    }

    $jarakKm = $biayaJarak = null;

    if ($latUser && $lonUser) {
        $jarakKm = hitungJarak($originLat, $originLon, $latUser, $lonUser);
        $biayaJarak = hitungBiayaJarak($jarakKm);

        if (is_numeric($biayaJarak) && in_array($layanan, ['antar', 'jemput'])) {
            $biayaJarak = round($biayaJarak / 2);
        }
    }

    $totalBiaya = ($layanan === 'tidak ada' || !is_numeric($biayaJarak))
        ? $biayaBerat
        : $biayaBerat + $biayaJarak;

    // ======================= INSERT KE RIWAYAT_TRANSAKSI (JIKA SUCCESS) =========================
    if (strtolower($row['status']) === 'success') {
        $cekStmt = $conn->prepare("SELECT 1 FROM riwayat_transaksi WHERE no_transaksi = ?");
        $cekStmt->bind_param("s", $no_transaksi);
        $cekStmt->execute();
        $cekStmt->store_result();

        if ($cekStmt->num_rows === 0) {
            $deskripsi = "Berat: {$berat} Kg\nStatus: {$row['status']}\nLayanan: {$layanan}\nAlamat: {$alamat}\nTanggal/Waktu: {$tanggal}";

            $insertStmt = $conn->prepare("INSERT INTO riwayat_transaksi (id_akun, no_transaksi, deskripsi, pembayaran) VALUES (?, ?, ?, ?)");
            $insertStmt->bind_param("sssi", $row['id_akun'], $no_transaksi, $deskripsi, $totalBiaya);

            if ($insertStmt->execute()) {
                $hapusStmt = $conn->prepare("DELETE FROM pesanan WHERE no_transaksi = ? AND status = 'success'");
                $hapusStmt->bind_param("s", $no_transaksi);
                $hapusStmt->execute();
                $hapusStmt->close();
            }
            $insertStmt->close();
        }

        $cekStmt->close();
    }

    // ======================= RETURN JSON =========================
    echo json_encode([
        "pengguna" => [
            "no_transaksi" => $no_transaksi,
            "berat" => $berat . " Kg",
            "status" => $row['status'],
            "layanan" => $layanan,
            "tanggal" => $tanggal,
            "lokasi" => $lokasi,
            "alamat" => $alamat,
            "jarak" => $jarakKm !== null ? number_format($jarakKm, 2) . " km" : "-",
            "biaya_berat" => "Rp" . number_format($biayaBerat, 0, ',', '.'),
            "biaya_jarak" => $biayaJarak !== null ? "Rp" . number_format($biayaJarak, 0, ',', '.') : "-",
            "total_biaya" => "Rp" . number_format($totalBiaya, 0, ',', '.'),
        ]
    ]);
} else {
    http_response_code(404);
    echo json_encode([
        "error" => true,
        "status_code" => 404,
        "message" => "Data tidak ditemukan untuk no_transaksi: $no_transaksi"
    ]);
}
