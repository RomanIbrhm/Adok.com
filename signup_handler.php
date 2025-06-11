<?php   
require 'config.php'; // Pastikan file config.php ada di folder yang sama

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Ambil data (pastikan 'name' di HTML cocok dengan kunci di $_POST)
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);

    // 2. Validasi
    if (empty($full_name) || empty($email) || empty($password) || empty($password_confirm)) {
        header("Location: signup_page.php?error=emptyfields");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: signup_page.php?error=invalidemail");
        exit();
    }
    if ($password !== $password_confirm) {
        header("Location: signup_page.php?error=passwordcheck");
        exit();
    }
    if (strlen($password) < 8) {
        header("Location: signup_page.php?error=passwordshort");
        exit();
    }

    // 3. Cek Email duplikat
    $sql_check = "SELECT id FROM users WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        header("Location: signup_page.php?error=emailtaken");
        $stmt_check->close();
        exit();
    }
    $stmt_check->close();
    
    // 4. Hash Password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 5. Masukkan ke Database
    $sql_insert = "INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("sss", $full_name, $email, $password_hash);

    if ($stmt_insert->execute()) {
        header("Location: login.php?signup=success");
        exit();
    } else {
        header("Location: signup_page.php?error=sqlerror");
        exit();
    }

} else {
    header("Location: signup_page.php");
    exit();
}
?>