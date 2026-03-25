<?php
    // Ensure session is active so we can read login state
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $loggedIn   = isset($_SESSION['name']);
    $userName   = $loggedIn ? htmlspecialchars($_SESSION['name']) : '';
    $isAdmin    = $loggedIn && ($_SESSION['role'] ?? '') === 'admin';
?>
<header class="global-header">
    <nav>

        <!-- LEFT: Burger + Brand -->
        <div class="nav-left">
            <button id="burger-btn" class="burger-btn" aria-label="Toggle menu" aria-expanded="false">
                <span class="burger-line"></span>
                <span class="burger-line"></span>
                <span class="burger-line"></span>
            </button>
            <a href="index.php" class="brand-link">
                <span class="brand">Our Store</span>
            </a>
        </div>

        <!-- CENTER: Search Bar -->
        <div class="nav-search">
            <form action="category.php" method="GET" class="search-form" role="search">
                <input
                    type="text"
                    name="search"
                    class="search-input"
                    placeholder="Search products..."
                    aria-label="Search products"
                    autocomplete="off"
                >
                <button type="submit" class="search-btn" aria-label="Submit search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
            </form>
        </div>

        <!-- RIGHT: Nav Links -->
        <div class="links">
            <a data-active="index" href="index.php">Home</a>
            <div class="category-dropdown-container">
                <button class="category-dropdown-btn" aria-haspopup="true" aria-expanded="false">
                    Categories <i class="fa-solid fa-chevron-down caret-icon"></i>
                </button>
                <div class="category-dropdown-menu" id="category-panel">
                    <?php
                    if (!function_exists('getCategories')) {
                        require_once "php/functions.php";
                    }
                    $categories = getCategories();
                    if ($categories) {
                        foreach($categories as $category){
                            echo '<a class="dropdown-item" href="category.php?category='.urlencode($category['category']).'">'.ucfirst($category['category']).'</a>';
                        }
                    }
                    ?>
                </div>
            </div>
            <a data-active="about" href="about.php">About</a>
            <a data-active="contact" href="contact.php">Contact</a>
            <a data-active="cart" href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
            <?php if ($loggedIn): ?>
                <div class="user-menu-container">
                    <button class="user-menu-btn" aria-haspopup="true" aria-expanded="false">
                        <i class="fa-solid fa-circle-user"></i>
                        <span class="user-menu-name"><?= $userName ?></span>
                        <i class="fa-solid fa-chevron-down caret-icon"></i>
                    </button>
                    <div class="user-dropdown">
                        <?php if ($isAdmin): ?>
                            <a class="user-dropdown-item admin-item" href="adminpage.php">
                                <i class="fa-solid fa-shield-halved"></i> Admin Panel
                            </a>
                        <?php endif; ?>
                        <a class="user-dropdown-item" href="logout.php">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a data-active="login" href="login_home.php" class="nav-login-btn">Login</a>
            <?php endif; ?>
        </div>

    </nav>

    <!-- Mobile Slide Menu -->
    <div class="mobile-menu" id="mobile-menu" aria-hidden="true">
        <div class="mobile-menu-header">
            <span class="brand">Our Store</span>
            <button class="mobile-close-btn" id="mobile-close-btn" aria-label="Close menu">&#10005;</button>
        </div>
        <div class="mobile-search">
            <form action="category.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search products..." class="search-input">
                <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>
        <nav class="mobile-nav">
            <a href="index.php"><i class="fa-solid fa-house"></i> Home</a>
            <div class="mobile-cat-section">
                <span class="mobile-cat-title"><i class="fa-solid fa-tag"></i> Categories</span>
                <?php
                if (!empty($categories)) {
                    foreach($categories as $category){
                        echo '<a class="mobile-cat-link" href="category.php?category='.urlencode($category['category']).'">'.ucfirst($category['category']).'</a>';
                    }
                }
                ?>
            </div>
            <a href="about.php"><i class="fa-solid fa-circle-info"></i> About</a>
            <a href="contact.php"><i class="fa-solid fa-envelope"></i> Contact</a>
            <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a>
            <?php if ($loggedIn): ?>
                <div class="mobile-user-section">
                    <span class="mobile-user-greeting"><i class="fa-solid fa-circle-user"></i> <?= $userName ?></span>
                    <?php if ($isAdmin): ?>
                        <a href="adminpage.php" class="mobile-admin-btn"><i class="fa-solid fa-shield-halved"></i> Admin Panel</a>
                    <?php endif; ?>
                    <a href="logout.php" class="mobile-login-btn"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                </div>
            <?php else: ?>
                <a href="login_home.php" class="mobile-login-btn"><i class="fa-solid fa-user"></i> Login</a>
            <?php endif; ?>
        </nav>
    </div>
    <div class="menu-overlay" id="menu-overlay"></div>
</header>
