<?php
session_start();
require_once "config.php";

// Pastikan pengguna login dan request adalah POST
if (!isset($_SESSION['loggedin']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header('location: login.php');
    exit;
}

// Validasi input
if (!isset($_POST['booking_id'], $_POST['car_id'], $_POST['rating']) || empty($_POST['booking_id']) || empty($_POST['car_id']) || empty($_POST['rating'])) {
    header("location: history.php?status=review_failed");
    exit();
}

$user_id = $_SESSION['user_id'];
$booking_id = (int)$_POST['booking_id'];
$car_id = (int)$_POST['car_id'];
$rating = (int)$_POST['rating'];
$review_text = trim($_POST['review_text']);

// Ambil sumber halaman dari hidden input
$source_page = $_POST['source'] ?? 'history'; // Default ke 'history' jika tidak ada

// Validasi rating antara 1 dan 5
if ($rating < 1 || $rating > 5) {
    header("location: " . $source_page . ".php?status=review_failed");
    exit();
}

// Cek apakah pengguna sudah pernah memberikan review untuk booking ini
$sql_check = "SELECT id FROM reviews WHERE booking_id = ? AND user_id = ?";
if($stmt_check = $conn->prepare($sql_check)){
    $stmt_check->bind_param("ii", $booking_id, $user_id);
    $stmt_check->execute();
    $stmt_check->store_result();
    if($stmt_check->num_rows > 0){
        $stmt_check->close();
        header("location: " . $source_page . ".php?status=already_reviewed");
        exit();
    }
    $stmt_check->close();
}


// Siapkan query untuk memasukkan data review
$sql = "INSERT INTO reviews (booking_id, user_id, car_id, rating, review_text) VALUES (?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("iiiis", $booking_id, $user_id, $car_id, $rating, $review_text);
    
    if ($stmt->execute()) {
        // Jika berhasil, redirect kembali ke halaman sumber dengan pesan sukses
        $redirect_url = ($source_page === 'dashboard') 
            ? 'dashboard.php?status=review_success#my-bookings' 
            : 'history.php?status=review_success';
        header("location: " . $redirect_url);
    } else {
        // Jika gagal, redirect kembali ke halaman sumber dengan pesan gagal
        $redirect_url = ($source_page === 'dashboard') 
            ? 'dashboard.php?status=review_failed#my-bookings' 
            : 'history.php?status=review_failed';
        header("location: " . $redirect_url);
    }
    $stmt->close();
}
$conn->close();
exit();

?>