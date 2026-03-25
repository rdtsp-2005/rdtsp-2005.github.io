<?php require "php/functions.php" ?>

<?php
if(isset($_POST['add_to_cart'])){
    $product_id = $_POST['product_id'];
    $qty = (int)$_POST['quantity'];
    $result = addToCart($product_id, $qty);
    if($result === "Success"){
        // Cleanly rebuild URL with added=1 flag
        $redirectTitle = $_POST['title'] ?? urlencode(getProductbyTitle($product_id)[0]['title'] ?? '');
        header("Location: product.php?title=" . urlencode(urldecode($_GET['title'] ?? '')) . "&added=1");
        exit();
    } else {
        $cartError = $result;
    }
}

if (isset($_GET['title'])) {
    $title   = urldecode($_GET['title']);
    $product = getProductbyTitle($title);
}

$p       = $product[0] ?? null;
$inStock = $p && $p['quantity'] > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php if($p): ?>
    <meta name="description" content="<?= htmlspecialchars($p['meta_description'] ?? '') ?>">
    <meta name="keywords"    content="<?= htmlspecialchars($p['meta_keywords']    ?? '') ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/style_main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title><?= htmlspecialchars($title ?? 'Product') ?> | Giftora by NSBM</title>
    <style>
        /* ── Product Detail Page ─────────────────────────────── */
        .product-page-wrapper {
            max-width: 1000px;
            margin: 36px auto;
            padding: 0 20px;
        }

        /* ── Breadcrumb bar ─────────────────────────────────── */
        .product-breadcrumb-bar {
            background: var(--clr-bg-card);
            border-bottom: 1px solid var(--clr-border);
            padding: 10px 28px;
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            color: var(--clr-text-sub);
        }

        .product-breadcrumb-bar a {
            color: var(--clr-primary);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.18s;
        }

        .product-breadcrumb-bar a:hover {
            color: var(--clr-primary-hover);
            text-decoration: underline;
        }

        .product-breadcrumb-bar .bc-current {
            color: var(--clr-text-main);
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 260px;
        }

        .bc-sep {
            font-size: 0.6rem;
            color: #d1d5db;
            flex-shrink: 0;
        }

        /* Card */
        .product-card-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            background: var(--clr-bg-card);
            border-radius: 20px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--clr-border);
            overflow: hidden;
        }

        /* Image panel */
        .product-img-panel {
            background: var(--clr-primary-light);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 36px;
            min-height: 380px;
        }

        .product-img-panel img {
            max-width: 100%;
            max-height: 340px;
            object-fit: contain;
            border-radius: 12px;
            transition: transform 0.4s ease;
        }

        .product-img-panel img:hover {
            transform: scale(1.04);
        }

        /* Info panel */
        .product-info-panel {
            padding: 40px 36px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .product-category-badge {
            display: inline-block;
            padding: 4px 12px;
            background: var(--clr-primary-light);
            color: var(--clr-primary);
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            width: fit-content;
        }

        .product-title-detail {
            font-size: 1.55rem;
            font-weight: 700;
            color: var(--clr-text-main);
            line-height: 1.3;
            margin: 0;
        }

        .product-price-detail {
            font-size: 2rem;
            font-weight: 800;
            color: var(--clr-primary);
            margin: 0;
        }

        .product-price-detail::before {
            content: 'Rs. ';
            font-size: 1rem;
            font-weight: 600;
            vertical-align: middle;
        }

        .product-description-detail {
            font-size: 0.92rem;
            color: var(--clr-text-sub);
            line-height: 1.7;
            margin: 0;
        }

        .product-divider {
            border: none;
            border-top: 1px solid var(--clr-border);
            margin: 4px 0;
        }

        /* Stock badge */
        .stock-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 20px;
            width: fit-content;
        }

        .stock-badge.in-stock {
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .stock-badge.out-of-stock {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        /* Quantity selector */
        .qty-row {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .qty-label {
            font-size: 0.82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--clr-text-sub);
        }

        .qty-controls {
            display: flex;
            align-items: center;
            border: 1.5px solid var(--clr-border);
            border-radius: 50px;
            overflow: hidden;
            background: var(--clr-bg-main);
        }

        .qty-btn {
            width: 36px;
            height: 36px;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--clr-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
        }

        .qty-btn:hover { background: var(--clr-primary-light); }

        .qty-input {
            width: 44px;
            text-align: center;
            border: none;
            background: transparent;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--clr-text-main);
            outline: none;
            padding: 0;
            -moz-appearance: textfield;
        }

        .qty-input::-webkit-inner-spin-button,
        .qty-input::-webkit-outer-spin-button { -webkit-appearance: none; }

        /* Add to Cart button */
        .atc-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--clr-primary), #a855f7);
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 700;
            font-family: var(--font-main);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 6px 20px rgba(124,58,237,0.3);
            margin-top: 6px;
        }

        .atc-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(124,58,237,0.42);
        }

        .atc-btn:disabled {
            background: #d1d5db;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        /* Success / error flash */
        .product-flash {
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 6px;
            animation: flashIn 0.35s ease;
        }

        @keyframes flashIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .product-flash.success {
            background: #dcfce7;
            border: 1.5px solid #86efac;
            color: #14532d;
            box-shadow: 0 4px 14px rgba(22, 101, 52, 0.12);
        }

        .product-flash.success i {
            font-size: 1.1rem;
            color: #16a34a;
        }

        .product-flash.error {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            color: #991b1b;
            box-shadow: 0 4px 14px rgba(220, 38, 38, 0.10);
        }

        .product-flash.error i {
            font-size: 1.1rem;
            color: #dc2626;
        }

        /* Responsive */
        @media (max-width: 700px) {
            .product-card-detail {
                grid-template-columns: 1fr;
            }

            .product-img-panel {
                min-height: 260px;
                padding: 24px;
            }

            .product-info-panel {
                padding: 24px 20px;
            }

            .product-title-detail { font-size: 1.25rem; }
            .product-price-detail { font-size: 1.6rem; }
        }
    </style>
</head>

<body>
    <?php include "files/header.php" ?>

    <main>
        <!-- ── Breadcrumb bar (top of page, full width) ─────── -->
        <nav class="product-breadcrumb-bar" aria-label="breadcrumb">
            <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
            <?php if ($p): ?>
            <i class="fa-solid fa-chevron-right bc-sep"></i>
            <a href="category.php?category=<?= urlencode($p['category'] ?? '') ?>">
                <?= htmlspecialchars($p['category'] ?? 'Products') ?>
            </a>
            <i class="fa-solid fa-chevron-right bc-sep"></i>
            <span class="bc-current"><?= htmlspecialchars($p['title'] ?? '') ?></span>
            <?php endif; ?>
        </nav>

        <div class="product-page-wrapper">

            <?php if ($p): ?>
            <div class="product-card-detail">

                <!-- Left: Product Image -->
                <div class="product-img-panel">
                    <img src="products/<?= htmlspecialchars($p['image']) ?>"
                         alt="<?= htmlspecialchars($p['title']) ?>">
                </div>

                <!-- Right: Product Info -->
                <div class="product-info-panel">

                    <?php if (!empty($p['category'])): ?>
                    <span class="product-category-badge">
                        <i class="fa-solid fa-tag"></i> <?= htmlspecialchars($p['category']) ?>
                    </span>
                    <?php endif; ?>

                    <h1 class="product-title-detail"><?= htmlspecialchars($p['title']) ?></h1>

                    <p class="product-price-detail"><?= number_format((float)$p['price'], 2) ?></p>

                    <?php if (!empty($p['description'])): ?>
                    <p class="product-description-detail"><?= nl2br(htmlspecialchars($p['description'])) ?></p>
                    <?php endif; ?>

                    <hr class="product-divider">

                    <!-- Stock -->
                    <?php if ($inStock): ?>
                    <span class="stock-badge in-stock">
                        <i class="fa-solid fa-circle-check"></i>
                        In Stock (<?= (int)$p['quantity'] ?> available)
                    </span>
                    <?php else: ?>
                    <span class="stock-badge out-of-stock">
                        <i class="fa-solid fa-circle-xmark"></i> Out of Stock
                    </span>
                    <?php endif; ?>

                    <!-- Flash messages -->
                    <?php if (isset($_GET['added'])): ?>
                    <div class="product-flash success">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>✅ Added to cart successfully!</span>
                    </div>
                    <?php elseif (isset($cartError)): ?>
                    <div class="product-flash error">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span><?= htmlspecialchars($cartError) ?></span>
                    </div>
                    <?php endif; ?>

                    <!-- Add to Cart Form -->
                    <form method="POST" action="" class="add-to-cart-form">
                        <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">

                        <div class="qty-row">
                            <span class="qty-label">Quantity</span>
                            <div class="qty-controls">
                                <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
                                <input type="number" id="qty" name="quantity" class="qty-input"
                                       value="1" min="1" max="<?= (int)$p['quantity'] ?>">
                                <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                            </div>
                        </div>

                        <button type="submit" name="add_to_cart" class="atc-btn"
                                <?= $inStock ? '' : 'disabled' ?>>
                            <i class="fa-solid fa-cart-plus"></i>
                            <?= $inStock ? 'Add to Cart' : 'Out of Stock' ?>
                        </button>
                    </form>

                </div>
            </div>

            <?php else: ?>
            <div style="text-align:center;padding:60px 20px;">
                <p style="font-size:1.1rem;color:var(--clr-text-sub);">
                    <i class="fa-solid fa-triangle-exclamation"></i> Product not found.
                </p>
                <a href="index.php" class="buy-now-btn" style="margin-top:20px;display:inline-block;">
                    Back to Shop
                </a>
            </div>
            <?php endif; ?>

        </div>
    </main>

    <?php include "files/footer.php" ?>

    <script src="javascript/script.js"></script>
    <script src="javascript/nav.js"></script>
    <script>
        function changeQty(delta) {
            const input = document.getElementById('qty');
            const val   = parseInt(input.value) + delta;
            const min   = parseInt(input.min) || 1;
            const max   = parseInt(input.max) || 99;
            input.value = Math.min(Math.max(val, min), max);
        }
    </script>
</body>
</html>