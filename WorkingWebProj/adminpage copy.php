<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require_once 'config.php';

$success_msg = '';
$error_msg = '';

// ── ADD PRODUCT ────────────────────────────────────────────────────────────────
if (isset($_POST['add_product'])) {
    $title       = trim($conn->real_escape_string($_POST['title']));
    $price       = floatval($_POST['price']);
    $description = trim($conn->real_escape_string($_POST['description']));
    $category    = trim($conn->real_escape_string($_POST['category']));
    $meta_desc   = trim($conn->real_escape_string($_POST['meta_description']));
    $meta_kw     = trim($conn->real_escape_string($_POST['meta_keywords']));
    $image       = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES['image']['type'], $allowed)) {
            $ext   = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image = uniqid('prod_') . '.' . $ext;
            $dest  = __DIR__ . '/products/' . $image;
            if (!is_dir(__DIR__ . '/products')) mkdir(__DIR__ . '/products', 0755, true);
            move_uploaded_file($_FILES['image']['tmp_name'], $dest);
        } else {
            $error_msg = 'Invalid image type. Please upload JPG, PNG, GIF, or WEBP.';
        }
    }

    if (empty($error_msg)) {
        $sql = "INSERT INTO products (image, title, price, description, category, meta_description, meta_keywords)
                VALUES ('$image', '$title', $price, '$description', '$category', '$meta_desc', '$meta_kw')";
        if ($conn->query($sql)) {
            $success_msg = "Product <strong>$title</strong> added successfully!";
        } else {
            $error_msg = 'Database error: ' . $conn->error;
        }
    }
}

// ── UPDATE PRODUCT ─────────────────────────────────────────────────────────────
if (isset($_POST['update_product'])) {
    $id          = intval($_POST['product_id']);
    $title       = trim($conn->real_escape_string($_POST['title']));
    $price       = floatval($_POST['price']);
    $description = trim($conn->real_escape_string($_POST['description']));
    $category    = trim($conn->real_escape_string($_POST['category']));
    $meta_desc   = trim($conn->real_escape_string($_POST['meta_description']));
    $meta_kw     = trim($conn->real_escape_string($_POST['meta_keywords']));

    $image_sql = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (in_array($_FILES['image']['type'], $allowed)) {
            $ext    = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image  = uniqid('prod_') . '.' . $ext;
            $dest   = __DIR__ . '/products/' . $image;
            if (!is_dir(__DIR__ . '/products')) mkdir(__DIR__ . '/products', 0755, true);
            move_uploaded_file($_FILES['image']['tmp_name'], $dest);
            $image_sql = ", image='$image'";
        }
    }

    $sql = "UPDATE products SET title='$title', price=$price, description='$description',
            category='$category', meta_description='$meta_desc', meta_keywords='$meta_kw'
            $image_sql WHERE id=$id";
    if ($conn->query($sql)) {
        $success_msg = "Product <strong>$title</strong> updated successfully!";
    } else {
        $error_msg = 'Database error: ' . $conn->error;
    }
}

// ── DELETE PRODUCT ─────────────────────────────────────────────────────────────
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($conn->query("DELETE FROM products WHERE id=$id")) {
        $success_msg = 'Product deleted successfully.';
    } else {
        $error_msg = 'Could not delete product.';
    }
}

// ── FETCH PRODUCT FOR EDIT ─────────────────────────────────────────────────────
$edit_product = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM products WHERE id=$id");
    if ($res && $res->num_rows === 1) {
        $edit_product = $res->fetch_assoc();
    }
}

// ── FETCH ALL PRODUCTS ─────────────────────────────────────────────────────────
$products = [];
$res = $conn->query("SELECT * FROM products ORDER BY id DESC");
while ($res && $row = $res->fetch_assoc()) {
    $products[] = $row;
}

