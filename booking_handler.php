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
$pickup_time_str = $_POST['pickup_time'];
$end_date_str = $_POST['end_date'];
$pickup_location = trim($_POST['pickup_location']);
$phone_number = trim($_POST['phone_number']); // Ambil nomor telepon

// --- LOGIKA UNTUK MENANGANI UPLOAD FILE SIM ---
$sim_image_path = '';
if (isset($_FILES['sim_upload']) && $_FILES['sim_upload']['error'] == 0) {
    $target_dir = "uploads/sim/";
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $file_extension = strtolower(pathinfo($_FILES['sim_upload']['name'], PATHINFO_EXTENSION));
    $unique_filename = 'sim_' . $user_id . '_' . time() . '.' . $file_extension;
    $target_file = $target_dir . $unique_filename;

    $allowed_types = ['jpg', 'jpeg', 'png'];
    if (in_array($file_extension, $allowed_types)) {
        if (move_uploaded_file($_FILES['sim_upload']['tmp_name'], $target_file)) {
            $sim_image_path = $target_file;
        } else {
            die("Error: Gagal saat memindahkan file SIM yang diunggah.");
        }
    } else {
        die("Error: Format file SIM tidak valid. Harap unggah file JPG, JPEG, atau PNG.");
    }
} else {
    die("Error: File SIM wajib diunggah atau terjadi kesalahan saat mengunggah.");
}


// --- VALIDASI DAN KALKULASI HARGA ---
if (empty($start_date_str) || empty($pickup_time_str) || empty($end_date_str) || empty($pickup_location) || empty($phone_number)) {
    die("Error: Semua field, termasuk nomor telepon, wajib diisi.");
}

$min_time = '08:00';
$max_time = '20:00';
if ($pickup_time_str < $min_time || $pickup_time_str > $max_time) {
    die("Error: Waktu penjemputan harus di antara jam 08:00 dan 20:00.");
}

$start_datetime_str = $start_date_str . ' ' . $pickup_time_str;
$start_date = new DateTime($start_datetime_str);
$end_date = new DateTime($end_date_str);

if ($end_date <= $start_date) {
    die("Error: Tanggal selesai harus setelah tanggal mulai.");
}

$interval = $end_date->diff($start_date);
$rental_days = $interval->days > 0 ? $interval->days : 1;

$stmt_price = $conn->prepare("SELECT price_per_day FROM cars WHERE id = ?");
$stmt_price->bind_param("i", $car_id);
$stmt_price->execute();
$result_price = $stmt_price->get_result();
if ($result_price->num_rows === 0) {
    die("Error: Mobil tidak ditemukan.");
}
$car_price_per_day = $result_price->fetch_assoc()['price_per_day'];
$stmt_price->close();

$rental_fee = $rental_days * $car_price_per_day;
$insurance_fee = 150;
$taxes_fee = $rental_fee * 0.10;
$total_price = $rental_fee + $insurance_fee + $taxes_fee;

// --- SIMPAN DETAIL BOOKING KE SESSION ---
$_SESSION['pending_booking'] = [
    'user_id' => $user_id,
    'car_id' => $car_id,
    'start_date' => $start_datetime_str,
    'end_date' => $end_date_str,
    'total_price' => $total_price,
    'pickup_location' => $pickup_location,
    'sim_image_url' => $sim_image_path,
    'phone_number' => $phone_number // Simpan nomor telepon ke session
];

$conn->close();

header("Location: payment.php");
exit();
?>