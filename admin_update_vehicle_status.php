<?php
session_start();
// Hanya admin yang bisa mengakses
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    header('location: login.html');
    exit;
}

require_once "config.php";

// Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validasi input yang diterima
    if (!isset($_POST['id']) || !isset($_POST['status'])) {
        header('location: admin_vehicle.php?status=update_fail');
        exit;
    }

    $vehicle_id = (int)$_POST['id'];
    $new_status = $_POST['status'];
    $allowed_statuses = ['available', 'rented', 'maintenance'];

    // Pastikan status yang dikirim adalah nilai yang diizinkan
    if (!in_array($new_status, $allowed_statuses)) {
        header('location: admin_vehicle.php?status=invalid_status');
        exit;
    }

    // Siapkan dan eksekusi query UPDATE
    $sql = "UPDATE cars SET status = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $new_status, $vehicle_id);
        
        if ($stmt->execute()) {
            // Jika berhasil, kembali dengan pesan sukses
            header("location: admin_vehicle.php?status=update_success");
        } else {
            // Jika gagal, kembali dengan pesan error
            header("location: admin_vehicle.php?status=update_fail");
        }
        $stmt->close();
    } else {
        header("location: admin_vehicle.php?status=update_fail");
    }

} else {
    // Jika bukan request POST, alihkan ke halaman utama
    header('location: admin_vehicle.php');
}

$conn->close();
exit();
?>