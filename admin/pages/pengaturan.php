<?php
if (!defined('APP')) die('Akses ditolak!');

// Ambil data pengaturan (jika ada)
$pengaturan = mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1");
$data = mysqli_fetch_assoc($pengaturan); // bisa null jika belum ada data

if (isset($_POST['simpan_pengaturan'])) {
    $biaya_kg = $_POST['biaya_kg'];
    $biaya_km = $_POST['biaya_km'];
    $biaya_lebih_km = $_POST['biaya_lebih_km'];
    $lokasi = $_POST['lokasi'];
    $alamat = $_POST['alamat'];

    $cek = mysqli_query($conn, "SELECT id_pengaturan FROM pengaturan LIMIT 1");

    if (mysqli_num_rows($cek) > 0) {
        $query = "UPDATE pengaturan SET 
                    biaya_kg='$biaya_kg', 
                    biaya_km='$biaya_km', 
                    biaya_lebih_km='$biaya_lebih_km', 
                    lokasi='$lokasi', 
                    alamat='$alamat' 
                  WHERE id_pengaturan=1";
    } else {
        $query = "INSERT INTO pengaturan (id_pengaturan, biaya_kg, biaya_km, biaya_lebih_km, lokasi, alamat) 
                  VALUES (1, '$biaya_kg', '$biaya_km', '$biaya_lebih_km', '$lokasi', '$alamat')";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Pengaturan berhasil disimpan!'); window.location.href='?page=pengaturan';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan pengaturan');</script>";
    }
}
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h5 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Beranda/</span> Pengaturan</h5>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <hr class="my-0" />
                    <div class="card-body">
                        <form id="pengaturan" method="POST">
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="biaya_kg" class="form-label">Biaya/ Kg</label>
                                    <input type="number" class="form-control" name="biaya_kg"  placeholder="Biaya perkilo gram" value="<?= $data['biaya_kg'] ?? '' ?>" autofocus required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="lokasi" class="form-label">Lokasi</label>
                                    <input class="form-control" type="text" name="lokasi" id="lokasi" placeholder="Lat, Lon" value="<?= $data['lokasi'] ?? '' ?>" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="biaya_km" class="form-label">Biaya/ Km</label>
                                    <input class="form-control" type="number" name="biaya_km" id="biaya_km" placeholder="Biaya 1 kilo meter" value="<?= $data['biaya_km'] ?? '' ?>" required />
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="biaya_lebih_km">Biaya Lebih 1Km</label>
                                    <input class="form-control" type="number" name="biaya_lebih_km" id="biaya_lebih_km" placeholder="Biaya lebih dari 1km" value="<?= $data['biaya_lebih_km'] ?? '' ?>" required />
                                </div>
                                <div class="mb-3 col-md-12">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="alamat" name="alamat" placeholder="Alamat" rows="3" required><?= $data['alamat'] ?? '' ?></textarea>
                                </div>
                                <div class="mt-2 text-end">
                                    <button type="reset" class="btn btn-outline-secondary">Batal</button>
                                    <button type="submit" name="simpan_pengaturan" class="btn btn-primary me-2">Simpan</button>
                                </div>
                            </div>
                        </form>


                    </div>
                    <!-- /Account -->
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->

<script>
document.addEventListener("DOMContentLoaded", function () {
    const inputs = ["biaya_kg", "biaya_km", "biaya_lebih_km"];
    
    inputs.forEach(function(id) {
        const input = document.querySelector(`[name="${id}"]`);
        if (!input) return;

        // Format saat diketik
        input.addEventListener("input", function () {
            let angka = input.value.replace(/[^\d]/g, ""); // hanya angka
            if (!angka) angka = "0";
            input.value = parseInt(angka).toLocaleString("id-ID"); // jadi 1.000 dst
        });

        // Format awal jika sudah ada nilai
        if (input.value) {
            let angkaAwal = input.value.replace(/[^\d]/g, "");
            input.value = parseInt(angkaAwal).toLocaleString("id-ID");
        }
    });

    // Hapus titik sebelum submit
    document.getElementById("pengaturan").addEventListener("submit", function () {
        inputs.forEach(function(id) {
            const input = document.querySelector(`[name="${id}"]`);
            input.value = input.value.replace(/\./g, ""); // hapus titik
        });
    });
});
</script>
