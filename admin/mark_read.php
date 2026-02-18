<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['id'])) {
            // Mark specific notification
            $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id");
            $stmt->execute([':id' => $_POST['id']]);
        } else {
            // Mark all as read (optional feature)
            $conn->query("UPDATE notifications SET is_read = 1 WHERE is_read = 0");
        }
    } catch (PDOException $e) {
        // Silently fail
    }
}
?>
