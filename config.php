<?php
// --- Konfigurasi Database ---
$db_host = 'localhost';     // Biasanya 'localhost'
$db_user = 'root';          // User default XAMPP
$db_pass = '';              // Password default XAMPP kosong
$db_name = 'singgak_db'; // <<< NAMA DATABASE SUDAH DIPERBARUI

// --- Membuat Koneksi ---
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// --- Cek Koneksi ---
if ($conn->connect_error) {
    // Jika koneksi gagal, hentikan skrip dan tampilkan pesan error.
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// Set karakter set ke utf8mb4 untuk mendukung berbagai karakter
$conn->set_charset("utf8mb4");

?>