<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit; }

$order_id = $_GET['id'];

// Fetch Order Info
$stmt = $conn->prepare("
    SELECT o.*, u.full_name, u.username 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = :id
");
$stmt->execute([':id' => $order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch Items
$stmt = $conn->prepare("
    SELECT oi.*, p.name, p.image 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = :id
");
$stmt->execute([':id' => $order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan #<?php echo $order_id; ?></title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .container { max-width: 800px; margin: 40px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .order-header { border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .item-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #eee; }
        .item-info { display: flex; align-items: center; gap: 15px; }
        .back-btn { display: inline-block; margin-top: 20px; color: #2e7d32; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body style="background: #f4f4f4;">

<div class="container">
    <div class="order-header">
        <h2>Detail Pesanan #<?php echo $order['id']; ?></h2>
        <p><strong>Pelanggan:</strong> <?php echo htmlspecialchars($order['full_name']); ?> (<?php echo htmlspecialchars($order['username']); ?>)</p>
        <p><strong>Tanggal:</strong> <?php echo date('d F Y H:i', strtotime($order['created_at'])); ?></p>
        <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
    </div>

    <h3>Produk Dibeli</h3>
    <?php foreach($items as $item): ?>
    <div class="item-row">
        <div class="item-info">
            <img src="../assets/images/<?php echo $item['image']; ?>" alt="img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
            <div>
                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                <p>Qty: <?php echo $item['quantity']; ?> x Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
            </div>
        </div>
        <div>
            <strong>Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></strong>
        </div>
    </div>
    <?php endforeach; ?>

    <div style="text-align: right; margin-top: 20px; font-size: 1.2rem;">
        <strong>Total: Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></strong>
    </div>

    <a href="pesanan.php" class="back-btn">&larr; Kembali ke Daftar Pesanan</a>
</div>

</body>
</html>
