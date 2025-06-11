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
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']);
    $price_per_day = trim($_POST['price_per_day']);
    $seater = trim($_POST['seater']);
    $transmission = trim($_POST['transmission']);
    $fuel_type = trim($_POST['fuel_type']);
    $image_url = trim($_POST['image_url']);
    $status = 'available'; // Status default untuk mobil baru

    // Validasi sederhana: pastikan tidak ada yang kosong
    if (empty($brand) || empty($model) || empty($price_per_day) || empty($image_url)) {
        die("Error: Semua field wajib diisi.");
    }

    // Siapkan query SQL untuk memasukkan data
    $sql = "INSERT INTO cars (brand, model, price_per_day, seater, transmission, fuel_type, image_url, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variabel ke statement sebagai parameter
        // s = string, i = integer, d = double
        $stmt->bind_param("ssdissss", $brand, $model, $price_per_day, $seater, $transmission, $fuel_type, $image_url, $status);

        // Eksekusi statement
        if ($stmt->execute()) {
            // Jika berhasil, redirect ke halaman vehicle management dengan pesan sukses
            header("location: admin_vehicle.php?status=add_success");
            exit();
        } else {
            echo "Error: Terjadi kesalahan. Gagal menyimpan data.";
        }

        // Tutup statement
        $stmt->close();
    }
    
    // Tutup koneksi
    $conn->close();

} else {
    // Jika bukan metode POST, redirect ke halaman form
    header("location: admin_add_vehicle.php");
    exit();
}
?>