<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Count unread
    $stmt_count = $conn->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0");
    $unread_count = $stmt_count->fetchColumn();

    // Get latest 5 notifications
    $stmt_list = $conn->query("
        SELECT n.*, o.total_price 
        FROM notifications n 
        LEFT JOIN orders o ON n.order_id = o.id 
        ORDER BY n.created_at DESC 
        LIMIT 5
    ");
    $notifications = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'unread_count' => $unread_count,
        'notifications' => $notifications
    ]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
