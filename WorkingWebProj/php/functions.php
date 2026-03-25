<?php


    require "config.php";

    function dbConnect(){
        $mysqli = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE);

        if($mysqli->connect_errno != 0){
            return FALSE;
        }else{
            return $mysqli;


        }
    }

    function getCategories(){
        $mysqli = dbConnect();
        $result = $mysqli->query("SELECT DISTINCT category FROM products");
        while($row = $result->fetch_assoc()){
            $categories[] = $row;
        }

        return $categories;

    }

    function searchProducts($query) {
        $mysqli = dbConnect();
        $search = '%' . $query . '%';
        $stmt = $mysqli->prepare("SELECT * FROM products WHERE title LIKE ? OR description LIKE ? OR category LIKE ?");
        $stmt->bind_param("sss", $search, $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    function getHomePageProducts($int){
        $mysqli = dbConnect();
        $result = $mysqli->query("SELECT * FROM products ORDER BY rand() LIMIT $int");
        while($row = $result->fetch_assoc()){
            $data[] = $row;
        }

        return $data;


    }

    function getProductsByCategory($category){
        $mysqli = dbConnect();

        $smtp = $mysqli->prepare("SELECT * FROM products WHERE category = ?");

        $smtp->bind_param("s", $category);

        $smtp->execute();

        $result = $smtp->get_result();

        $data = $result->fetch_all(MYSQLI_ASSOC);

        return $data;


    }

    function getProductByTitle($title){

    $mysqli = dbConnect();

        $stmt = $mysqli->prepare("SELECT * FROM products WHERE title = ?");

        $stmt->bind_param("s", $title);

        $stmt->execute();

        $result = $stmt->get_result();

        $data = $result->fetch_all(MYSQLI_ASSOC);

        return $data;

    }

    function addToCart($product_id, $quantity){
        $mysqli = dbConnect();

        $stmt = $mysqli->prepare("SELECT quantity, title FROM products where id=?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();

        $current_stock = $res['quantity'];
        $product_title = $res['title'];

        if($quantity > $current_stock){
            return "Sorry, only $current_stock number of $product_title are available.";


        }

        if (isset($_SESSION['cart'][$product_id])){
            if (($_SESSION['cart'][$product_id] + $quantity) > $current_stock){
                return "Cannot add more items as it exceeds the amount in stock ($current_stock are in stock),";
            }
            $_SESSION['cart'][$product_id] += $quantity;
        
        }else{
            $_SESSION['cart'][$product_id] = $quantity;
        }

        return "Success";
    }

    function getCartProducts($ids){
        $mysqli = dbConnect();

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $mysqli->prepare("SELECT * FROM products WHERE id IN ($placeholders)");

        $types = str_repeat('i', count($ids));
        $stmt->bind_param($types, ...$ids);

        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }