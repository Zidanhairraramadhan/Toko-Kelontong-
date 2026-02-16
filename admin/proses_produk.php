<?php
session_start();
require_once '../config/database.php';

// Check Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// --- DELETE PRODUCT ---
if ($action == 'delete') {
    $id = $_GET['id'];
    try {
        $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: produk.php?msg=deleted");
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// --- ADD/EDIT PRODUCT (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category'];
    $is_popular = isset($_POST['is_popular']) ? 1 : 0;
    
    // Image Upload Logic
    $image = $_POST['existing_image'] ?? 'default.jpg';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        // Simple check (allow jpg, png, jpeg)
        if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg") {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = basename($_FILES["image"]["name"]);
            }
        }
    }

    try {
        if ($id) {
            // Update
            $sql = "UPDATE products SET name=:name, description=:description, price=:price, stock=:stock, category=:category, is_popular=:is_popular, image=:image WHERE id=:id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id);
        } else {
            // Insert
            $sql = "INSERT INTO products (name, description, price, stock, category, is_popular, image) VALUES (:name, :description, :price, :stock, :category, :is_popular, :image)";
            $stmt = $conn->prepare($sql);
        }
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':stock', $stock);
        $stmt->bindParam(':category', $category);
        $stmt->bindParam(':is_popular', $is_popular);
        $stmt->bindParam(':image', $image);
        
        $stmt->execute();
        header("Location: produk.php?msg=success");

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
