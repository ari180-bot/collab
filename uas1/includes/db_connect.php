<?php
// Konfigurasi Database MySQL
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', ''); // GANTI DENGAN PASSWORD ROOT MYSQL ANDA! (Kosong jika default XAMPP)
define('DB_NAME', 'uas_travel_db'); // Nama database yang sudah Anda buat

// Membuat koneksi ke database menggunakan MySQLi (objek oriented)
$mysqli = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Cek koneksi
if ($mysqli->connect_error) {
    die("Koneksi database gagal: " . $mysqli->connect_error);
}
?>