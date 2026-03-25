<?php require "php/functions.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Giftora by NSBM – Browse our wide selection of products">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/style_main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Giftora by NSBM</title>
</head>

<body id="index">

    <?php include "files/header.php" ?>

    <main>
        <div class="product-grid">
            <?php 
            // Auto-shuffle: fetch all products and shuffle server-side on every load
            $products = getHomePageProducts(12);
            if ($products) {
                shuffle($products);
                foreach($products as $product): 
            ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="products/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                    </div>
                    <div class="product-info">
                        <p class="title">
                            <a href="product.php?title=<?php echo urlencode($product['title'])?>">
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
                                <button type="submit" name="add_to_cart" class="add-btn" aria-label="Add <?php echo htmlspecialchars($product['title']); ?> to cart">+</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; } ?>
        </div>
    </main>

    <?php include "files/footer.php" ?>

    <script src="javascript/script.js"></script>
    <script src="javascript/nav.js"></script>

</body>
</html>