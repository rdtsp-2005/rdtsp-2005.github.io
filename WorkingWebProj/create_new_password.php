<?php
require 'config.php';

// Ensure table exists
$conn->query("CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at BIGINT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$token = $_GET['token'] ?? null;
$error = '';

if (!$token) {
    $error = 'Invalid request — no reset token provided.';
} else {
    $curTime = time();
    $stmt = $conn->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at >= ?");
    $stmt->bind_param("si", $token, $curTime);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result->fetch_assoc()) {
        $error = 'This link has expired or is invalid. Please request a new password reset.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Password — Giftora by NSBM</title>
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/style_login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="reset-container">
    <div class="lock-icon">🔒</div>
    <h2>Set New Password</h2>
    <p class="reset-subtitle">Choose a strong new password for your account.</p>

    <?php if ($error): ?>
        <div class="error-message">❌ <?= htmlspecialchars($error) ?></div>
        <div class="footer-links">
            <a href="forgot_password.php">← Request a new reset link</a>
        </div>
    <?php else: ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">❌ <?= htmlspecialchars(urldecode($_GET['error'])) ?></div>
        <?php endif; ?>

        <form action="update-password.php" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="form-group">
                <label for="pwd"><i class="fa-solid fa-lock"></i> New Password</label>
                <input type="password" name="pwd" id="pwd" placeholder="Enter a strong password" required minlength="6">
            </div>

            <div class="form-group" style="margin-top:14px;">
                <label for="pwd-repeat"><i class="fa-solid fa-lock"></i> Confirm Password</label>
                <input type="password" name="pwd-repeat" id="pwd-repeat" placeholder="Repeat your new password" required minlength="6">
            </div>

            <button type="submit" name="reset-password-submit" style="margin-top:22px;">
                <i class="fa-solid fa-floppy-disk"></i> Save New Password
            </button>
        </form>

        <div class="footer-links">
            <a href="forgot_password.php">← Request a different reset link</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>