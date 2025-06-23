<?php
// RENTAL/booking_handler.php

session_start();

// Pastikan pengguna sudah login dan metode request adalah POST
if (!isset($_SESSION['loggedin']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: login.php");
    exit;
}

require_once "config.php";

// --- PENGAMBILAN DATA DARI FORM ---
$user_id = (int)$_POST['user_id'];
$car_id = (int)$_POST['car_id'];
$start_date_str = $_POST['start_date'];
$end_date_str = $_POST['end_date'];
$pickup_location = trim($_POST['pickup_location']); // Mengambil data lokasi

// --- VALIDASI DAN KALKULASI HARGA ---
// Validasi tanggal dan lokasi dasar
if (empty($start_date_str) || empty($end_date_str) || empty($pickup_location)) {
    die("Error: Tanggal dan lokasi penjemputan wajib diisi.");
}

$start_date = new DateTime($start_date_str);
$end_date = new DateTime($end_date_str);

// Pastikan tanggal selesai tidak lebih awal dari tanggal mulai
if ($end_date <= $start_date) {
    die("Error: Tanggal selesai harus setelah tanggal mulai.");
}

// Hitung durasi sewa
$interval = $end_date->diff($start_date);
$rental_days = $interval->days > 0 ? $interval->days : 1; // Minimal sewa 1 hari

// Ambil harga mobil dari database untuk kalkulasi
$stmt_price = $conn->prepare("SELECT price_per_day FROM cars WHERE id = ?");
$stmt_price->bind_param("i", $car_id);
$stmt_price->execute();
$result_price = $stmt_price->get_result();
if ($result_price->num_rows === 0) {
    die("Error: Mobil tidak ditemukan.");
}
$car_price_per_day = $result_price->fetch_assoc()['price_per_day'];
$stmt_price->close();

// Kalkulasi total biaya
$rental_fee = $rental_days * $car_price_per_day;
$insurance_fee = 150; // Biaya asuransi tetap
$taxes_fee = $rental_fee * 0.10; // Pajak 10%
$total_price = $rental_fee + $insurance_fee + $taxes_fee;

// --- SIMPAN DETAIL BOOKING (TERMASUK LOKASI) KE SESSION ---
$_SESSION['pending_booking'] = [
    'user_id' => $user_id,
    'car_id' => $car_id,
    'start_date' => $start_date_str,
    'end_date' => $end_date_str,
    'total_price' => $total_price,
    'pickup_location' => $pickup_location // Menyimpan lokasi ke session
];

// Tutup koneksi database
$conn->close();

// Arahkan pengguna ke halaman pembayaran
header("Location: payment.php");
exit();

?>