<?php
// login_handler.php

session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validasi dasar
    if (empty($email) || empty($password)) {
        // Mengarahkan ke login.php dengan error 'empty_fields'
        header("location: login.php?error=empty_fields");
        exit();
    }

    // --- LOGIKA LOGIN GABUNGAN ---

    // 1. Cek kredensial admin
    $admin_email = 'admin@singgak.com';
    $admin_password = 'admin123'; 

    if ($email === $admin_email && $password === $admin_password) {
        // Jika cocok, set session sebagai admin
        $_SESSION['loggedin'] = true;
        $_SESSION['user_name'] = 'Admin';
        $_SESSION['user_email'] = $admin_email;
        $_SESSION['user_role'] = 'admin';
        
        // Arahkan ke dashboard admin
        header("location: admin_dashboard.php");
        exit();
    }

    // 2. Jika bukan admin, proses sebagai pengguna biasa
    $sql = "SELECT id, full_name, email, password_hash FROM users WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        
        if ($stmt->execute()) {
            $stmt->store_result();
            
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($id, $full_name, $db_email, $password_hash);
                
                if ($stmt->fetch()) {
                    // Verifikasi password
                    if (password_verify($password, $password_hash)) {
                        // Set session sebagai user
                        $_SESSION['loggedin'] = true;
                        $_SESSION['user_id'] = $id;
                        $_SESSION['user_name'] = $full_name;
                        $_SESSION['user_email'] = $db_email;
                        $_SESSION['user_role'] = 'user';
                        
                        // Arahkan ke dashboard pengguna
                        header("location: dashboard.php");
                        exit();
                    }
                }
            }
        }
        // Jika email tidak ditemukan atau password salah, arahkan kembali dengan error
        header("location: login.php?error=invalid_credentials");
        exit();
        
        $stmt->close();
    }
    $conn->close();

} else {
    // Jika file diakses langsung, arahkan ke halaman login
    header("location: login.php");
    exit();
}
?>