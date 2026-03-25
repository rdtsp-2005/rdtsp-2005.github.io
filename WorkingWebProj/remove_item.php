<?php
session_start();

if(isset($_GET['id'])){
    $id_to_remove = $_GET['id'];

    if(isset($_SESSION['cart'][$id_to_remove])){
        unset($_SESSION['cart'][$id_to_remove]);
    }
}

header("Location: cart.php");
exit();

?>