<?php
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailException;

$message = "";
$success = false;

// ── Ensure password_resets table exists ──────────────────────
$conn->query("
    CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL,
        token VARCHAR(255) NOT NULL UNIQUE,
        expires_at BIGINT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

if (isset($_POST['reset-request-submit'])) {
    $userEmail = trim($_POST['email']);

    // Check if email exists
    $check = $conn->prepare("SELECT email FROM customers WHERE email = ?");
    $check->bind_param("s", $userEmail);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        // Security: don't reveal if email exists
        $message = "<div class='alert-success'>✅ If that email is registered, a reset link has been sent. Check your inbox.</div>";
        $success  = true;
    } else {
        $token   = bin2hex(random_bytes(32));
        $expires = time() + 3600; // Unix timestamp, 1 hour from now

        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $baseUrl  = $protocol . '://' . $_SERVER['HTTP_HOST'];
        $url      = $baseUrl . '/Shopping-Store/WorkingWebProj/create_new_password.php?token=' . $token;

        // Remove old tokens for this email
        $del = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $del->bind_param("s", $userEmail);
        $del->execute();

        // Insert new token
        $ins = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $ins->bind_param("ssi", $userEmail, $token, $expires);
        $ins->execute();

        // ── Send email ───────────────────────────────────────
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dulaj.testing@gmail.com';
            $mail->Password   = 'zaqeqmisklnyjism';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->CharSet    = 'UTF-8';

            // ✅ Fix: disable SSL certificate verification for localhost dev
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true,
                ],
            ];

            $mail->setFrom('dulaj.testing@gmail.com', 'Giftora by NSBM');
            $mail->addAddress($userEmail);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password — Giftora by NSBM';
            $mail->Body    = "
                <div style='font-family:Arial,sans-serif;max-width:520px;margin:auto;padding:30px;background:#f5f4ff;border-radius:16px;'>
                    <h2 style='color:#7c3aed;margin-bottom:16px;'>&#128274; Password Reset Request</h2>
                    <p style='color:#374151;margin-bottom:24px;line-height:1.6;'>
                        You requested a password reset for your account.<br>
                        Click the button below to set a new password.<br>
                        <strong>This link expires in 1 hour.</strong>
                    </p>
                    <a href='{$url}'
                       style='display:inline-block;padding:13px 28px;background:#7c3aed;color:#fff;
                              border-radius:50px;text-decoration:none;font-weight:700;font-size:15px;'>
                        Reset My Password
                    </a>
                    <p style='color:#9ca3af;font-size:12px;margin-top:24px;'>
                        If you did not request this, you can safely ignore this email.
                    </p>
                    <hr style='border:none;border-top:1px solid #e5e7eb;margin:20px 0;'>
                    <p style='color:#9ca3af;font-size:11px;'>
                        Or copy this link: {$url}
                    </p>
                </div>";
            $mail->AltBody = "Reset your password: {$url}";
            $mail->send();

            $message = "<div class='alert-success'>✅ Reset link sent! Check your inbox (and spam folder).</div>";
            $success  = true;

        } catch (MailException $e) {
            $message = "<div class='error-message'>❌ Email could not be sent.<br><small>" . htmlspecialchars($mail->ErrorInfo) . "</small></div>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Giftora by NSBM</title>
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/style_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="forgot-container">
    <div class="lock-icon">🔑</div>
    <h2>Forgot Password?</h2>
    <p class="reset-subtitle">Enter your email and we'll send you a reset link.</p>

    <?php echo $message; ?>

    <?php if (!$success): ?>
    <form action="forgot_password.php" method="POST">
        <div class="form-group">
            <label for="email"><i class="fa-solid fa-envelope"></i> Email Address</label>
            <input type="email" name="email" id="email" placeholder="you@example.com" required>
        </div>
        <button type="submit" name="reset-request-submit">
            <i class="fa-solid fa-paper-plane"></i> Send Reset Link
        </button>
    </form>
    <?php endif; ?>

    <div class="footer-links">
        <a href="login_home.php">← Back to Login</a>
    </div>
</div>

</body>
</html>
