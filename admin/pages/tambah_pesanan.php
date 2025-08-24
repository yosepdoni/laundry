<?php
if (!defined('APP')) die('Akses ditolak!');

date_default_timezone_set('Asia/Jakarta');
$tanggal_pesanan = date('Y-m-d H:i:s');

if (isset($_POST['tambah-pesanan-tidak-terdaftar'])) {
    $no_hp = $_POST['no_hp'] ?? '';
    $nama = $_POST['nama'] ?? '';
    $berat = floatval($_POST['berat'] ?? 0);

    if (!empty($no_hp) && !empty($nama) && $berat > 0) {
        // Cek apakah no_hp sudah terdaftar
        $cekNoHp = mysqli_query($conn, "SELECT 1 FROM akun WHERE no_hp = '$no_hp'");
        if (mysqli_num_rows($cekNoHp) > 0) {
            echo "<script>alert('Nomor HP sudah terdaftar!'); window.location.href='?page=tambah_pesanan';</script>";
            exit;
        }

        // Buat id_akun custom: TREddmmyyN
        $tanggal = date('dmy');
        $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM akun WHERE id_akun LIKE 'TRE$tanggal%'");
        $data = mysqli_fetch_assoc($result);
        $urutan = $data['total'] + 1;
        $id_akun = 'TRE' . $tanggal . $urutan;

        // Insert ke tabel akun
        $insertAkun = mysqli_query($conn, "INSERT INTO akun (id_akun, no_hp) VALUES ('$id_akun', '$no_hp')");

        if ($insertAkun) {
            // Insert ke tabel pengguna
            $insertPengguna = mysqli_query($conn, "INSERT INTO pengguna (id_akun, nama) VALUES ('$id_akun', '$nama')");

            if ($insertPengguna) {
                // Buat no_transaksi: TRSyyyymmddhhmmssID
                $no_transaksi = 'TRS' . date('YmdHis') . $id_akun;

                // Insert ke tabel pesanan
                $insertPesanan = mysqli_query($conn, "INSERT INTO pesanan (id_akun, no_transaksi, layanan, berat, status, tanggal) VALUES ('$id_akun', '$no_transaksi', 'Tidak Ada', '$berat', 'Sedang Diproses', '$tanggal_pesanan')");

                if ($insertPesanan) {
                    echo "<script>alert('Pesanan berhasil ditambahkan!'); window.location.href='?page=pesanan';</script>";
                } else {
                    echo "<script>alert('Gagal menyimpan ke tabel pesanan'); window.location.href='?page=pesanan';</script>";
                }
            } else {
                echo "<script>alert('Gagal menyimpan ke tabel pengguna'); window.location.href='?page=pesanan';</script>";
            }
        } else {
            echo "<script>alert('Gagal menyimpan ke tabel akun'); window.location.href='?page=pesanan';</script>";
        }
    } else {
        echo "<script>alert('Semua kolom harus diisi dan berat harus lebih dari 0'); window.location.href='?page=pesanan';</script>";
    }
}

if (isset($_POST['tambah-pesanan-sudah-didaftar'])) {
    $no = 1;
    $query = mysqli_query($conn, "SELECT id_akun FROM pengguna ORDER BY nama ASC");

    while ($row = mysqli_fetch_assoc($query)) {
        $id_akun = $row['id_akun'];
        $beratKey = 'berat' . $no;

        if (isset($_POST[$beratKey]) && floatval($_POST[$beratKey]) > 0) {
            $berat = floatval($_POST[$beratKey]);

            // Buat no_transaksi
            $no_transaksi = 'TRS' . date('YmdHis') . $id_akun;

            // Insert ke pesanan
            $insert = mysqli_query($conn, "INSERT INTO pesanan (id_akun, no_transaksi, layanan, berat, status, tanggal) VALUES ('$id_akun', '$no_transaksi', 'Tidak Ada', '$berat', 'Sedang Diproses', '$tanggal_pesanan')");

            if (!$insert) {
                $_SESSION['error'] = "Gagal menambahkan pesanan untuk ID Akun $id_akun.";
            }
        }

        $no++;
    }

    // Redirect setelah submit
    echo "<script>alert('Pesanan berhasil ditambahkan!'); window.location.href='?page=pesanan';</script>";
    exit;
}

