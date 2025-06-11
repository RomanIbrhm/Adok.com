<?php
session_start();
// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak. Anda bukan admin.");
}

require_once "config.php";

// Cek apakah ID pengguna ada di URL dan bukan string kosong
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: admin_users.php");
    exit();
}

$user_id_to_delete = (int)$_GET['id'];

// Memulai transaksi
$conn->begin_transaction();

try {
    // LANGKAH 1: Dapatkan semua ID pesanan (booking_id) dan ID mobil (car_id) yang terkait dengan pengguna ini.
    // Kita perlu info ini untuk langkah selanjutnya.
    $sql_get_info = "SELECT id, car_id FROM bookings WHERE user_id = ?";
    $stmt_get_info = $conn->prepare($sql_get_info);
    $stmt_get_info->bind_param("i", $user_id_to_delete);
    $stmt_get_info->execute();
    $result = $stmt_get_info->get_result();
    
    $booking_ids = [];
    $car_ids_to_update = [];
    while ($row = $result->fetch_assoc()) {
        $booking_ids[] = $row['id'];
        $car_ids_to_update[] = $row['car_id'];
    }
    $stmt_get_info->close();

    // LANGKAH 2: Hapus semua data pembayaran (payments) yang terkait dengan pesanan pengguna.
    // Ini harus dilakukan pertama kali karena 'payments' bergantung pada 'bookings'.
    if (!empty($booking_ids)) {
        $placeholders = implode(',', array_fill(0, count($booking_ids), '?'));
        $types = str_repeat('i', count($booking_ids));
        
        $sql_delete_payments = "DELETE FROM payments WHERE booking_id IN ($placeholders)";
        $stmt_delete_payments = $conn->prepare($sql_delete_payments);
        $stmt_delete_payments->bind_param($types, ...$booking_ids);
        $stmt_delete_payments->execute();
        $stmt_delete_payments->close();
    }

    // LANGKAH 3: Sekarang hapus data pesanan (bookings) milik pengguna.
    // Ini aman dilakukan setelah data pembayaran dihapus.
    $sql_delete_bookings = "DELETE FROM bookings WHERE user_id = ?";
    $stmt_delete_bookings = $conn->prepare($sql_delete_bookings);
    $stmt_delete_bookings->bind_param("i", $user_id_to_delete);
    $stmt_delete_bookings->execute();
    $stmt_delete_bookings->close();

    // LANGKAH 4: Hapus pengguna itu sendiri dari tabel 'users'.
    // Ini aman dilakukan setelah data pesanan dihapus.
    $sql_delete_user = "DELETE FROM users WHERE id = ?";
    $stmt_delete_user = $conn->prepare($sql_delete_user);
    $stmt_delete_user->bind_param("i", $user_id_to_delete);
    $stmt_delete_user->execute();
    $stmt_delete_user->close();

    // LANGKAH 5: Terakhir, perbarui status mobil yang tadinya disewa menjadi 'available'.
    // Ini adalah operasi terpisah dan tidak akan memblokir penghapusan.
    if (!empty($car_ids_to_update)) {
        $unique_car_ids = array_unique($car_ids_to_update);
        $car_placeholders = implode(',', array_fill(0, count($unique_car_ids), '?'));
        $car_types = str_repeat('i', count($unique_car_ids));

        $sql_update_cars = "UPDATE cars SET status = 'available' WHERE id IN ($car_placeholders)";
        $stmt_update_cars = $conn->prepare($sql_update_cars);
        $stmt_update_cars->bind_param($car_types, ...$unique_car_ids);
        $stmt_update_cars->execute();
        $stmt_update_cars->close();
    }

    // Jika semua langkah di atas berhasil, simpan semua perubahan ke database.
    $conn->commit();
    header("location: admin_users.php?status=delete_success");

} catch (Exception $e) {
    // Jika salah satu langkah gagal, batalkan SEMUA perubahan yang sudah dilakukan.
    $conn->rollback();
    // Arahkan kembali dengan pesan error.
    header("location: admin_users.php?status=delete_error");
} finally {
    // Selalu tutup koneksi di akhir.
    $conn->close();
}

exit();
?>