<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require_once 'config.php';

$success_msg = '';
$error_msg = '';

// ── FETCH ORDERS DATA ──────────────────────────────────────────────────────────
$orders = [];
$orders_res = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
if ($orders_res) {
    while ($row = $orders_res->fetch_assoc()) {
        $orders[] = $row;
    }
}

// ── UPDATE STOCK QUANTITY ────────────────────────────────────────────────────
if (isset($_POST['update_stock'])) {
    $id  = intval($_POST['stock_product_id']);
    $qty = max(0, intval($_POST['new_quantity']));
    $conn->query("UPDATE products SET quantity=$qty WHERE id=$id");
    $success_msg = "Stock updated successfully!";
}

// ── ADD PRODUCT (Existing Logic) ──────────────────────────────────────────────
if (isset($_POST['add_product'])) {
    $title       = trim($conn->real_escape_string($_POST['title']));
    $price       = floatval($_POST['price']);
    $description = trim($conn->real_escape_string($_POST['description']));
    $category    = trim($conn->real_escape_string($_POST['category']));
    $meta_desc   = trim($conn->real_escape_string($_POST['meta_description'] ?? ''));
    $meta_kw     = trim($conn->real_escape_string($_POST['meta_keywords'] ?? ''));
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
            $error_msg = 'Invalid image type.';
        }
    }

    $stock_qty = max(0, intval($_POST['quantity'] ?? 0));

    if (empty($error_msg)) {
        $sql = "INSERT INTO products (image, title, price, description, category, meta_description, meta_keywords, quantity)
                VALUES ('$image', '$title', $price, '$description', '$category', '$meta_desc', '$meta_kw', $stock_qty)";
        if ($conn->query($sql)) {
            $success_msg = "Product <strong>$title</strong> added successfully!";
        }
    }
}

// ── UPDATE PRODUCT (Existing Logic) ───────────────────────────────────────────
if (isset($_POST['update_product'])) {
    $id          = intval($_POST['product_id']);
    $title       = trim($conn->real_escape_string($_POST['title']));
    $price       = floatval($_POST['price']);
    $description = trim($conn->real_escape_string($_POST['description']));
    $category    = trim($conn->real_escape_string($_POST['category']));

    $image_sql = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext    = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image  = uniqid('prod_') . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/products/' . $image);
        $image_sql = ", image='$image'";
    }

    $edit_qty = max(0, intval($_POST['quantity'] ?? 0));

    $sql = "UPDATE products SET title='$title', price=$price, description='$description', category='$category', quantity=$edit_qty $image_sql WHERE id=$id";
    $conn->query($sql);
    $success_msg = "Product updated!";
}

// ── DELETE PRODUCT (Existing Logic) ───────────────────────────────────────────
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id=$id");
    $success_msg = 'Product deleted successfully.';
}

// ── FETCH DATA FOR DISPLAY ─────────────────────────────────────────────────────
$edit_product = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $res = $conn->query("SELECT * FROM products WHERE id=$id");
    $edit_product = $res->fetch_assoc();
}

