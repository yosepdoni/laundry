
<?php $akun ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<style>
    #map {
        height: 200px;
        margin-top: 20px;
        margin-bottom: 70px;
    }
</style>
<div class="container mt-5">
    <form method="POST" class="border border-secondary shadow-lg p-5 bg-body rounded" id="formPengaturan">
        <h3 class="mb-4"><i class="bi bi bi-gear fs-2"> Pengaturan Akun</i></h3>
        <div class="row g-4">
            <!-- Kolom Gambar -->
            <div class="col py-5 text-center">
                <img src="./assets/images/akun.jpg" class="img-fluid rounded" alt="Gambar Profil" style="max-height: 350px;">
            </div>

            <!-- Kolom Form Input -->
            <div class="col-md-7 text-sm">
                <div class="mb-3">
                    <label class="form-label">No Hp</label>
                    <div class="input-group">
                        <div class="input-group input-group-merge">
                            <span class="input-group-text">ID (+62)</span>
                            <input type="text" id="noHpInput" class="form-control" value="<?= htmlspecialchars($no_hp_masked) ?>" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="toggleNoHp">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control form-data" name="nama" value="<?= htmlspecialchars($nama) ?>" disabled required>
                </div>
                <div class="mb-3">

                </div>
                <div class="mb-3">
                    <div class="row">
                        <label class="form-label">Lokasi</label>
                        <div class="col">
                            <input type="text" id="alamatTujuan" class="form-control form-data" name="lokasi" value="<?= htmlspecialchars($lokasi) ?>" readonly required>
                        </div>
                        <div class="col-auto">
                            <button id="btnTentukan" class="form-conrol form-data btn btn-primary mb-3" disabled required><i class="bi bi-geo-fill"></i></button>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea class="form-control form-data" name="alamat" rows="3" disabled required><?= htmlspecialchars($alamat) ?></textarea>
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-secondary" id="btnEdit">
                        <i class="bi bi-pencil-square fs-5 me-1"></i> Edit
                    </button>
                    <button type="submit" name="simpan" class="btn btn-primary d-none" id="btnSimpan">
                        <i class="bi bi-save fs-5 me-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </form>
    <div class="row">
        <div class="col">
            <div id="map" class="rounded border"></div>
        </div>
    </div>
</div>
<script>
    const toggleBtn = document.getElementById('toggleNoHp');
    const input = document.getElementById('noHpInput');
    const eyeIcon = document.getElementById('eyeIcon');

    const noHpAsli = <?= json_encode($no_hp) ?>;
    const noHpMasked = <?= json_encode($no_hp_masked) ?>;

    let visible = false;

    toggleBtn.addEventListener('click', () => {
        visible = !visible;
        input.value = visible ? noHpAsli : noHpMasked;
        eyeIcon.className = visible ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
</script>
<script>
    // Saat tombol Edit diklik
    document.getElementById('btnEdit').addEventListener('click', function() {
        // Aktifkan semua input yang punya class "form-data"
        document.querySelectorAll('.form-data').forEach(function(input) {
            input.removeAttribute('disabled');
        });

        // Sembunyikan tombol Edit dan tampilkan tombol Simpan
        document.getElementById('btnEdit').classList.add('d-none');
        document.getElementById('btnSimpan').classList.remove('d-none');
    });
</script>

<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

<script>
    // Ambil nilai dari input
    const lokasiInput = document.getElementById('alamatTujuan').value.trim();
    let map;
    let marker;

    // Cek apakah input mengandung koordinat
    if (lokasiInput && lokasiInput.includes(',')) {
        const [lat, lon] = lokasiInput.split(',').map(Number);
        const koordinat = L.latLng(lat, lon);

        // Inisialisasi peta ke lokasi yang tersimpan
        map = L.map('map').setView(koordinat, 15);

        // Tampilkan peta
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Tambahkan marker
        marker = L.marker(koordinat).addTo(map).bindPopup("Lokasi yang tersimpan").openPopup();

    } else {
        // Jika tidak ada koordinat, tampilkan peta Indonesia
        map = L.map('map').setView([-2.5, 118], 5);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
    }

    // Event klik pada peta untuk menambahkan atau memindahkan marker
    // map.on('click', function(e) {
    //     const lat = e.latlng.lat.toFixed(5);
    //     const lon = e.latlng.lng.toFixed(5);
    //     const latlng = L.latLng(lat, lon);

    //     document.getElementById('alamatTujuan').value = `${lat}, ${lon}`;

    //     if (marker) {
    //         marker.setLatLng(latlng).setPopupContent("Lokasi yang dipilih").openPopup();
    //     } else {
    //         marker = L.marker(latlng).addTo(map).bindPopup("Lokasi yang dipilih").openPopup();
    //     }
    // });

    // Tombol untuk menentukan lokasi dari posisi perangkat
    document.getElementById('btnTentukan').addEventListener('click', function(e) {
        e.preventDefault(); // Cegah reload form jika tombol dalam form

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude.toFixed(5);
                const lon = position.coords.longitude.toFixed(5);
                const latlng = L.latLng(lat, lon);

                // Setel input dengan posisi sekarang
                document.getElementById('alamatTujuan').value = `${lat}, ${lon}`;

                // Tambahkan atau pindahkan marker
                if (marker) {
                    marker.setLatLng(latlng).setPopupContent("Lokasi Anda Sekarang").openPopup();
                } else {
                    marker = L.marker(latlng).addTo(map).bindPopup("Lokasi Anda Sekarang").openPopup();
                }

                // Fokuskan peta
                map.setView(latlng, 15);
            }, function(error) {
                alert("Gagal mendapatkan lokasi: " + error.message);
            });
        } else {
            alert("Browser tidak mendukung Geolocation");
        }
    });
</script>