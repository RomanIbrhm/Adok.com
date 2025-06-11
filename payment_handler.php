<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: login.html");
    exit;
}

require_once "config.php";

$booking_id = (int)$_POST['booking_id'];
$payment_method = $_POST['payment_method']; // 'Card' atau 'Cash'

// Pastikan booking ID yang diproses adalah yang ada di sesi
if ($booking_id !== $_SESSION['processing_booking_id']) {
    die("Error: Booking ID tidak cocok. Silakan coba lagi.");
}

$conn->begin_transaction();
try {
    $booking_status = '';
    $transaction_status = '';
    
    // Tentukan status berdasarkan metode pembayaran
    if ($payment_method == 'Card') {
        $booking_status = 'pending'; // Menunggu konfirmasi admin
        $transaction_status = 'successful';
    } elseif ($payment_method == 'Cash') {
        $booking_status = 'pending'; // Menunggu konfirmasi admin juga, tapi status pembayaran beda
        $transaction_status = 'pending'; // Akan dibayar nanti
    } else {
        throw new Exception("Metode pembayaran tidak valid.");
    }

    // 1. Update status booking
    $stmt1 = $conn->prepare("UPDATE bookings SET booking_status = ? WHERE id = ?");
    $stmt1->bind_param("si", $booking_status, $booking_id);
    $stmt1->execute();
    $stmt1->close();

    // 2. Hapus payment record lama jika ada (untuk idempotency)
    $stmt_del = $conn->prepare("DELETE FROM payments WHERE booking_id = ?");
    $stmt_del->bind_param("i", $booking_id);
    $stmt_del->execute();
    $stmt_del->close();

    // 3. Masukkan data pembayaran baru
    $amount = (float)$_POST['amount'];
    $stmt2 = $conn->prepare("INSERT INTO payments (booking_id, amount, payment_method, transaction_status) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("idss", $booking_id, $amount, $payment_method, $transaction_status);
    $stmt2->execute();
    $stmt2->close();
    
    // Jika semua berhasil, commit
    $conn->commit();
    
    // Hapus sesi dan arahkan ke halaman struk
    unset($_SESSION['processing_booking_id']);
    header("Location: transaction_receipt.php?booking_id=" . $booking_id);
    exit();

} catch (Exception $e) {
    $conn->rollback();
    die("Terjadi kesalahan: " . $e->getMessage());
}
?>