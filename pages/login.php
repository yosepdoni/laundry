<?php
if (!defined('APP')) die('Akses ditolak!');

if (isset($_POST['login'])) {
    $no_hp = $_POST['no_hp'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($no_hp && $password) {
        // Cek apakah akun ada
        $query = mysqli_query($conn, "SELECT * FROM akun WHERE no_hp='$no_hp'");
        if (mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_assoc($query);

            // Verifikasi password
            if (password_verify($password, $data['sandi'])) {
                include_once 'sesi.php';
                $_SESSION['toast_type'] = 'success';
                $_SESSION['toast_message'] = 'Login berhasil. Selamat datang!';
                $_SESSION['show_toast'] = true;

                // Redirect berdasarkan status
                if ($data['status'] === 'admin') {
                    echo "<script>window.location.href='/laundry/admin';</script>";
                } else {
                    echo "<script>window.location.href='/laundry';</script>";
                }
                exit;
            } else {
                $_SESSION['toast_type'] = 'error';
                $_SESSION['toast_message'] = 'Password salah!';
                $_SESSION['show_toast'] = true;
                echo "<script>window.location.href='?page=login';</script>";
                exit;
            }
        } else {
            $_SESSION['toast_type'] = 'error';
            $_SESSION['toast_message'] = 'Nomor HP tidak ditemukan!';
            $_SESSION['show_toast'] = true;
            echo "<script>window.location.href='?page=login';</script>";
            exit;
        }
    } else {
        $_SESSION['toast_type'] = 'error';
        $_SESSION['toast_message'] = 'Semua field harus diisi!';
        $_SESSION['show_toast'] = true;
        echo "<script>window.location.href='?page=login';</script>";
        exit;
    }
}
?>


<div class="container mt-5 pt-5">
    <div class="row mt-5 align-items-center">
        <div class="col-md-6 mb-4 mb-md-0 text-center">
            <img src="./assets/images/login.jpg" class="img-fluid" alt="Login Image">
        </div>
        <div class="col-md-6 border border-grey shadow-lg p-4 mb-5 bg-body rounded">
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
                    <input type="password" name="password" class="form-control" id="password" required>
                </div>
                <div class="row">
                    <div class="col">
                        <a href="?page=register" class="text-decoration-none fst-italic lh-lg">Belum punya akun? Daftar sekarang!</a>
                    </div>
                    <div class="col text-end">
                        <button type="submit" name="login" class="btn btn-primary">
                            <i class="bi bi-box-arrow-in-right"></i> Login
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

