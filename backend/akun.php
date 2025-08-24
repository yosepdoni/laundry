<?php
ob_start();

$idAkun = $_SESSION['id_akun'] ?? null;
$no_hp = $_SESSION['no_hp'] ?? '-';

if ($no_hp !== '-' && strlen($no_hp) >= 4) {
    $last_digits = substr($no_hp, -4);
    $hidden_part = str_repeat('*', strlen($no_hp) - 4);
    $no_hp_masked = $hidden_part . $last_digits;
} else {
    $no_hp_masked = $no_hp;
}

if (!$idAkun) {
    die("Session tidak ditemukan. Silakan login ulang.");
}


// Ambil data pengguna
$query = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_akun = '$idAkun'");
$dataPengguna = mysqli_fetch_assoc($query);

$nama   = $dataPengguna['nama'] ?? '';
$lokasi = $dataPengguna['lokasi'] ?? '';
$alamat = $dataPengguna['alamat'] ?? '';

// Proses simpan
if (isset($_POST['simpan'])) {
    $namaBaru   = mysqli_real_escape_string($conn, $_POST['nama']);
    $lokasiBaru = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $alamatBaru = mysqli_real_escape_string($conn, $_POST['alamat']);

    // Validasi: semua field harus diisi
    if (
        empty(trim($namaBaru)) ||
        empty(trim($lokasiBaru)) ||
        empty(trim($alamatBaru))
    ) {
        $_SESSION['toast_type'] = 'error';
        $_SESSION['toast_message'] = 'Semua field wajib diisi!';
        $_SESSION['show_toast'] = true;
        echo "<script>window.location.href='?page=akun';</script>";
        exit;
    }

    // Proses update
    $update = mysqli_query($conn, "UPDATE pengguna SET 
        nama = '$namaBaru', 
        lokasi = '$lokasiBaru', 
        alamat = '$alamatBaru' 
        WHERE id_akun = '$idAkun'
    ");

    if ($update) {
        $_SESSION['toast_type'] = 'success';
        $_SESSION['toast_message'] = 'Akun berhasil diperbaharui.';
        $_SESSION['show_toast'] = true;
    } else {
        $_SESSION['toast_type'] = 'error';
        $_SESSION['toast_message'] = 'Gagal memperbaharui akun!';
        $_SESSION['show_toast'] = true;
    }

    echo "<script>window.location.href='?page=akun';</script>";
    exit;
}
$akun = ob_get_clean();
?>
