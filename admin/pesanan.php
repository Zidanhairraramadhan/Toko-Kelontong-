<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Update Status Logic
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->execute([':status' => $status, ':id' => $order_id]);
    header("Location: pesanan.php?msg=updated");
    exit;
}

// Fetch Orders
$stmt = $conn->query("
    SELECT o.id, o.total_price, o.status, o.created_at, o.payment_method, u.full_name 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pesanan - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #1b5e20; color: white; padding: 20px; }
        .sidebar a { color: #dcedc8; display: block; padding: 10px; margin-bottom: 5px; border-radius: 5px; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.1); color: white; }
        .content { flex: 1; padding: 20px; background: #f4f4f4; }
        
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2e7d32; color: white; }
        
        .status-select { padding: 5px; border-radius: 4px; border: 1px solid #ddd; }
        .btn-update { background: #2e7d32; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="produk.php"><i class="fas fa-box"></i> Produk</a>
        <a href="pesanan.php" class="active"><i class="fas fa-shopping-cart"></i> Pesanan</a>
        <a href="pelanggan.php"><i class="fas fa-users"></i> Pelanggan</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
        <h2>Daftar Pesanan</h2>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pelanggan</th>
                    <th>Total Harga</th>
                    <th>Metode Bayar</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                <tr>
                    <td>#<?php echo $order['id']; ?></td>
                    <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                    <td>Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></td>
                    <td style="text-transform: uppercase; font-weight: bold; color: #555;">
                        <?php echo str_replace('_', ' ', $order['payment_method']); ?>
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <input type="hidden" name="update_status" value="1">
                            <select name="status" class="status-select" onchange="this.form.submit()">
                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="completed" <?php echo $order['status'] == 'completed' ? 'selected' : ''; ?>>Selesai</option>
                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Batal</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <a href="detail_pesanan.php?id=<?php echo $order['id']; ?>" class="btn-update" style="text-decoration: none;">Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
