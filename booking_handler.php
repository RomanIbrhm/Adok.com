<?php
// booking_handler.php
session_start();

if (!isset($_SESSION['loggedin']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: login.html");
    exit;
}

require_once "config.php";

// Ambil data dari form
$user_id = (int)$_POST['user_id'];
$car_id = (int)$_POST['car_id'];
$start_date_str = $_POST['start_date'];
$end_date_str = $_POST['end_date'];

// (Validasi dan kalkulasi harga tetap sama)
$start_date = new DateTime($start_date_str);
$end_date = new DateTime($end_date_str);
if ($end_date <= $start_date) die("Error: Tanggal tidak valid.");
$interval = $end_date->diff($start_date);
$rental_days = $interval->days > 0 ? $interval->days : 1;
$stmt_price = $conn->prepare("SELECT price_per_day FROM cars WHERE id = ?");
$stmt_price->bind_param("i", $car_id);
$stmt_price->execute();
$car_price_per_day = $stmt_price->get_result()->fetch_assoc()['price_per_day'];
$stmt_price->close();
$rental_fee = $rental_days * $car_price_per_day;
$insurance_fee = 150;
$taxes_fee = $rental_fee * 0.10;
$total_price = $rental_fee + $insurance_fee + $taxes_fee;

// --- PERUBAHAN UTAMA DI SINI ---

// 1. Simpan booking ke database dengan status 'awaiting_payment'
$sql_booking = "INSERT INTO bookings (user_id, car_id, start_date, end_date, total_price, booking_status) VALUES (?, ?, ?, ?, ?, 'awaiting_payment')";
$stmt_booking = $conn->prepare($sql_booking);
$stmt_booking->bind_param("iissd", $user_id, $car_id, $start_date_str, $end_date_str, $total_price);

if ($stmt_booking->execute()) {
    $last_booking_id = $stmt_booking->insert_id;
    $_SESSION['processing_booking_id'] = $last_booking_id;
    
    // Alihkan pengguna ke halaman pembayaran
    header("Location: payment.php");
    exit();

} else {
    echo "Error: Tidak dapat memproses pesanan Anda. " . $stmt_booking->error;
}

$stmt_booking->close();
$conn->close();
?>