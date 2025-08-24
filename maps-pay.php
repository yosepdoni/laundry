<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Tresia Laundry - Estimasi Harga Antar Jemput</title>

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />

  <style>
    #map { height: 300px; margin-top: 20px; }
    body { font-family: Arial, sans-serif; padding: 20px; }
    label, input, button { font-size: 1rem; margin: 5px 0; }
  </style>
</head>
<body>

  <h2>Estimasi Harga Antar Jemput</h2>

  <label for="alamatTujuan">Masukkan Alamat Tujuan:</label><br>
  <input type="text" id="alamatTujuan" placeholder="Alamat pelanggan" size="50" />
  <button onclick="cekJarak()">Hitung Jarak & Harga</button>
  <br />
  <!-- Tombol aktifkan lokasi pelanggan -->
  <button onclick="gunakanLokasiPelanggan()">Gunakan Lokasi Saya (Pelanggan)</button>

  <p id="hasil"></p>

  <div id="map"></div>

  <!-- Leaflet JS -->
  <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>

  <script>
    // Lokasi laundry sebagai origin
    const origin = [0.512079, 108.948760];

    // Inisialisasi peta
    const map = L.map('map').setView(origin, 13);

    // Layer peta
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Marker origin laundry
    const markerOrigin = L.marker(origin).addTo(map).bindPopup("Tresia Laundry").openPopup();

    let markerDestination;
    let routeLine;

    // Fungsi hitung biaya
    function hitungBiaya(jarakKm) {
      const tarifDasar = 10000;
      const tarifPerKm = 5000;
      if (jarakKm <= 1) {
        return tarifDasar;
      } else if (jarakKm <= 5) {
        return tarifDasar + tarifPerKm * (jarakKm - 1);
      } else {
        return 'Di luar jangkauan';
      }
    }

    // Fungsi untuk menggunakan lokasi pelanggan sebagai tujuan
    function gunakanLokasiPelanggan() {
      if (!navigator.geolocation) {
        alert('Geolokasi tidak didukung oleh browser Anda.');
        return;
      }
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          const lat = pos.coords.latitude;
          const lon = pos.coords.longitude;
          const tujuan = [lat, lon];

          // Update input alamat tujuan dengan koordinat (atau bisa kosong, tinggal manual)
          document.getElementById('alamatTujuan').value = `Lat:${lat.toFixed(5)}, Lon:${lon.toFixed(5)}`;

          // Hapus marker tujuan sebelumnya
          if (markerDestination) {
            map.removeLayer(markerDestination);
          }
          // Pasang marker tujuan di lokasi pelanggan
          markerDestination = L.marker(tujuan).addTo(map).bindPopup("Lokasi Pelanggan").openPopup();

          // Zoom peta untuk origin dan tujuan
          const bounds = L.latLngBounds([origin, tujuan]);
          map.fitBounds(bounds, { padding: [50, 50] });

          // Hitung rute dan jarak
          hitungJarakRoute(origin, tujuan);
        },
        (err) => {
          alert('Gagal mendapatkan lokasi: ' + err.message);
        }
      );
    }

    // Fungsi hitung jarak dan route dari origin ke tujuan (koordinat)
    async function hitungJarakRoute(originCoords, destCoords) {
      const routingUrl = `https://router.project-osrm.org/route/v1/driving/${originCoords[1]},${originCoords[0]};${destCoords[1]},${destCoords[0]}?overview=full&geometries=geojson`;
      const routeRes = await fetch(routingUrl);
      const routeData = await routeRes.json();

      if (routeData.code !== "Ok") {
        alert('Gagal mendapatkan rute.');
        return;
      }

      const route = routeData.routes[0];
      const distanceMeter = route.distance;
      const distanceKM = distanceMeter / 1000;

      if (routeLine) {
        map.removeLayer(routeLine);
      }
      routeLine = L.geoJSON(route.geometry).addTo(map);

      const biaya = hitungBiaya(distanceKM);
      document.getElementById('hasil').innerHTML = `
        Jarak antar: <strong>${distanceKM.toFixed(2)} km</strong><br/>
        Estimasi biaya antar jemput: <strong>${biaya === 'Di luar jangkauan' ? biaya : 'Rp ' + biaya.toLocaleString()}</strong>
      `;
    }

    // Fungsi cek jarak dari alamat tujuan (text input), tetap untuk alamat manual
    async function cekJarak() {
      const alamat = document.getElementById('alamatTujuan').value;
      if (!alamat) {
        alert('Masukkan alamat tujuan dulu ya!');
        return;
      }

      // Coba geocoding hanya jika input bukan koordinat lat/lon
      const coordMatch = alamat.match(/Lat:([\d.-]+),\s*Lon:([\d.-]+)/i);
      if (coordMatch) {
        const lat = parseFloat(coordMatch[1]);
        const lon = parseFloat(coordMatch[2]);
        const destLatLng = [lat, lon];

        if (markerDestination) map.removeLayer(markerDestination);
        markerDestination = L.marker(destLatLng).addTo(map).bindPopup("Tujuan").openPopup();

        const bounds = L.latLngBounds([origin, destLatLng]);
        map.fitBounds(bounds, { padding: [50, 50] });

        hitungJarakRoute(origin, destLatLng);
        return;
      }

      // Kalau bukan koordinat, gunakan Nominatim untuk geocoding
      const geocodeUrl = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(alamat)}`;
      const res = await fetch(geocodeUrl);
      const data = await res.json();

      if (data.length === 0) {
        alert('Alamat tidak ditemukan, coba yang lain ya.');
        return;
      }

      const destLatLng = [parseFloat(data[0].lat), parseFloat(data[0].lon)];

      if (markerDestination) map.removeLayer(markerDestination);
      markerDestination = L.marker(destLatLng).addTo(map).bindPopup("Tujuan").openPopup();

      const bounds = L.latLngBounds([origin, destLatLng]);
      map.fitBounds(bounds, { padding: [50, 50] });

      hitungJarakRoute(origin, destLatLng);
    }
  </script>

</body>
</html>
