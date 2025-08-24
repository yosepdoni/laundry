<?php
function tampilDataRiwayatTransaksi($conn, $id_akun)
{
    $output = '';

    $sql_pesanan = "SELECT id_riwayat_transaksi, id_akun, no_transaksi, deskripsi, pembayaran, tanggal_bayar FROM riwayat_transaksi WHERE id_akun = ?";
    $stmt_pesanan = $conn->prepare($sql_pesanan);
    $stmt_pesanan->bind_param("s", $id_akun);
    $stmt_pesanan->execute();
    $result = $stmt_pesanan->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $output .= '
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-sm-2 fw-bold">No Transaksi:</div>
                            <div class="col-sm-9">' . htmlspecialchars($row['no_transaksi']) . '</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-2 fw-bold">Deskripsi:</div>
                            <div class="col-sm-9">' . htmlspecialchars($row['deskripsi']) . '</div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2 fw-bold">Pembayaran:</div>
                            <div class="col-sm-9">Rp' . number_format($row['pembayaran']) . '</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-2 fw-bold">Tanggal Bayar:</div>
                            <div class="col-sm-9">' . htmlspecialchars(date("d-m-Y", strtotime($row['tanggal_bayar'])))  . '</div>
                        </div>
                    </div>
                </div>
            ';
        }
    } else {
        $output .= '<div class="alert alert-info text-center">Belum ada transaksi.</div>';
    }

    $stmt_pesanan->close();
    return $output;
}
