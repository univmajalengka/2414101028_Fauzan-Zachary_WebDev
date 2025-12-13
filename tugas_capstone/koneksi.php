<?php
$servername = "localhost";
$username = "root"; // Ganti dengan username MariaDB Anda
$password = ""; // Ganti dengan password MariaDB Anda
$dbname = "db_wisata_bandung";

// Membuat Koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek Koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
// echo "Koneksi berhasil"; // Hapus baris ini setelah pengujian
?>