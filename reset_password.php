<?php
// reset_password.php
require_once "config.php";

$message = '';
$message_type = '';
$token_valid = false;
$token = $_GET['token'] ?? '';

if (!empty($token)) {
    $sql = "SELECT id FROM users WHERE reset_token = ? AND reset_token_expires_at > NOW()";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $token_valid = true;
        } else {
            $message = 'Invalid or expired password reset link.';
            $message_type = 'danger';
        }
        $stmt->close();
    }
} else {
    header("location: forgot_password.php?status=invalidtoken");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $token_valid) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $submitted_token = $_POST['token']; // Get token from hidden field

    if (empty($new_password) || empty($confirm_password)) {
        $message = 'Please fill in all fields.';
        $message_type = 'danger';
    } elseif ($new_password !== $confirm_password) {
        $message = 'New password and confirm password do not match.';
        $message_type = 'danger';
    } elseif (strlen($new_password) < 8) {
        $message = 'Password must be at least 8 characters long.';
        $message_type = 'danger';
    } else {
        // Re-validate token to prevent double-use or race conditions
        $sql_recheck = "SELECT id FROM users WHERE reset_token = ? AND reset_token_expires_at > NOW()";
        if ($stmt_recheck = $conn->prepare($sql_recheck)) {
            $stmt_recheck->bind_param("s", $submitted_token);
            $stmt_recheck->execute();
            $stmt_recheck->store_result();

            if ($stmt_recheck->num_rows == 1) {
                $stmt_recheck->bind_result($user_id_to_update);
                $stmt_recheck->fetch();
                $stmt_recheck->close();

                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                // Update password and clear reset token
                $update_sql = "UPDATE users SET password_hash = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id = ?";
                if ($update_stmt = $conn->prepare($update_sql)) {
                    $update_stmt->bind_param("si", $new_password_hash, $user_id_to_update);
                    if ($update_stmt->execute()) {
                        $message = 'Your password has been successfully reset. You can now log in with your new password.';
                        $message_type = 'success';
                        $token_valid = false; // Invalidate form after successful reset
                    } else {
                        $message = 'Error updating password. Please try again.';
                        $message_type = 'danger';
                    }
                    $update_stmt->close();
                }
            } else {
                $message = 'Invalid or expired password reset link. Please try again.';
                $message_type = 'danger';
                $token_valid = false; // Invalidate form if token is no longer valid
            }
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Singgak Car Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bs-primary-rgb: 245, 183, 84;
            --bs-dark-rgb: 22, 22, 22;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        .form-signin {
            width: 100%;
            max-width: 420px;
            padding: 15px;
            margin: auto;
        }
        .form-signin .form-floating:focus-within {
            z-index: 2;
        }
        .btn-primary {
            background-color: #f5b754;
            border-color: #f5b754;
            color: #161616;
            padding: 12px 35px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #e4a94a;
            border-color: #e4a94a;
        }
    </style>
</head>
<body class="text-center d-flex align-items-center min-vh-100">
    <main class="form-signin">
        <h1 class="h3 mb-3 fw-normal">Set New Password</h1>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($token_valid): ?>
            <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="newPassword" name="new_password" placeholder="New Password" required>
                    <label for="newPassword">New Password</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="confirmPassword" name="confirm_password" placeholder="Confirm New Password" required>
                    <label for="confirmPassword">Confirm New Password</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">Reset Password</button>
            </form>
        <?php else: ?>
            <p class="mt-4 text-muted">If you believe this is an error, please return to the <a href="forgot_password.php">forgot password page</a> to request a new link.</p>
        <?php endif; ?>
        
        <p class="mt-5 mb-3 text-muted">&copy; 2025 Singgak</p>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>