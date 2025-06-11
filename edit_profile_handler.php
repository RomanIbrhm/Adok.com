<?php
// edit_profile_handler.php
session_start();

if (!isset($_SESSION['loggedin']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    header("location: login.html");
    exit;
}

require_once "config.php";

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// Logika untuk memperbarui profil (nama dan email)
if ($action === 'update_profile') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);

    if (empty($full_name) || empty($email)) {
        header("location: edit_profile.php?status=empty");
        exit();
    }

    $sql = "UPDATE users SET full_name = ?, email = ? WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $full_name, $email, $user_id);
        if ($stmt->execute()) {
            // Perbarui juga session
            $_SESSION['user_name'] = $full_name;
            $_SESSION['user_email'] = $email;
            header("location: edit_profile.php?status=success");
        } else {
            header("location: edit_profile.php?status=error");
        }
        $stmt->close();
    }
}

// Logika untuk mengubah kata sandi
elseif ($action === 'change_password') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

     if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        header("location: edit_profile.php?status=empty");
        exit();
    }

    if ($new_password !== $confirm_password) {
        header("location: edit_profile.php?status=pwdmatch");
        exit();
    }

    // Ambil hash password saat ini dari database
    $sql_pass = "SELECT password_hash FROM users WHERE id = ?";
    if ($stmt_pass = $conn->prepare($sql_pass)) {
        $stmt_pass->bind_param("i", $user_id);
        $stmt_pass->execute();
        $result = $stmt_pass->get_result();
        $user = $result->fetch_assoc();
        $stmt_pass->close();

        // Verifikasi password saat ini
        if (password_verify($current_password, $user['password_hash'])) {
            // Jika cocok, hash password baru dan update
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_update_pass = "UPDATE users SET password_hash = ? WHERE id = ?";
            if ($stmt_update = $conn->prepare($sql_update_pass)) {
                $stmt_update->bind_param("si", $new_password_hash, $user_id);
                if ($stmt_update->execute()) {
                    header("location: edit_profile.php?status=pwdsuccess");
                } else {
                    header("location: edit_profile.php?status=error");
                }
                $stmt_update->close();
            }
        } else {
            // Jika password saat ini salah
            header("location: edit_profile.php?status=pwdcurrent");
        }
    }
}

else {
    header("location: edit_profile.php");
}

$conn->close();
exit();
?>