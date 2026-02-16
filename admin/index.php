<?php
session_start();
require_once '../config/database.php';

// Authorization Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Stats Logic
try {
    // Total Products
    $stmt = $conn->query("SELECT COUNT(*) FROM products");
    $total_products = $stmt->fetchColumn();

    // Total Pending Orders
    $stmt = $conn->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
    $pending_orders = $stmt->fetchColumn();

    // Total Customers
    $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $total_customers = $stmt->fetchColumn();
    
    // Recent Orders
    $stmt = $conn->query("
        SELECT o.id, o.total_price, o.status, o.created_at, u.full_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC LIMIT 5
    ");
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Toko Kelontong</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #1b5e20; color: white; padding: 20px; }
        .sidebar a { color: #dcedc8; display: block; padding: 10px; margin-bottom: 5px; border-radius: 5px; transition: 0.3s; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: white; }
        .content { flex: 1; padding: 20px; background: #f4f4f4; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 20px; }
        .stat-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        
        .bg-green { background-color: #e8f5e9; color: #2e7d32; }
        .bg-orange { background-color: #fff3e0; color: #ff9800; }
        .bg-blue { background-color: #e3f2fd; color: #2196f3; }

        table { width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #fafafa; font-weight: 600; color: #555; }
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; }
        .status-pending { background: #fff3e0; color: #ef6c00; }
        .status-completed { background: #e8f5e9; color: #2e7d32; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="produk.php"><i class="fas fa-box"></i> Produk</a>
        <a href="pesanan.php"><i class="fas fa-shopping-cart"></i> Pesanan</a>
        <a href="pelanggan.php"><i class="fas fa-users"></i> Pelanggan</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Selamat Datang, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h3>
            <a href="../index.php" target="_blank" class="btn-produk"><i class="fas fa-external-link-alt"></i> Lihat Website</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-green"><i class="fas fa-box"></i></div>
                <div>
                    <h3><?php echo $total_products; ?></h3>
                    <p>Total Produk</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-orange"><i class="fas fa-shopping-cart"></i></div>
                <div>
                    <h3><?php echo $pending_orders; ?></h3>
                    <p>Pesanan Baru</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-blue"><i class="fas fa-users"></i></div>
                <div>
                    <h3><?php echo $total_customers; ?></h3>
                    <p>Pelanggan</p>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 1rem;">Pesanan Terakhir</h3>
            <?php if(count($recent_orders) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($recent_orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                        <td>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></td>
                        <td>
                            <span class="status-badge <?php echo 'status-' . $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>Belum ada data pesanan.</p>
            <?php endif; ?>
            <div style="margin-top: 15px; text-align: right;">
                <a href="pesanan.php" style="color: var(--primary-color); font-weight: 600;">Lihat Semua Pesanan &rarr;</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>