$active_section = isset($_GET['section']) ? $_GET['section'] : 'stock';
if ($edit_product) $active_section = 'edit';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/style_admin.css">
</head>
<body class="adminpage">

    <!-- Sidebar -->
    <div class="panel_sidebar">
        <div class="sidebar-logo">⚡ Admin <span>Panel</span></div>
        <ul>
            <li>
                <a href="adminpage.php?section=stock"
                   class="<?= ($active_section === 'stock') ? 'active' : '' ?>">
                    <span>📦</span> Stock / Products
                </a>
            </li>
            <li>
                <a href="adminpage.php?section=add"
                   class="<?= ($active_section === 'add' || $active_section === 'edit') ? 'active' : '' ?>">
                    <span>➕</span> Add Product
                </a>
            </li>
            <li>
                <a href="adminpage.php?section=orders"
                   class="<?= ($active_section === 'orders') ? 'active' : '' ?>">
                    <span>🛒</span> View Orders
                </a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <a href="logout.php">🚪 Logout</a>
        </div>
    </div>

    <!-- Main -->
    <div class="main-content">

        <?php if ($success_msg): ?>
            <div class="alert alert-success" id="flash-msg">✅ <?= $success_msg ?></div>
        <?php elseif ($error_msg): ?>
            <div class="alert alert-error" id="flash-msg">❌ <?= $error_msg ?></div>
        <?php endif; ?>

        <!-- Stats -->
        <?php
            $total_products = count($products);
            $total_value    = array_sum(array_column($products, 'price'));
            $categories_set = array_unique(array_column($products, 'category'));
        ?>
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-info">
                    <div class="stat-number"><?= $total_products ?></div>
                    <div class="stat-label">Total Products</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🏷️</div>
                <div class="stat-info">
                    <div class="stat-number"><?= count($categories_set) ?></div>
                    <div class="stat-label">Categories</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-info">
                    <div class="stat-number">Rs.<?= number_format($total_value) ?></div>
                    <div class="stat-label">Total Stock Value</div>
                </div>
            </div>
        </div>

        <!-- ADD PRODUCT FORM -->
        <?php if ($active_section === 'add'): ?>
        <div class="card">
            <h2>➕ Add New Product</h2>
            <form action="adminpage.php?section=add" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Product Title <span style="color:#f8a3aa">*</span></label>
                        <input type="text" name="title" placeholder="e.g. Chocolate Box" required>
                    </div>
                    <div class="form-group">
                        <label>Price (Rs.) <span style="color:#f8a3aa">*</span></label>
                        <input type="number" name="price" placeholder="e.g. 2500" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Category <span style="color:#f8a3aa">*</span></label>
                        <input type="text" name="category" placeholder="e.g. chocolates, flowers" required>
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <div class="form-group full-width">
                        <label>Description <span style="color:#f8a3aa">*</span></label>
                        <textarea name="description" placeholder="Describe the product..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Meta Description</label>
                        <input type="text" name="meta_description" placeholder="SEO meta description">
                    </div>
                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <input type="text" name="meta_keywords" placeholder="keyword1, keyword2, ...">
                    </div>
                </div>
                <button type="submit" name="add_product" class="btn-submit">➕ Add Product</button>
            </form>
        </div>
        <?php endif; ?>

        <!-- EDIT PRODUCT FORM -->
        <?php if ($active_section === 'edit' && $edit_product): ?>
        <div class="card">
            <h2>✏️ Edit Product — <?= htmlspecialchars($edit_product['title']) ?></h2>
            <form action="adminpage.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?= $edit_product['id'] ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Product Title <span style="color:#f8a3aa">*</span></label>
                        <input type="text" name="title" value="<?= htmlspecialchars($edit_product['title']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Price (Rs.) <span style="color:#f8a3aa">*</span></label>
                        <input type="number" name="price" value="<?= $edit_product['price'] ?>" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Category <span style="color:#f8a3aa">*</span></label>
                        <input type="text" name="category" value="<?= htmlspecialchars($edit_product['category']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>New Image (leave blank to keep current)</label>
                        <input type="file" name="image" accept="image/*">
                        <?php if (!empty($edit_product['image'])): ?>
                            <small style="color:rgba(255,255,255,0.45);font-size:0.78rem;">
                                Current file: <?= htmlspecialchars($edit_product['image']) ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    <div class="form-group full-width">
                        <label>Description <span style="color:#f8a3aa">*</span></label>
                        <textarea name="description" required><?= htmlspecialchars($edit_product['description']) ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Meta Description</label>
                        <input type="text" name="meta_description" value="<?= htmlspecialchars($edit_product['meta_description']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Meta Keywords</label>
                        <input type="text" name="meta_keywords" value="<?= htmlspecialchars($edit_product['meta_keywords']) ?>">
                    </div>
                </div>
                <button type="submit" name="update_product" class="btn-submit">💾 Update Product</button>
                <a href="adminpage.php?section=stock" class="btn-cancel">✕ Cancel</a>
            </form>
        </div>
        <?php endif; ?>

        <!-- STOCK TABLE -->
        <?php if ($active_section === 'stock'): ?>
        <div class="card">
            <h2>📦 Product Stock</h2>
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <div class="icon">🗃️</div>
                    <p>No products found. <a href="adminpage.php?section=add" style="color:#b97bf0;">Add your first product →</a></p>
                </div>
            <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td>#<?= $p['id'] ?></td>
                            <td>
                                <?php
                                    $img_path = __DIR__ . '/products/' . $p['image'];
                                    $img_src  = (!empty($p['image']) && file_exists($img_path))
                                                ? 'products/' . htmlspecialchars($p['image']) : null;
                                ?>
                                <?php if ($img_src): ?>
                                    <img src="<?= $img_src ?>" alt="" class="product-thumb">
                                <?php else: ?>
                                    <div class="thumb-placeholder">🖼️</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
                            <td><span class="badge"><?= htmlspecialchars($p['category']) ?></span></td>
                            <td class="price-cell">Rs. <?= number_format($p['price'], 2) ?></td>
                            <td>
                                <div class="action-btns">
                                    <a href="adminpage.php?edit=<?= $p['id'] ?>" class="btn-edit">✏️ Edit</a>
                                    <a href="adminpage.php?delete=<?= $p['id'] ?>&section=stock"
                                       class="btn-delete"
                                       onclick="return confirm('Delete \'<?= htmlspecialchars(addslashes($p['title'])) ?>\'?')">
                                       🗑️ Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- ORDERS (placeholder) -->
        <?php if ($active_section === 'orders'): ?>
        <div class="card">
            <h2>🛒 Orders</h2>
            <div class="empty-state">
                <div class="icon">🚧</div>
                <p>Order management coming soon.</p>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <script>
        const flash = document.getElementById('flash-msg');
        if (flash) {
            setTimeout(() => {
                flash.style.transition = 'opacity 0.5s';
                flash.style.opacity = '0';
                setTimeout(() => flash.remove(), 500);
            }, 4000);
        }
    </script>
</body>
</html>
