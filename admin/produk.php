<?php
session_start();
require_once '../config/database.php';

// Authorization Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Fetch Products
try {
    $stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin Toko</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="admin-wrapper">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="produk.php" class="active"><i class="fas fa-box"></i> Produk</a>
        <a href="pesanan.php"><i class="fas fa-shopping-cart"></i> Pesanan</a>
        <a href="pelanggan.php"><i class="fas fa-users"></i> Pelanggan</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2>Daftar Produk</h2>
            <a href="tambah_produk.php" class="btn-admin-add" style="margin-bottom: 0;"><i class="fas fa-plus"></i> Tambah Produk</a>
        </div>

        <div class="admin-card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gambar</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <img src="../assets/images/<?php echo $product['image']; ?>" alt="img" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                        <td>
                            <span style="font-weight: bold; color: <?php echo $product['stock'] < 10 ? 'red' : 'green'; ?>">
                                <?php echo $product['stock']; ?>
                            </span>
                        </td>
                        <td><?php echo $product['category']; ?></td>
                        <td>
                            <a href="edit_produk.php?id=<?php echo $product['id']; ?>" class="btn-action btn-action-edit"><i class="fas fa-edit"></i></a>
                            <a href="proses_produk.php?action=delete&id=<?php echo $product['id']; ?>" class="btn-action btn-action-delete" onclick="return confirm('Yakin ingin menghapus?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
