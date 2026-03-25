<?php require "php/functions.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/style_main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Thank You! – Giftora by NSBM</title>
</head>
<body id="index">
    <?php include "files/header.php"; ?>

    <main>
        <div class="container" style="text-align: center; padding: 50px;">
            <div class="card" style="background: white; color: #333; padding: 40px; border-radius: 15px; display: inline-block;">
                <h1 style="color: #28a745;">🎉 Purchase Complete!</h1>
                <p style="font-size: 1.2rem; margin: 20px 0;">
                    Thank you, <strong><?php echo htmlspecialchars($_GET['name'] ?? 'Customer'); ?></strong>!
                </p>
                <p>Your order has been placed successfully and is being processed.</p>

                
                <div style="margin-top: 30px;">
                    <a href="index.php" class="buy-now-btn" style="text-decoration: none;">Return to Shop</a>
                </div>
            </div>
        </div>
    </main>

    <?php include "files/footer.php"; ?>

    <script src="javascript/script.js"></script>
    <script src="javascript/nav.js"></script>
</body>
</html>