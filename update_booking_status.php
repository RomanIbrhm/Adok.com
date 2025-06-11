<?php
// update_booking_status.php

session_start();
// Hanya admin yang bisa mengakses
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    header('location: login.html');
    exit;
}

require_once "config.php";

if (isset($_GET['id']) && isset($_GET['action'])) {
    $booking_id = (int)$_GET['id'];
    $action = $_GET['action']; // Ini akan berisi 'confirm' atau 'reject'

    $new_status = '';
    // Menentukan status baru berdasarkan aksi admin
    if ($action == 'confirm') {
        $new_status = 'confirmed';
    } elseif ($action == 'reject') {
        $new_status = 'rejected';
    } else {
        die("Invalid action.");
    }

    // Memulai transaksi untuk memastikan semua update berhasil atau tidak sama sekali
    $conn->begin_transaction();

    try {
        // 1. Update status di tabel bookings
        $stmt_update = $conn->prepare("UPDATE bookings SET booking_status = ? WHERE id = ?");
        $stmt_update->bind_param("si", $new_status, $booking_id);
        $stmt_update->execute();
        $stmt_update->close();

        // Ambil car_id dari booking untuk update status mobil
        $stmt_get_car = $conn->prepare("SELECT car_id FROM bookings WHERE id = ?");
        $stmt_get_car->bind_param("i", $booking_id);
        $stmt_get_car->execute();
        $result = $stmt_get_car->get_result();
        if ($result->num_rows === 0) {
            throw new Exception("Booking tidak ditemukan.");
        }
        $car_id = $result->fetch_assoc()['car_id'];
        $stmt_get_car->close();

        // 2. Logika untuk status mobil dan pembayaran
        if ($new_status == 'confirmed') {
            // Jika dikonfirmasi, ubah status mobil menjadi 'rented'
            $stmt_car_status = $conn->prepare("UPDATE cars SET status = 'rented' WHERE id = ?");
            $stmt_car_status->bind_param("i", $car_id);
            $stmt_car_status->execute();
            $stmt_car_status->close();

            // --- BAGIAN PENTING YANG DIPERBAIKI ---
            // Juga update status pembayaran menjadi 'successful'
            $stmt_payment = $conn->prepare("UPDATE payments SET transaction_status = 'successful' WHERE booking_id = ?");
            $stmt_payment->bind_param("i", $booking_id);
            $stmt_payment->execute();
            $stmt_payment->close();

        } elseif ($new_status == 'rejected') {
            // Jika ditolak, kembalikan status mobil menjadi 'available'
            $stmt_car_status = $conn->prepare("UPDATE cars SET status = 'available' WHERE id = ?");
            $stmt_car_status->bind_param("i", $car_id);
            $stmt_car_status->execute();
            $stmt_car_status->close();
            
            // Opsional: Update status pembayaran menjadi 'failed' atau 'cancelled'
            $stmt_payment = $conn->prepare("UPDATE payments SET transaction_status = 'failed' WHERE booking_id = ?");
            $stmt_payment->bind_param("i", $booking_id);
            $stmt_payment->execute();
            $stmt_payment->close();
        }

        // Jika semua query berhasil, commit transaksi
        $conn->commit();
        header("Location: admin_dashboard.php?status=success");

    } catch (Exception $e) {
        // Jika ada kesalahan, batalkan semua perubahan (rollback)
        $conn->rollback();
        die("Terjadi kesalahan saat memperbarui data: " . $e->getMessage());
    }

} else {
    header("Location: admin_dashboard.php");
}
$conn->close();
?>