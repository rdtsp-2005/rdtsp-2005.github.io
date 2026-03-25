<?php 
require "php/functions.php";

if(!empty($_SESSION['cart'])){
    
    $customer_name    = trim($_POST['customer_name'] ?? 'Customer');
    $shipping_address = trim($_POST['shipping_address'] ?? 'Not provided');
    $email            = trim($_POST['email'] ?? $_SESSION['email'] ?? 'guest@example.com');
    $payment          = trim($_POST['payment_method'] ?? 'Cash on Delivery');

    $mysqli = dbConnect();
    $cart_ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));

    $stmt = $mysqli->prepare("SELECT id, price, quantity, title FROM products WHERE id IN ($placeholders)");
    $types = str_repeat('i', count($cart_ids));
    $stmt->bind_param($types, ...$cart_ids);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $mysqli->begin_transaction();

    try {
        foreach($products as $product){
            $pid = $product['id'];
            $qty_bought = $_SESSION['cart'][$pid];

            if($product['quantity'] < $qty_bought){
                throw new Exception("Insufficient stock for " . $product['title']);
            }

            $subtotal = $product['price'] * $qty_bought;

            $stmt_order = $mysqli->prepare("INSERT INTO orders (product_id, quantity, total_price, email, payment_method, customer_name, shipping_address) VALUES (?,?,?,?,?,?,?)");
            $stmt_order->bind_param("iidssss", $pid, $qty_bought, $subtotal, $email, $payment, $customer_name, $shipping_address);
            $stmt_order->execute();

            $stmt_update = $mysqli->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
            $stmt_update->bind_param("ii", $qty_bought, $pid);
            $stmt_update->execute();
        }
        
        $mysqli->commit();
        unset($_SESSION['cart']);
        
        header("Location: thank_you.php?name=" . urlencode($customer_name));
        exit();

    } catch(Exception $e) {
        $mysqli->rollback();
        echo "<script>alert('Order failed: " . addslashes($e->getMessage()) . "'); window.location.href='cart.php';</script>";
        exit();
    }
} else {
    header("Location: cart.php");
    exit();
}
?>