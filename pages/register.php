<?php
if (!defined('APP')) die('Akses ditolak!');

if (isset($_POST['regis'])) {
    $no_hp = $_POST['no_hp'] ?? '';
    $password = $_POST['sandi'] ?? '';

      // Bersihkan dan validasi nomor HP
    $no_hp = preg_replace('/[^0-9]/', '', $no_hp); // hanya angka
    if (substr($no_hp, 0, 1) === '0') {
        $no_hp = substr($no_hp, 1); // buang angka 0 di awal
    }

    if ($no_hp && $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah no_hp sudah ada
        $cekUser = mysqli_query($conn, "SELECT * FROM akun WHERE no_hp = '$no_hp'");
        
        if (mysqli_num_rows($cekUser) > 0) {
            $dataUser = mysqli_fetch_assoc($cekUser);
            $id_akun = $dataUser['id_akun'];
            $sandiLama = $dataUser['sandi'];

            if (!empty($sandiLama)) {
                // Jika no_hp sudah ada dan password juga sudah diisi sebelumnya
                $_SESSION['toast_type'] = 'error';
                $_SESSION['toast_message'] = 'Nomor HP sudah terdaftar!';
                $_SESSION['show_toast'] = true;
                echo "<script>window.location.href='?page=register';</script>";
                exit;
            } else {
                // Jika no_hp ada tapi sandi belum pernah diset (NULL atau kosong)
                $update = mysqli_query($conn, "UPDATE akun SET sandi = '$hashed_password' WHERE id_akun = '$id_akun'");

                if ($update) {
                    $_SESSION['toast_type'] = 'success';
                    $_SESSION['toast_message'] = 'Registrasi berhasil, silakan login!';
                    $_SESSION['show_toast'] = true;
                    echo "<script>window.location.href='?page=login';</script>";
                    exit;
                } else {
                    $_SESSION['toast_type'] = 'error';
                    $_SESSION['toast_message'] = 'Gagal memperbarui sandi!';
                    $_SESSION['show_toast'] = true;
                    echo "<script>window.location.href='?page=register';</script>";
                    exit;
                }
            }
        }

        // Jika belum ada: buat akun dan pengguna baru
        $tanggal = date('dmy');
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM akun WHERE id_akun LIKE 'TRE$tanggal%'");
        $data = mysqli_fetch_assoc($result);
        $urutan = $data['total'] + 1;
        $id_akun = 'TRE' . $tanggal . $urutan;

        $insertUser = mysqli_query($conn, "INSERT INTO akun (id_akun, no_hp, sandi) VALUES ('$id_akun', '$no_hp', '$hashed_password')");

        if ($insertUser) {
            $insertPengguna = mysqli_query($conn, "INSERT INTO pengguna (id_akun) VALUES ('$id_akun')");

            if ($insertPengguna) {
                $_SESSION['toast_type'] = 'success';
                $_SESSION['toast_message'] = 'Registrasi berhasil, Silahkan Login!';
                $_SESSION['show_toast'] = true;
                echo "<script>window.location.href='?page=login';</script>";
                exit;
            } else {
                $_SESSION['toast_type'] = 'error';
                $_SESSION['toast_message'] = 'Gagal menyimpan data pengguna!';
                $_SESSION['show_toast'] = true;
                echo "<script>window.location.href='?page=register';</script>";
                exit;
            }
        } else {
            $_SESSION['toast_type'] = 'error';
            $_SESSION['toast_message'] = 'Registrasi gagal, mohon coba lagi!';
            $_SESSION['show_toast'] = true;
            echo "<script>window.location.href='?page=register';</script>";
            exit;
        }
    } else {
        $_SESSION['toast_type'] = 'error';
        $_SESSION['toast_message'] = 'Semua field harus diisi!';
        $_SESSION['show_toast'] = true;
        echo "<script>window.location.href='?page=register';</script>";
        exit;
    }
}
?>


<div class="container mt-5 pt-5">
    <div class="row mt-5 align-items-center">
        <div class="col-md-6 order-1 order-md-2 text-center mb-4 mb-md-0">
            <img src="./assets/images/registrasi.jpg" class="img-fluid" alt="Register Image">
        </div>

        <div class="col-md-6 order-2 order-md-1 border border-grey shadow-lg p-4 mb-5 bg-body rounded">
            <form method="POST">
                <div class="mb-3">
                    <label for="no_hp" class="form-label">Hp</label>
                    <div class="input-group input-group-merge">
                        <span class="input-group-text">ID (+62)</span>
                        <input type="number" name="no_hp" class="form-control" id="no_hp" aria-describedby="no_hpHelp" required>
                    </div>
                    <div id="no_hpHelp" class="form-text">Kami tidak akan pernah membagikan nomor Hp Anda kepada orang lain.</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Sandi</label>
                    <input type="password" name="sandi" class="form-control" id="password" required>
                </div>
                <div class="row">
                    <div class="col">
                        <a href="?page=login" class="text-decoration-none fst-italic lh-lg">Sudah punya akun? Login sekarang!</a>
                    </div>
                    <div class="col text-end">
                        <button name="regis" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Registrasi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    const noHpInput = document.getElementById('no_hp');

    noHpInput.addEventListener('input', function () {
        let value = this.value;

        // Pastikan hanya angka
        value = value.replace(/[^0-9]/g, '');

        // Jika angka pertama adalah 0, hapus
        if (value.startsWith('0')) {
            value = value.slice(1);
        }

        this.value = value;
    });
</script>

