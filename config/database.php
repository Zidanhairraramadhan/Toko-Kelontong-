<?php
$host = 'localhost';
$db_name = 'toko_kelontong';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password (usually empty)

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully"; 
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>
