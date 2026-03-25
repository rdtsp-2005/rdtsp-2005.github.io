<?php require "php/functions.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/style_main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Contact Us | Giftora by NSBM</title>
    <style>
        /* ── Contact Page Specific Styles ── */
        #contact .contact-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        #contact .contact-container > h1 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--clr-text-main);
            margin-bottom: 6px;
            text-align: center;
        }

        #contact .contact-container > p {
            text-align: center;
            color: var(--clr-text-sub);
            font-size: 0.92rem;
            margin-bottom: 32px;
        }

        .contact-box {
            display: grid;
            grid-template-columns: 1fr 1.4fr;
            gap: 30px;
            background: var(--clr-bg-card);
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--clr-border);
            overflow: hidden;
        }

        /* Left info panel */
        .contact-info {
            background: linear-gradient(160deg, var(--clr-primary), #a855f7);
            padding: 36px 30px;
            color: #fff;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .contact-info h3 {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 4px;
            color: #fff;
        }

        .contact-info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.9);
            line-height: 1.5;
        }

        .contact-info-item i {
            font-size: 1rem;
            margin-top: 2px;
            color: #c4b5fd;
            flex-shrink: 0;
        }

        /* Right form panel */
        .contact-form-panel {
            padding: 36px 32px;
        }

        .contact-form-panel .form-group {
            margin-bottom: 18px;
        }

        .contact-form-panel label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--clr-text-sub);
            margin-bottom: 6px;
        }

        .contact-form-panel input,
        .contact-form-panel textarea {
            width: 100%;
            padding: 11px 14px;
            border: 1.5px solid var(--clr-border);
            border-radius: 10px;
            font-size: 0.92rem;
            font-family: var(--font-main);
            color: var(--clr-text-main);
            background: var(--clr-bg-main);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .contact-form-panel input:focus,
        .contact-form-panel textarea:focus {
            border-color: var(--clr-primary);
            box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
        }

        .contact-form-panel textarea {
            min-height: 130px;
            resize: vertical;
        }

        .contact-submit-btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--clr-primary), #a855f7);
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: var(--font-main);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 6px;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 6px 18px rgba(124,58,237,0.3);
        }

        .contact-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(124,58,237,0.4);
        }

        /* Flash messages */
        .flash-success,
        .flash-error {
            padding: 14px 18px;
            border-radius: 12px;
            font-size: 0.92rem;
            font-weight: 500;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }

        .flash-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        @media (max-width: 680px) {
            .contact-box {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body id="contact">
    <?php include "files/header.php" ?>

    <main>
        <div class="contact-container" id="contact">
            <h1>Contact Us</h1>
            <p>We'd love to hear from you! Send us a message and we'll get back to you soon.</p>

            <?php
            // Show feedback flash message from redirect
            $status = $_GET['status'] ?? '';
            $msg    = htmlspecialchars($_GET['msg'] ?? '');

            if ($status === 'success'): ?>
                <div class="flash-success">
                    <i class="fa-solid fa-circle-check"></i>
                    Your message has been sent successfully! We'll get back to you soon.
                </div>
            <?php elseif ($status === 'error'): ?>
                <div class="flash-error">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <?= $msg ?: 'Something went wrong. Please try again.' ?>
                </div>
            <?php endif; ?>

            <div class="contact-box">
                <!-- Left: Contact Info -->
                <div class="contact-info">
                    <h3>Get in Touch</h3>
                    <div class="contact-info-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>NSBM Green University,<br>Pitipana, Homagama,<br>Sri Lanka</span>
                    </div>
                    <div class="contact-info-item">
                        <i class="fa-solid fa-phone"></i>
                        <span>+94 77 123 4567</span>
                    </div>
                    <div class="contact-info-item">
                        <i class="fa-solid fa-envelope"></i>
                        <span>giftora@nsbm.ac.lk</span>
                    </div>
                    <div class="contact-info-item">
                        <i class="fa-solid fa-clock"></i>
                        <span>Mon – Fri, 9 AM – 5 PM</span>
                    </div>
                </div>

                <!-- Right: Contact Form -->
                <div class="contact-form-panel">
                    <form method="POST" action="php/send_contact.php">
                        <div class="form-group">
                            <label for="name"><i class="fa-solid fa-user"></i> Full Name</label>
                            <input type="text" id="name" name="name"
                                   placeholder="Enter your full name"
                                   value="<?= htmlspecialchars($_GET['name'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email"><i class="fa-solid fa-envelope"></i> Email Address</label>
                            <input type="email" id="email" name="email"
                                   placeholder="you@example.com"
                                   value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="message"><i class="fa-solid fa-message"></i> Message</label>
                            <textarea id="message" name="message"
                                      placeholder="Write your message here..." required></textarea>
                        </div>
                        <button type="submit" class="contact-submit-btn">
                            <i class="fa-solid fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include "files/footer.php" ?>
    <script src="javascript/script.js"></script>
    <script src="javascript/nav.js"></script>
</body>
</html>