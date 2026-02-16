<?php
require_once 'config/database.php';

try {
    $sql = "ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) DEFAULT 'cod' AFTER total_price";
    $conn->exec($sql);
    echo "Kolom payment_method berhasil ditambahkan ke tabel orders.";
} catch(PDOException $e) {
    echo "Error (Mungkin kolom sudah ada): " . $e->getMessage();
}
?>
