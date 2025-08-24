<?php
// Hapus semua data sesi
session_start();
session_destroy();
header("Location:./?page=login");
?>