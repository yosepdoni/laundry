<?php
if (isset($_SESSION['show_toast']) && $_SESSION['show_toast']) {
    $type = $_SESSION['toast_type'] ?? 'success'; // default success
    $message = $_SESSION['toast_message'] ?? 'Data berhasil diproses!';

    // Hapus session agar toast tidak muncul ulang
    unset($_SESSION['show_toast'], $_SESSION['toast_type'], $_SESSION['toast_message']);

    // Atur ikon dan warna
    $icon = ($type === 'success') ? '<i class="bi bi-info-circle-fill me-2"></i>' : '<i class="bi bi-exclamation-triangle-fill me-2"></i>';
    // warna header untuk toast
    $bgColor = ($type === 'success') ? 'rgb(64, 118, 255)' : 'rgb(146, 13, 26)';
    // warna timer versi transparan, manual ubah jadi rgba dengan alpha 0.3
    $timerColor = ($type === 'success') ? 'rgba(148, 148, 148, 0.58)' : 'rgba(148, 148, 148, 0.58)';
    $title = ($type === 'success') ? 'Informasi' : 'Peringatan';
?>
    <!-- Toast Notification -->
    <div style="position: fixed; top: 80px; right: 10px; z-index: 1055; width: 320px;">
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header" style="background-color: <?= $bgColor ?>; color: white; border-radius: 5px 5px 0 0;">
                <?= $icon ?>
                <strong class="me-auto"><?= $title ?></strong>
                <button type="button" class="btn-close btn-close-white ms-2 mb-1" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" style="background-color: #f8f9fa; border-radius: 0 0 5px 5px; position: relative; padding-bottom: 12px;">
                <?= htmlspecialchars($message) ?>

                <!-- Bar indikator waktu animasi -->
                <div class="toast-timer"></div>
            </div>
        </div>
    </div>

    <style>
        .toast-timer {
            position: absolute;
            bottom: 5px;
            left: 0;
            height: 4px;
            background-color: <?= $timerColor ?>;
            border-radius: 2px;
            animation: shrinkWidth 3s linear forwards;
            width: 100%;
        }

        @keyframes shrinkWidth {
            from {
                width: 100%;
            }

            to {
                width: 0;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastEl = document.querySelector('.toast');
            var toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            toast.show();
        });
    </script>
<?php } ?>