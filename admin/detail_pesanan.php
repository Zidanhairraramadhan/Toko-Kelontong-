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
    <title>Detail Pesanan #<?php echo $order_id; ?> - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .detail-wrapper {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: var(--transition-smooth);
        }
        .back-link:hover {
            color: var(--primary-color);
            transform: translateX(-4px);
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-block {
            background: var(--bg-light);
            padding: 15px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-color);
        }
        .info-block p {
            margin-bottom: 8px;
            font-size: 0.92rem;
        }
        .info-block p:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body style="background: var(--bg-light); color: var(--text-color);">

<div class="detail-wrapper">
    <a href="pesanan.php" class="back-link"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Pesanan</a>
    
    <div class="admin-card">
        <h2>Detail Pesanan #<?php echo $order['id']; ?></h2>
        <div style="border-bottom: 1px solid var(--border-color); margin-bottom: 20px; padding-bottom: 10px;"></div>
        
        <div class="info-grid">
            <div class="info-block">
                <h4 style="margin-bottom: 10px; color: var(--primary-dark);"><i class="fas fa-user"></i> Informasi Pelanggan</h4>
                <p><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($order['username']); ?></p>
                <p><strong>Metode Bayar:</strong> <span style="text-transform: uppercase; font-weight: 600;"><?php echo str_replace('_', ' ', $order['payment_method']); ?></span></p>
                <p><strong>Status:</strong> <span class="status-badge" style="display:inline-block; font-size:0.8rem; font-weight:600;"><?php echo ucfirst($order['status']); ?></span></p>
            </div>
            
            <div class="info-block">
                <h4 style="margin-bottom: 10px; color: var(--primary-dark);"><i class="fas fa-shipping-fast"></i> Informasi Pengiriman</h4>
                <p><strong>Tipe Pengiriman:</strong> <?php echo $order['shipping_type'] == 'delivery' ? 'Antar ke Rumah' : 'Ambil di Toko'; ?></p>
                <p><strong>Ongkos Kirim:</strong> Rp <?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?></p>
                <?php if($order['shipping_type'] == 'delivery'): ?>
                    <p><strong>Alamat Pengiriman:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <h3 style="margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid var(--border-color); padding-bottom: 8px;">Daftar Produk yang Dibeli</h3>
        <table class="admin-table" style="margin-bottom: 20px;">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal_items = 0;
                foreach($items as $item): 
                    $subtotal_item = $item['price'] * $item['quantity'];
                    $subtotal_items += $subtotal_item;
                ?>
                <tr>
                    <td>
                        <img src="../assets/images/<?php echo $item['image']; ?>" alt="img" style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--radius-sm);">
                    </td>
                    <td><strong style="color: var(--text-color);"><?php echo htmlspecialchars($item['name']); ?></strong></td>
                    <td>Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td style="text-align: right; font-weight: 600;">Rp <?php echo number_format($subtotal_item, 0, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="max-width: 320px; margin-left: auto; border-top: 1px dashed var(--border-color); padding-top: 15px;">
            <div style="display: flex; justify-content: space-between; font-size: 0.92rem; color: var(--text-muted); margin-bottom: 8px;">
                <span>Subtotal Produk</span>
                <span>Rp <?php echo number_format($subtotal_items, 0, ',', '.'); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.92rem; color: var(--text-muted); margin-bottom: 8px;">
                <span>Ongkos Kirim</span>
                <span>Rp <?php echo number_format($order['shipping_fee'], 0, ',', '.'); ?></span>
            </div>
            <?php if($order['voucher_code']): ?>
                <div style="display: flex; justify-content: space-between; font-size: 0.92rem; color: var(--text-muted); margin-bottom: 8px;">
                    <span>Diskon (<?php echo htmlspecialchars($order['voucher_code']); ?>)</span>
                    <span style="color: #ef4444;">- Rp <?php echo number_format($order['discount_amount'], 0, ',', '.'); ?></span>
                </div>
            <?php endif; ?>
            
            <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 700; color: var(--text-color); border-top: 1px solid var(--border-color); padding-top: 10px; margin-top: 10px;">
                <span>Total Bayar</span>
                <span style="color: var(--primary-dark);">Rp <?php echo number_format($order['total_price'], 0, ',', '.'); ?></span>
            </div>
            
            <div style="background-color: #fffbeb; color: #d97706; padding: 10px; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; display: flex; justify-content: space-between; margin-top: 12px; border: 1px solid #fde68a;">
                <span><i class="fas fa-crown"></i> Poin Didapatkan</span>
                <span>+<?php echo number_format($order['points_earned'], 0, ',', '.'); ?> Poin</span>
            </div>
        </div>
    </div>
</div>

</body>
</html>