$products = [];
$res = $conn->query("SELECT * FROM products ORDER BY id DESC");
while ($res && $row = $res->fetch_assoc()) { $products[] = $row; }

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

    <div class="panel_sidebar">
        <div class="sidebar-logo">⚡ Admin <span>Panel</span></div>
        <ul>
            <li><a href="adminpage.php?section=stock" class="<?= ($active_section === 'stock') ? 'active' : '' ?>"><span>📦</span> Stock</a></li>
            <li><a href="adminpage.php?section=add" class="<?= ($active_section === 'add') ? 'active' : '' ?>"><span>➕</span> Add Product</a></li>
            <li><a href="adminpage.php?section=orders" class="<?= ($active_section === 'orders') ? 'active' : '' ?>"><span>🛒</span> View Orders</a></li>
            <li><a href="index.php"><span>🌐</span> Visit Site</a></li>
        </ul>
        <div class="sidebar-footer"><a href="logout.php">🚪 Logout</a></div>
    </div>

    <div class="main-content">
        <?php if ($success_msg): ?><div class="alert alert-success" id="flash-msg">✅ <?= $success_msg ?></div><?php endif; ?>

        <?php if ($active_section === 'stock'): ?>
        <div class="card">
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>ID</th><th>Image</th><th>Title</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td>#<?= $p['id'] ?></td>
                            <td><img src="products/<?= $p['image'] ?>" class="product-thumb"></td>
                            <td><strong><?= htmlspecialchars($p['title']) ?></strong></td>
                            <td class="price-cell">Rs. <?= number_format($p['price'], 2) ?></td>
                            <td>
                                <?php $qty = intval($p['quantity'] ?? 0); ?>
                                <span <?= $qty === 0 ? 'style="color:#dc2626;font-weight:800;font-size:1rem;"' : '' ?>>
                                    <?= $qty ?>
                                </span>
                            </td>
                            <td><div class="action-btns">
                                <a href="adminpage.php?edit=<?= $p['id'] ?>" class="btn-edit">✏️ Edit</a>
                                <a href="adminpage.php?delete=<?= $p['id'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this product?')">🗑️ Delete</a>
                            </div></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($active_section === 'orders'): ?>
        <div class="card">
            <h2>🛒 Customer Orders</h2>
            <?php if (empty($orders)): ?>
                <div class="empty-state"><div class="icon">📝</div><p>No orders placed yet.</p></div>
            <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer Info</th>
                            <th>Product ID</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Shipping Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><small><?= $o['order_date'] ?></small></td>
                            <td>
                                <strong><?= htmlspecialchars($o['customer_name']) ?></strong><br>
                                <small><?= htmlspecialchars($o['email']) ?></small><br>
                                <span class="badge" style="background:#4cd137"><?= htmlspecialchars($o['payment_method']) ?></span>
                            </td>
                            <td>#<?= $o['product_id'] ?></td>
                            <td><?= $o['quantity'] ?></td>
                            <td class="price-cell">Rs. <?= number_format($o['total_price'], 2) ?></td>
                            <td><small><?= htmlspecialchars($o['shipping_address']) ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($active_section === 'add'): ?>
        <div class="card">
            <h2>➕ Add New Product</h2>
            <form action="adminpage.php?section=add" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group"><label>Title</label><input type="text" name="title" required></div>
                    <div class="form-group"><label>Price (Rs.)</label><input type="number" name="price" required></div>
                    <div class="form-group"><label>Category</label><input type="text" name="category" required></div>
                    <div class="form-group"><label>Stock Quantity</label><input type="number" name="quantity" min="0" value="0" required></div>
                    <div class="form-group"><label>Image</label><input type="file" name="image"></div>
                    <div class="form-group full-width"><label>Description</label><textarea name="description" required></textarea></div>
                </div>
                <button type="submit" name="add_product" class="btn-submit">➕ Add Product</button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($active_section === 'edit' && $edit_product): ?>
        <div class="card">
            <h2>✏️ Edit Product &mdash; <em><?= htmlspecialchars($edit_product['title']) ?></em></h2>
            <form action="adminpage.php?section=stock" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="product_id" value="<?= $edit_product['id'] ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="<?= htmlspecialchars($edit_product['title']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Price (Rs.)</label>
                        <input type="number" name="price" step="0.01" value="<?= $edit_product['price'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" value="<?= htmlspecialchars($edit_product['category']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" name="quantity" min="0" value="<?= intval($edit_product['quantity'] ?? 0) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>New Image <small style="color:#999">(leave blank to keep current)</small></label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                    <?php if (!empty($edit_product['image'])): ?>
                    <div class="form-group full-width" style="align-items:flex-start">
                        <label>Current Image</label>
                        <img src="products/<?= htmlspecialchars($edit_product['image']) ?>" alt="Current product image" class="edit-preview-img">
                    </div>
                    <?php endif; ?>
                    <div class="form-group full-width">
                        <label>Description</label>
                        <textarea name="description" required><?= htmlspecialchars($edit_product['description']) ?></textarea>
                    </div>
                </div>
                <button type="submit" name="update_product" class="btn-submit">💾 Save Changes</button>
                <a href="adminpage.php?section=stock" class="btn-cancel">✕ Cancel</a>
            </form>
        </div>
        <?php elseif ($active_section === 'edit' && !$edit_product): ?>
        <div class="card">
            <div class="empty-state">
                <div class="icon">🔍</div>
                <p>Product not found. <a href="adminpage.php?section=stock">Back to Stock</a></p>
            </div>
        </div>
        <?php endif; ?>

    </div>

<!-- ── Stock Edit Modal ──────────────────────────────────────────────────── -->
<div id="stockModal" class="stock-modal-overlay" style="display:none;" onclick="if(event.target===this)closeStockModal()">
    <div class="stock-modal-box">
        <h3>📦 Edit Stock Quantity</h3>
        <p id="stockModalTitle" style="color:#666;font-size:0.9rem;margin-bottom:18px;"></p>
        <form method="POST" action="adminpage.php?section=stock">
            <input type="hidden" name="stock_product_id" id="stockProductId">
            <div class="form-group" style="margin-bottom:18px;">
                <label>New Stock Quantity</label>
                <input type="number" name="new_quantity" id="newQuantityInput" min="0" required style="margin-bottom:0;">
            </div>
            <div style="display:flex;gap:10px;">
                <button type="submit" name="update_stock" class="btn-submit" style="margin-top:0;">💾 Save Stock</button>
                <button type="button" class="btn-cancel" style="margin-top:0;" onclick="closeStockModal()">✕ Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openStockModal(id, title, qty) {
    document.getElementById('stockProductId').value = id;
    document.getElementById('stockModalTitle').textContent = title;
    document.getElementById('newQuantityInput').value = qty;
    document.getElementById('stockModal').style.display = 'flex';
}
function closeStockModal() {
    document.getElementById('stockModal').style.display = 'none';
}
</script>

</body>
</html>