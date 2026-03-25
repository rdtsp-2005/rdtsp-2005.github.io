<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/style_main.css">
    <!-- FontAwesome for icons and dropdown chevron -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>About Us | Giftora by NSBM</title>
</head>

<body id="about">
    <?php include "files/header.php" ?>

    <main>
        <section class="about-hero">
            <h1>About Giftora by NSBM</h1>
            <p>Spreading happiness through thoughtful gifts</p>
        </section>

        <section class="about-container">
            <div class="about-card">
                <i class="fa-solid fa-gift"></i>
                <h2>Who We Are</h2>
                <p>We are a modern gift shop dedicated to providing high-quality, meaningful gifts for every special occasion.</p>
            </div>

            <div class="about-card">
                <i class="fa-solid fa-heart"></i>
                <h2>Our Mission</h2>
                <p>Our mission is to connect people through gifts that express love, care, and appreciation.</p>
            </div>

            <div class="about-card">
                <i class="fa-solid fa-star"></i>
                <h2>Why Choose Us</h2>
                <ul>
                    <li>Wide range of gift categories</li>
                    <li>High-quality products</li>
                    <li>Affordable prices</li>
                </ul>
            </div>
        </section>

        <section class="about-cta">
            <h2>Make Every Moment Special</h2>
            <button onclick="location.href='index.php'">Explore Our Gifts</button>
        </section>
    </main>

    <?php include "files/footer.php" ?>
    <script src="javascript/script.js"></script>
    <script src="javascript/nav.js"></script>
</body>
</html>