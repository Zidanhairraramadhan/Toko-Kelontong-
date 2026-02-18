<?php
require_once 'config/database.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        message TEXT,
        is_read BOOLEAN DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    )";
    
    $conn->exec($sql);
    echo "Tabel 'notifications' berhasil dibuat.";
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
