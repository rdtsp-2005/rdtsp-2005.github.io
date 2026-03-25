<?php
require "php/functions.php";

if (isset($_POST['add_to_cart'])) {
    $pid = (int)$_POST['product_id'];
    $qty = (int)$_POST['quantity'];

    $message = addToCart($pid, $qty);

    $previous_page = $_SERVER['HTTP_REFERER'] ?? 'index.php';

    // Encode message safely for JS
    $safe_message = htmlspecialchars($message, ENT_QUOTES);
    echo "<script>
        alert('$safe_message');
        window.location.href='$previous_page';
    </script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/style_main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Shopping Cart | Giftora by NSBM</title>
    <style>
        /* ── Cart Page Styles ── */
        .cart-page-title {
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--clr-text-main);
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cart-page-title i { color: var(--clr-primary); }

        /* ── Cart Table ── */
        .cart-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .cart-table thead th {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--clr-text-sub);
            padding: 6px 18px;
            text-align: left;
        }

        .cart-table tbody tr {
            background: var(--clr-bg-card);
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            transition: box-shadow var(--transition-fast);
        }

        .cart-table tbody tr:hover { box-shadow: var(--shadow-md); }

        .cart-table tbody td {
            padding: 16px 18px;
            font-size: 0.92rem;
            color: var(--clr-text-main);
            vertical-align: middle;
        }

        .cart-table tbody tr td:first-child { border-radius: 12px 0 0 12px; }
        .cart-table tbody tr td:last-child  { border-radius: 0 12px 12px 0; }

        .cart-item-info {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .cart-item-info img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--clr-border);
            background: var(--clr-primary-light);
        }

        .cart-item-info span {
            font-weight: 600;
            color: var(--clr-text-main);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .cart-price { font-weight: 600; color: var(--clr-text-main); }
        .cart-subtotal { font-weight: 700; color: var(--clr-primary); }

        .cart-qty-badge {
            display: inline-block;
            background: var(--clr-primary-light);
            color: var(--clr-primary);
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.88rem;
        }

        .remove-link {
            color: #ef4444;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: color var(--transition-fast);
            white-space: nowrap;
        }

        .remove-link:hover { color: #b91c1c; text-decoration: underline; }

        /* ── Cart Summary Box ── */
        .cart-summary-box {
            background: var(--clr-bg-card);
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--clr-border);
            padding: 28px 32px;
            margin-top: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .cart-total-label {
            font-size: 0.85rem;
            color: var(--clr-text-sub);
            margin-bottom: 4px;
        }

        .cart-total-amount {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--clr-text-main);
        }

        .cart-total-amount span { color: var(--clr-primary); }

        .cart-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-clear-cart {
            padding: 11px 20px;
            border: 1.5px solid #ef4444;
            color: #ef4444;
            border-radius: 50px;
            font-size: 0.88rem;
            font-weight: 600;
            font-family: var(--font-main);
            cursor: pointer;
            background: transparent;
            transition: all var(--transition-fast);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .btn-clear-cart:hover { background: #fef2f2; }

        .btn-checkout {
            padding: 12px 28px;
            background: var(--clr-primary);
            color: #fff;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: var(--font-main);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 9px;
            transition: background var(--transition-fast), transform var(--transition-fast);
        }

        .btn-checkout:hover {
            background: var(--clr-primary-hover);
            transform: translateY(-2px);
        }

        /* ── Empty Cart State ── */
        .cart-empty {
            text-align: center;
            padding: 80px 20px;
            background: var(--clr-bg-card);
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
        }

        .cart-empty-icon {
            font-size: 4rem;
            color: var(--clr-primary-light);
            margin-bottom: 16px;
            display: block;
        }

        .cart-empty h2 {
            font-size: 1.4rem;
            color: var(--clr-text-main);
            margin-bottom: 8px;
        }

        .cart-empty p {
            color: var(--clr-text-sub);
            margin-bottom: 24px;
            font-size: 0.95rem;
        }
    </style>
</head>

<body id="cart">
    <?php include "files/header.php" ?>

    <main>
        <h1 class="cart-page-title">
            <i class="fa-solid fa-cart-shopping"></i>
            Your Shopping Cart
        </h1>

        <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>

            <!-- Empty Cart -->
            <div class="cart-empty">
                <i class="fa-solid fa-bag-shopping cart-empty-icon"></i>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added anything yet. Start shopping!</p>
                <a href="index.php" class="btn-checkout">
                    <i class="fa-solid fa-arrow-left"></i> Browse Products
                </a>
            </div>

        <?php else:
            $cart_ids   = array_keys($_SESSION['cart']);
            $products   = getCartProducts($cart_ids);
            $total_price = 0;
        ?>

            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Unit Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                        <th>Remove</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $item):
                        $qty      = $_SESSION['cart'][$item['id']];
                        $subtotal = $item['price'] * $qty;
                        $total_price += $subtotal;
                    ?>
                    <tr>
                        <td>
                            <div class="cart-item-info">
                                <img src="products/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <span><?php echo htmlspecialchars($item['title']); ?></span>
                            </div>
                        </td>
                        <td class="cart-price">Rs. <?php echo number_format($item['price'], 2); ?></td>
                        <td><span class="cart-qty-badge"><?php echo $qty; ?></span></td>
                        <td class="cart-subtotal">Rs. <?php echo number_format($subtotal, 2); ?></td>
                        <td>
                            <a href="remove_item.php?id=<?php echo $item['id']; ?>" class="remove-link">
                                <i class="fa-solid fa-trash"></i> Remove
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Summary -->
            <div class="cart-summary-box">
                <div>
                    <p class="cart-total-label">Order Total</p>
                    <p class="cart-total-amount">Rs. <span><?php echo number_format($total_price, 2); ?></span></p>
                </div>
                <div class="cart-actions">
                    <a href="clear_cart.php" class="btn-clear-cart"
                       onclick="return confirm('Empty your entire cart?')">
                        <i class="fa-solid fa-trash"></i> Clear Cart
                    </a>
                    <a href="checkout_details.php" class="btn-checkout">
                        <i class="fa-solid fa-lock"></i> Checkout
                    </a>
                </div>
            </div>

        <?php endif; ?>
    </main>

    <?php include "files/footer.php" ?>
    <script src="javascript/script.js"></script>
    <script src="javascript/nav.js"></script>
</body>
</html>