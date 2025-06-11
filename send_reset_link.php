<?php
// send_reset_link.php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        header("location: forgot_password.php?status=emailempty");
        exit();
    }

    // Check if email exists in the database
    $sql = "SELECT id FROM users WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($user_id);
            $stmt->fetch();

            // Generate a unique token
            $token = bin2hex(random_bytes(32)); // 64 character hex string
            $expires = date("Y-m-d H:i:s", strtotime('+1 hour')); // Token valid for 1 hour

            // Store token and expiration in the database
            $update_sql = "UPDATE users SET reset_token = ?, reset_token_expires_at = ? WHERE id = ?";
            if ($update_stmt = $conn->prepare($update_sql)) {
                $update_stmt->bind_param("ssi", $token, $expires, $user_id);
                $update_stmt->execute();
                $update_stmt->close();

                // Construct the reset link
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/RENTAL/reset_password.php?token=" . $token;

                // --- EMAIL SENDING SIMULATION ---
                // In a real application, you would use a library like PHPMailer
                // to send a proper email here.
                $to = $email;
                $subject = "Password Reset Request for Singgak Car Rental";
                $message = "Dear User,\n\n";
                $message .= "You have requested to reset your password for your Singgak Car Rental account.\n";
                $message .= "Please click on the following link to reset your password:\n\n";
                $message .= $reset_link . "\n\n";
                $message .= "This link will expire in 1 hour.\n";
                $message .= "If you did not request a password reset, please ignore this email.\n\n";
                $message .= "Regards,\nSinggak Car Rental Team";
                $headers = "From: no-reply@singgak.com\r\n";
                $headers .= "Reply-To: no-reply@singgak.com\r\n";
                $headers .= "X-Mailer: PHP/" . phpversion();

                // mail($to, $subject, $message, $headers); // Uncomment this line to actually send email (requires mail server config)
                // For demonstration, we'll just redirect directly without actual email sending.
                // In a real app, you would always redirect to a success page regardless of email existence for security reasons.

            }
        }
        $stmt->close();
    }
    $conn->close();

    // Always redirect to a generic success message to prevent email enumeration
    header("location: forgot_password.php?status=emailsent");
    exit();

} else {
    header("location: forgot_password.php");
    exit();
}
?>