<?php require "php/functions.php" ?>

<?php
// Handle both category browsing and search
$pageTitle = "Our Store";
$products  = [];

if (!empty($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    $products    = searchProducts($searchQuery);
    $pageTitle   = 'Search: ' . htmlspecialchars($searchQuery);

} elseif (!empty($_GET['category'])) {
    $cat       = urldecode($_GET['category']);
    $products  = getProductsByCategory($cat);
    $pageTitle = ucfirst($cat);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/style_main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title><?php echo $pageTitle ?> | Giftora by NSBM</title>
</head>

<body id="category">
    <?php include "files/header.php" ?>

    <main>
        <h2 class="section-title"><?php echo $pageTitle ?></h2>

        <?php if (empty($products)): ?>
            <div style="text-align:center; padding: 60px 0; color: var(--clr-text-sub);">
                <i class="fa-solid fa-box-open" style="font-size:3rem; margin-bottom:16px; display:block; color: var(--clr-primary);"></i>
                <p style="font-size:1.1rem;">No products found.</p>
                <?php if (!empty($_GET['search'])): ?>
                    <a href="index.php" style="margin-top:14px; display:inline-block; color:var(--clr-primary);">← Back to All Products</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="products/<?php echo $product['image'] ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                        </div>
                        <div class="product-info">
                            <p class="title">
                                <a href="product.php?title=<?php echo urlencode($product['title']) ?>">
                                    <?php echo htmlspecialchars($product['title']) ?>
                                </a>
                            </p>
                            <p class="description">
                                <?php echo (strlen($product['description']) > 70) ? htmlspecialchars(substr($product['description'], 0, 70)) . '...' : htmlspecialchars($product['description']); ?>
                            </p>
                            <div class="card-footer">
                                <p class="price">Rs.<?php echo $product['price']; ?></p>
                                <form action="cart.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" name="add_to_cart" class="add-btn" aria-label="Add to cart">+</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include "files/footer.php" ?>

    <script src="javascript/script.js"></script>
    <script src="javascript/nav.js"></script>
</body>
</html>