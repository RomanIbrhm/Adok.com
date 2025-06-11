<?php
session_start();
// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak.");
}

require_once "config.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: admin_vehicle.php?status=invalid_id");
    exit();
}

$vehicle_id = (int)$_GET['id'];

// Anda tidak bisa menghapus mobil yang sedang disewa 'rented'
$stmt_check = $conn->prepare("SELECT status FROM cars WHERE id = ?");
$stmt_check->bind_param("i", $vehicle_id);
$stmt_check->execute();
$result = $stmt_check->get_result();
$car = $result->fetch_assoc();
$stmt_check->close();

if ($car && $car['status'] === 'rented') {
    header("location: admin_vehicle.php?status=delete_error_rented");
    exit();
}

// Hapus mobil dari database
$sql = "DELETE FROM cars WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $vehicle_id);
    if ($stmt->execute()) {
        header("location: admin_vehicle.php?status=delete_success");
    } else {
        header("location: admin_vehicle.php?status=delete_error");
    }
    $stmt->close();
}
$conn->close();
exit();
?>