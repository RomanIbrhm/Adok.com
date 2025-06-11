<?php
// RENTAL/payment_handler.php

session_start();
// Pastikan ada data booking yang pending di session
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

$payment_method = $_POST['payment_method']; // 'Card' atau 'Cash'

// --- MULAI TRANSAKSI DATABASE ---
// Transaksi memastikan bahwa data booking dan pembayaran dibuat bersamaan.
// Jika salah satu gagal, keduanya akan dibatalkan (rollback).
$conn->begin_transaction();

try {
    // Tentukan status awal berdasarkan metode pembayaran
    $booking_status = 'pending'; // Status awal selalu 'pending' untuk konfirmasi admin
    $transaction_status = ($payment_method == 'Card') ? 'successful' : 'pending';

    // 1. BUAT CATATAN BOOKING BARU DI DATABASE
    $sql_booking = "INSERT INTO bookings (user_id, car_id, start_date, end_date, total_price, booking_status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_booking = $conn->prepare($sql_booking);
    $stmt_booking->bind_param("iissds", $user_id, $car_id, $start_date, $end_date, $total_price, $booking_status);
    $stmt_booking->execute();
    
    // Ambil ID dari booking yang baru saja dibuat
    $last_booking_id = $stmt_booking->insert_id;
    $stmt_booking->close();

    // 2. BUAT CATATAN PAYMENT BARU DI DATABASE
    $sql_payment = "INSERT INTO payments (booking_id, amount, payment_method, transaction_status) VALUES (?, ?, ?, ?)";
    $stmt_payment = $conn->prepare($sql_payment);
    $stmt_payment->bind_param("idss", $last_booking_id, $total_price, $payment_method, $transaction_status);
    $stmt_payment->execute();
    $stmt_payment->close();
    
    // Jika semua query di atas berhasil, simpan perubahan ke database
    $conn->commit();
    
    // --- PEMBERSIHAN DAN REDIRECT ---
    // Hapus data booking dari session karena sudah berhasil disimpan
    unset($_SESSION['pending_booking']);
    
    // Arahkan pengguna ke halaman struk/kwitansi
    header("Location: transaction_receipt.php?booking_id=" . $last_booking_id);
    exit();

} catch (Exception $e) {
    // Jika terjadi error di salah satu query, batalkan semua perubahan
    $conn->rollback();
    // Tampilkan pesan error (dalam produksi, sebaiknya log error ini)
    die("Terjadi kesalahan saat memproses pesanan Anda. Silakan coba lagi. Error: " . $e->getMessage());
} finally {
    // Selalu tutup koneksi
    $conn->close();
}
?>