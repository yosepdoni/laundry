<?php
function tampilDataPesanan($conn, $id_akun)
{
    $output = '';

    $sql_pesanan = "SELECT no_transaksi, berat, status, layanan, tanggal FROM pesanan WHERE id_akun = ? AND status <> 'selesai' AND status <> 'Diantar Menuju Alamat' AND status <> 'sukses'";
    $stmt_pesanan = $conn->prepare($sql_pesanan);
    $stmt_pesanan->bind_param("s", $id_akun);
    $stmt_pesanan->execute();
    $result = $stmt_pesanan->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row['status'] === 'selesai') continue; // Lewatkan data selesai di sini

            $output .= '<tr>';
            $output .= '<td>' . htmlspecialchars($row['no_transaksi']) . '</td>';
            $output .= '<td>' . htmlspecialchars($row['berat'] ?? '-') . '</td>';
            $output .= '<td>' . htmlspecialchars($row['status']) . '</td>';
            $output .= '<td>' . htmlspecialchars($row['layanan']) . '</td>';

            // Format tanggal
            $formattedDate = date("d-m-Y/ H:i:s", strtotime($row['tanggal']));
            $output .= '<td>' . htmlspecialchars($formattedDate) . '</td>';
            $output .= '</tr>';
        }
    } else {
        $output .= '<tr><td colspan="5" class="text-center mt-5">Tidak ada pesanan</td></tr>';
    }

    $stmt_pesanan->close();
    return $output;
}

function getDataPesananSelesai($conn, $id_akun)
{
    $data = [];
    $sql = "SELECT no_transaksi, berat, status, layanan, tanggal, lokasi, alamat,
                   biaya_berat, biaya_layanan, total
            FROM pesanan 
            WHERE id_akun = ? 
              AND (status = 'selesai' OR status = 'Diantar Menuju Alamat' OR status = 'sukses')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id_akun);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $formattedDate = date("d-m-Y / H:i:s", strtotime($row['tanggal']));
        $row['gabungan'] = 
            "Berat: {$row['berat']} Kg\n" .
            "Status: {$row['status']}\n" .
            "Layanan: {$row['layanan']}\n" .
            "Alamat: {$row['alamat']}\n" . 
            "Tanggal/ Waktu: {$formattedDate}";

        $data[] = $row;
    }

    $stmt->close();
    return $data;
}


