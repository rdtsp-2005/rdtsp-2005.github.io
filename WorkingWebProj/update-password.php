<?php
require 'config.php';

// Only handle POST submissions
if (!isset($_POST['reset-password-submit'])) {
    header("Location: login_home.php");
    exit();
}

$token        = $_POST['token'] ?? '';
$newPwd       = $_POST['pwd'] ?? '';
$newPwdRepeat = $_POST['pwd-repeat'] ?? '';

// ── Validate inputs ──────────────────────────────────────────
if (empty($token)) {
    header("Location: forgot_password.php");
    exit();
}

if (empty($newPwd) || empty($newPwdRepeat)) {
    $errMsg = urlencode("Please fill in both password fields.");
    header("Location: create_new_password.php?token=$token&error=$errMsg");
    exit();
}

if ($newPwd !== $newPwdRepeat) {
    $errMsg = urlencode("Passwords do not match. Please try again.");
    header("Location: create_new_password.php?token=$token&error=$errMsg");
    exit();
}

if (strlen($newPwd) < 6) {
    $errMsg = urlencode("Password must be at least 6 characters long.");
    header("Location: create_new_password.php?token=$token&error=$errMsg");
    exit();
}

// ── Verify the token is still valid ──────────────────────────
$curTime = time();
$stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at >= ?");
$stmt->bind_param("si", $token, $curTime);
$stmt->execute();
$result = $stmt->get_result();
$row    = $result->fetch_assoc();

if (!$row) {
    $errMsg = urlencode("Reset link has expired or is invalid. Please request a new one.");
    header("Location: create_new_password.php?token=$token&error=$errMsg");
    exit();
}

$tokenEmail = $row['email'];

// ── Update the password ───────────────────────────────────────
$newPwdHash = password_hash($newPwd, PASSWORD_DEFAULT);
$upd = $conn->prepare("UPDATE customers SET password = ? WHERE email = ?");
$upd->bind_param("ss", $newPwdHash, $tokenEmail);
$upd->execute();

// ── Delete the used token ─────────────────────────────────────
$del = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
$del->bind_param("s", $tokenEmail);
$del->execute();

// ── Redirect to login with success message ─────────────────────
header("Location: login_home.php?newpswrd=passwordupdated");
exit();