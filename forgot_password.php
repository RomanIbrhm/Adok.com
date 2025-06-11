<?php
// forgot_password.php
require_once "config.php"; // Include your database connection

// Initialize variables for messages
$message = '';
$message_type = '';

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'emailempty') {
        $message = 'Please enter your email address.';
        $message_type = 'danger';
    } elseif ($_GET['status'] == 'emailsent') {
        $message = 'If an account with that email exists, a password reset link has been sent.';
        $message_type = 'success';
    } elseif ($_GET['status'] == 'invalidtoken') {
        $message = 'Invalid or expired password reset link. Please try again.';
        $message_type = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Singgak Car Rental</title>
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
        .form-signin input[type="email"] {
            margin-bottom: 10px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
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
        <form action="send_reset_link.php" method="POST">
            <h1 class="h3 mb-3 fw-normal">Forgot Your Password?</h1>
            <p class="text-muted">Enter your email address and we'll send you a link to reset your password.</p>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="floatingInput" name="email" placeholder="name@example.com" required autofocus>
                <label for="floatingInput">Email address</label>
            </div>
            <button class="w-100 btn btn-lg btn-primary" type="submit">Send Reset Link</button>
            <p class="mt-5 mb-3 text-muted">&copy; 2025 Singgak</p>
        </form>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>