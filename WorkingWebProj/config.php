<?php
define('SERVER', 'localhost');
define('USERNAME', 'root');
define('PASSWORD', '');
define('DATABASE', 'customers_db');
define('PORT', 3307);   // ✅ your MySQL port

$conn = new mysqli(SERVER, USERNAME, PASSWORD, DATABASE, PORT);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>