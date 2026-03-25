<?php require "php/functions.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/style_main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Checkout | Giftora by NSBM</title>
    <style>
        /* ── Checkout Page Styles ── */
        .checkout-wrapper {
            max-width: 620px;
            margin: 40px auto;
        }

        .checkout-card {
            background: var(--clr-bg-card);
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--clr-border);
            padding: 36px 40px;
        }

        .checkout-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--clr-text-main);
            margin-bottom: 8px;
            text-align: center;
        }

        .checkout-subtitle {
            text-align: center;
            color: var(--clr-text-sub);
            font-size: 0.9rem;
            margin-bottom: 32px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--clr-text-main);
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 11px 16px;
            border: 1.5px solid var(--clr-border);
            border-radius: 10px;
            font-size: 0.9rem;
            font-family: var(--font-main);
            color: var(--clr-text-main);
            background: var(--clr-bg-main);
            transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
            outline: none;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--clr-primary);
            box-shadow: 0 0 0 3px rgba(124,58,237,0.12);
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .divider {
            border: none;
            border-top: 1px solid var(--clr-border);
            margin: 28px 0;
        }

        .card-fields-box {
            background: var(--clr-primary-light);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            display: none;
            flex-direction: column;
            gap: 14px;
        }

        .card-fields-box label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--clr-text-main);
            margin-bottom: 5px;
            display: block;
        }

        .card-fields-box input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--clr-border);
            border-radius: 8px;
            font-family: var(--font-main);
            background: #fff;
            font-size: 0.9rem;
            outline: none;
        }

        .card-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .checkout-submit-btn {
            width: 100%;
            padding: 14px;
            background: var(--clr-primary);
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 700;
            font-family: var(--font-main);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background var(--transition-fast), transform var(--transition-fast);
            margin-top: 8px;
        }

        .checkout-submit-btn:hover {
            background: var(--clr-primary-hover);
            transform: translateY(-2px);
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 18px;
            color: var(--clr-text-sub);
            font-size: 0.85rem;
        }

        .back-link a {
            color: var(--clr-primary);
            font-weight: 600;
        }
    </style>
</head>

<body id="checkout">
    <?php include "files/header.php"; ?>

    <main>
        <div class="checkout-wrapper">
            <div class="checkout-card">

                <h1 class="checkout-title">
                    <i class="fa-solid fa-bag-shopping" style="color: var(--clr-primary); margin-right: 10px;"></i>
                    Finalize Your Purchase
                </h1>
                <p class="checkout-subtitle">Fill in your details to complete the order</p>

                <form action="process_checkout.php" method="POST">

                    <div class="form-group">
                        <label for="email"><i class="fa-solid fa-envelope"></i> Email Address</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" placeholder="you@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="customer_name"><i class="fa-solid fa-user"></i> Full Name</label>
                        <input type="text" id="customer_name" name="customer_name" placeholder="Enter your full name" required>
                    </div>

                    <div class="form-group">
                        <label for="shipping_address"><i class="fa-solid fa-location-dot"></i> Shipping Address</label>
                        <textarea id="shipping_address" name="shipping_address" placeholder="Enter your full delivery address..." required></textarea>
                    </div>

                    <hr class="divider">

                    <div class="form-group">
                        <label for="payment_select"><i class="fa-solid fa-credit-card"></i> Payment Method</label>
                        <select id="payment_select" name="payment_method" onchange="toggleCardFields()">
                            <option value="cash">💵 Cash on Delivery</option>
                            <option value="card">💳 Credit / Debit Card</option>
                        </select>
                    </div>

                    <!-- Card fields (shown when card is selected) -->
                    <div id="card_fields" class="card-fields-box">
                        <div>
                            <label>Card Number</label>
                            <input type="text" placeholder="XXXX  XXXX  XXXX  XXXX" maxlength="19">
                        </div>
                        <div class="card-row">
                            <div>
                                <label>Expiry Date</label>
                                <input type="text" placeholder="MM / YY" maxlength="5">
                            </div>
                            <div>
                                <label>CVV</label>
                                <input type="text" placeholder="CVV" maxlength="4">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="checkout-submit-btn">
                        <i class="fa-solid fa-lock"></i>
                        Confirm & Place Order
                    </button>

                </form>

                <p class="back-link">
                    Changed your mind? <a href="cart.php">← Back to Cart</a>
                </p>

            </div>
        </div>
    </main>

    <?php include "files/footer.php" ?>
    <script src="javascript/script.js"></script>
    <script src="javascript/nav.js"></script>
    <script>
        function toggleCardFields() {
            const select = document.getElementById('payment_select');
            const fields = document.getElementById('card_fields');
            fields.style.display = select.value === 'card' ? 'flex' : 'none';
        }
    </script>

</body>
</html>