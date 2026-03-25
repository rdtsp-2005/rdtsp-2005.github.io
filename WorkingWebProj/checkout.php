<!-- <?php
require "php/functions.php";
// session_start();

if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])){
    header("Location : index.php");
    exit();
}

$mysqli = dbConnect();
$cart_ids = array_keys($_SESSION['cart']);


$placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
$stmt = $mysqli->prepare("SELECT id, price, quantity FROM products WHERE id IN ($placeholders)");
$types = str_repeat('i', count($cart_ids));
$stmt->bind_param($types, ...$cart_ids);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(mode: MYSQLI_ASSOC);


$mysqli->begin_transaction();

try{
    foreach ($products as $product){
        $product_id = $product['id'];
        $qty_bought = $_SESSION['cart'][$product_id];
        $subtotal = $product['price'] * $qty_bought;

        $stmt_order = $mysqli->prepare("INSERT INTO orders (product_id, quantity, total_price) VALUES (?, ?, ?)");
        $stmt_order->bind_param("iid", $product_id, $qty_bought, $subtotal);
        $stmt_order->execute();

        $stmt_update = $mysqli->prepare("UPDATE products SET quantity = quantity - ? WHERE id=?");
        $stmt_update->bind_param("ii", $qty_bought, $product_id);
        $stmt_update->execute();
    }

    $mysqli->commit();

    unset($_SESSION['cart']);

    echo "<script>alert('Purchase Successful! Stock updated.'); window.location.href='index.php'; </script>";

}catch(Exception $e){
    $mysqli->rollback();
    echo "Error processing order: " . $e->getMessage();

}
?> -->