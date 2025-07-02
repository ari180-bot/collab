<?php
session_start(); // Mulai session
$_SESSION = array(); // Hapus semua variabel sesi
session_destroy(); // Hancurkan sesi

// Redirect ke halaman login
header("Location: login.php");
exit;
?>