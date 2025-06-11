<?php
session_start();
// Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['loggedin']) || $_SESSION['user_role'] !== 'admin') {
    die("Akses ditolak. Anda bukan admin.");
}

require_once "config.php";

// Cek apakah metode request adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil data dari form dan bersihkan
    $id = (int)$_POST['id'];
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']);
    $price_per_day = trim($_POST['price_per_day']);
    $seater = trim($_POST['seater']);
    $transmission = trim($_POST['transmission']);
    $fuel_type = trim($_POST['fuel_type']);
    $status = trim($_POST['status']);
    $image_url = trim($_POST['image_url']);
    
    // Validasi sederhana
    if (empty($brand) || empty($model) || empty($price_per_day) || empty($image_url) || $id === 0) {
        die("Error: Semua field wajib diisi dan ID mobil harus valid.");
    }

    // Siapkan query SQL untuk memperbarui data (UPDATE)
    $sql = "UPDATE cars SET 
                brand = ?, 
                model = ?, 
                price_per_day = ?, 
                seater = ?, 
                transmission = ?, 
                fuel_type = ?, 
                image_url = ?, 
                status = ?
            WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variabel ke statement sebagai parameter
        $stmt->bind_param("ssdissssi", $brand, $model, $price_per_day, $seater, $transmission, $fuel_type, $image_url, $status, $id);

        // Eksekusi statement
        if ($stmt->execute()) {
            // Jika berhasil, redirect ke halaman vehicle management dengan pesan sukses
            header("location: admin_vehicle.php?status=edit_success");
            exit();
        } else {
            echo "Error: Terjadi kesalahan. Gagal memperbarui data.";
        }
        $stmt->close();
    }
    $conn->close();

} else {
    // Jika bukan metode POST, redirect ke halaman utama admin
    header("location: admin_dashboard.php");
    exit();
}
?>