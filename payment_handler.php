<?php
// RENTAL/payment_handler.php

session_start();

if (!isset($_SESSION['loggedin']) || $_SERVER["REQUEST_METHOD"] != "POST" || !isset($_SESSION['pending_booking'])) {
    header("location: login.php");
    exit;
}

require_once "config.php";

// --- AMBIL DETAIL DARI SESSION DAN FORM ---
$booking_details = $_SESSION['pending_booking'];
$user_id = $booking_details['user_id'];
$car_id = $booking_details['car_id'];
$start_date = $booking_details['start_date'];
$end_date = $booking_details['end_date'];
$total_price = $booking_details['total_price'];
$pickup_location = $booking_details['pickup_location'];
$sim_image_url = $booking_details['sim_image_url'];
$phone_number = $booking_details['phone_number']; // Ambil nomor telepon dari session

$payment_method = $_POST['payment_method']; 

$conn->begin_transaction();

try {
    $booking_status = 'pending';
    $transaction_status = ($payment_method == 'Card') ? 'successful' : 'pending';

    // 1. BUAT CATATAN BOOKING BARU DI DATABASE
    $sql_booking = "INSERT INTO bookings (user_id, car_id, start_date, end_date, total_price, booking_status, pickup_location, sim_image_url, phone_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_booking = $conn->prepare($sql_booking);
    
    // Update bind_param menjadi "iissdssss" untuk 3 string baru
    $stmt_booking->bind_param("iissdssss", $user_id, $car_id, $start_date, $end_date, $total_price, $booking_status, $pickup_location, $sim_image_url, $phone_number);
    $stmt_booking->execute();
    
    $last_booking_id = $stmt_booking->insert_id;
    $stmt_booking->close();

    // 2. BUAT CATATAN PAYMENT BARU DI DATABASE
    $sql_payment = "INSERT INTO payments (booking_id, amount, payment_method, transaction_status) VALUES (?, ?, ?, ?)";
    $stmt_payment = $conn->prepare($sql_payment);
    $stmt_payment->bind_param("idss", $last_booking_id, $total_price, $payment_method, $transaction_status);
    $stmt_payment->execute();
    $stmt_payment->close();
    
    $conn->commit();
    
    unset($_SESSION['pending_booking']);
    
    header("Location: transaction_receipt.php?booking_id=" . $last_booking_id);
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Terjadi kesalahan saat memproses pesanan Anda. Silakan coba lagi. Error: " . $e->getMessage());
} finally {
    $conn->close();
}
?>