?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h5 class="fw-bold py-3 mb-4"><span>Beranda /</span> <a href="?page=pesanan ">Pesanan</a> / Tambah Data</h5>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <hr class="my-0" />
                    <div class="card-body">
                        <form id="Tambah_Pesanan_Tidak_Terdaftar" method="POST">
                            <div class="row">
                                <h5 class="mb-3">Tambah Pesanan No Hp Tidak Terdaftar</h5>
                                <div class="mb-3 col-md-4">
                                    <label for="no_hp" class="form-label">No Hp</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text bg-dark text-white">ID (+62)</span>&nbsp;
                                        <input type="number" name="no_hp" class="form-control" id="no_hp" autofocus required />
                                    </div>
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input type="text" name="nama" class="form-control" id="nama" required />
                                </div>
                                <div class="mb-3 col-md-2">
                                    <label for="berat" class="form-label">Berat</label>
                                    <input type="number" name="berat" id="berat" value="" min="0" step="0.01" class="form-control" required>
                                </div>

                                <div class="mb-3 col-md-12 text-end">
                                    <label id="biaya-kg-label" class="fs-6">Rp0</label>
                                </div>
                                <div class="mt-2 text-end">
                                    <button type="reset" class="btn btn-primary me-2" style="background-color: red;">Batal</button>
                                    <button type="submit" name="tambah-pesanan-tidak-terdaftar" class="btn btn-primary me-2">Tambah Pesanan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /Account -->
                </div>


                <!-- Tabel dan Form -->
                <div class="card mb-4">
                    <hr class="my-0" />
                    <div class="card-body">
                        <div class="row align-items-center mb-3">
                            <div class="col">
                                <h5 class="mb-0">Tambah Pesanan No Hp Terdaftar</h5>
                            </div>
                            <div class="col-auto">
                                <input type="text" id="cari-nama" class="form-control" placeholder="Cari Nama...">
                            </div>
                        </div>


                        <form id="Tambah_Pesanan_Baru" method="POST">
                            <div class="row">
                                <div class="table-responsive">
                                    <table id="tabel-akun" class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID Akun</th>
                                                <th>Nama</th>
                                                <th>Berat (Kg)</th>
                                                <th>Biaya</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $query = mysqli_query($conn, "
                                                SELECT pengguna.id_akun, pengguna.nama 
                                                FROM pengguna
                                                INNER JOIN akun ON pengguna.id_akun = akun.id_akun
                                                WHERE akun.status != 'admin'
                                                AND TRIM(pengguna.nama) != ''
                                                ORDER BY pengguna.nama ASC
                                            ");
                                            $no = 1;
                                            while ($row = mysqli_fetch_assoc($query)) :
                                                $id_akun = $row['id_akun'];
                                                $nama = htmlspecialchars($row['nama']);
                                            ?>

                                                <tr>
                                                    <td><?= $id_akun ?></td>
                                                    <td class="col-md-4"><?= $nama ?></td>
                                                    <td class="col-md-2">
                                                        <input type="number" name="berat<?= $no ?>" id="berat<?= $no ?>" value="" min="0" step="0.01" class="form-control berat-input" data-no="<?= $no ?>">
                                                        <input type="hidden" name="id_akun" value="<?= $id_akun ?>">
                                                    </td>
                                                    <td class="text-end align-middle">
                                                        <label id="biaya-kg-label<?= $no ?>" class="mb-0">Rp0</label>
                                                    </td>
                                                    <td class="text-end align-middle">
                                                        <button type="submit" name="tambah-pesanan-sudah-didaftar" class="btn btn-primary me-1">Tambah Pesanan</button>
                                                    </td>
                                                </tr>
                                            <?php
                                                $no++;
                                            endwhile;
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- / Content -->

    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
<?php
// Ambil biaya per kg dari tabel pengaturan
$qPengaturan = mysqli_query($conn, "SELECT biaya_kg FROM pengaturan LIMIT 1");
$dataPengaturan = mysqli_fetch_assoc($qPengaturan);
$biayaKg = (int)($dataPengaturan['biaya_kg'] ?? 8000); // fallback 8000 kalau null
?>
<!-- Script jQuery pencarian berdasarkan nama -->
<script>
    $(document).ready(function() {
        $("#cari-nama").on("keyup", function() {
            let keyword = $(this).val().toLowerCase();

            $("#tabel-akun tbody tr").filter(function() {
                let nama = $(this).find("td:eq(1)").text().toLowerCase();
                $(this).toggle(nama.includes(keyword));
            });
        });
    });
</script>

<script>
    document.querySelector('button[type="reset"]').addEventListener('click', function() {
        document.getElementById('Tambah_Pesanan_Tidak_Terdaftar').reset();

        // Kosongkan nilai input
        document.getElementById('no_hp').value = '';
        document.getElementById('nama').value = '';
        document.querySelector('input[name="berat"]').value = '';

        // Reset label biaya juga
        document.getElementById('biaya-kg-label').textContent = 'Rp0';
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const beratInput = document.getElementById("berat");
        const labelBiaya = document.getElementById("biaya-kg-label");

        const tarifPerKg = <?= $biayaKg ?>;

        function updateBiaya() {
            const berat = parseFloat(beratInput.value) || 0;
            const total = berat * tarifPerKg;
            labelBiaya.textContent = `Rp${total.toLocaleString('id-ID')}`;
        }

        beratInput.addEventListener("input", updateBiaya);
    });
</script>


<!-- Script: Hitung biaya otomatis -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const biayaPerKg = <?= $biayaKg ?>;

        // Ambil semua input dengan class "berat-input"
        const inputs = document.querySelectorAll(".berat-input");

        inputs.forEach(function(input) {
            input.addEventListener("input", function() {
                const no = input.dataset.no;
                const berat = parseFloat(input.value) || 0;
                const total = berat * biayaPerKg;
                const label = document.getElementById("biaya-kg-label" + no);
                if (label) {
                    label.textContent = "Rp" + total.toLocaleString("id-ID");
                }
            });
        });
    });
</script>