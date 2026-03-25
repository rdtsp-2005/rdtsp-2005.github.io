<?php
/**
 * send_contact.php
 * Handles the Contact Us form submission.
 * Sends the visitor's message to the store email via PHPMailer.
 */

require_once __DIR__ . '/../PHPMailer/Exception.php';
require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailException;

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../contact.php');
    exit();
}

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$message = trim($_POST['message'] ?? '');

// Basic validation
if (empty($name) || empty($email) || empty($message)) {
    header('Location: ../contact.php?status=error&msg=' . urlencode('Please fill in all fields.'));
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../contact.php?status=error&msg=' . urlencode('Please enter a valid email address.'));
    exit();
}

// ── Send email via PHPMailer ─────────────────────────────
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

    // Bypass SSL cert verification on localhost
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true,
        ],
    ];

    // Who the email goes to (your store inbox)
    $mail->setFrom('dulaj.testing@gmail.com', 'Giftora by NSBM — Contact Form');
    $mail->addAddress('dulaj.testing@gmail.com', 'Giftora Support');

    // Reply-To set to the visitor so you can reply directly
    $mail->addReplyTo($email, $name);

    $mail->isHTML(true);
    $mail->Subject = "New Contact Message from {$name} — Giftora";

    $safeMessage = nl2br(htmlspecialchars($message));
    $mail->Body  = "
        <div style='font-family:Arial,sans-serif;max-width:550px;margin:auto;padding:28px;
                    background:#f5f4ff;border-radius:14px;border:1px solid #e5e7eb;'>
            <h2 style='color:#7c3aed;margin-bottom:6px;'>📬 New Contact Message</h2>
            <p style='color:#6b7280;font-size:12px;margin-bottom:20px;'>via Giftora by NSBM contact form</p>
            <table style='width:100%;border-collapse:collapse;font-size:14px;'>
                <tr>
                    <td style='padding:10px 14px;background:#ede9fe;border-radius:8px 8px 0 0;
                               font-weight:600;color:#4c1d95;width:90px;'>From</td>
                    <td style='padding:10px 14px;background:#fff;border-radius:0 8px 0 0;
                               color:#1e1b4b;'>" . htmlspecialchars($name) . "</td>
                </tr>
                <tr>
                    <td style='padding:10px 14px;background:#ede9fe;font-weight:600;color:#4c1d95;'>Email</td>
                    <td style='padding:10px 14px;background:#fff;color:#1e1b4b;'>
                        <a href='mailto:{$email}' style='color:#7c3aed;'>{$email}</a>
                    </td>
                </tr>
                <tr>
                    <td style='padding:10px 14px;background:#ede9fe;border-radius:0 0 0 8px;
                               font-weight:600;color:#4c1d95;vertical-align:top;'>Message</td>
                    <td style='padding:10px 14px;background:#fff;border-radius:0 0 8px 0;
                               color:#374151;line-height:1.6;'>{$safeMessage}</td>
                </tr>
            </table>
            <p style='color:#9ca3af;font-size:11px;margin-top:20px;'>
                Hit Reply to respond directly to {$name}.
            </p>
        </div>";

    $mail->AltBody = "From: {$name} <{$email}>\n\nMessage:\n{$message}";
    $mail->send();

    header('Location: ../contact.php?status=success');
    exit();

} catch (MailException $e) {
    $errMsg = urlencode('Message could not be sent. Please try again later. Error: ' . $mail->ErrorInfo);
    header('Location: ../contact.php?status=error&msg=' . $errMsg);
    exit();
}
