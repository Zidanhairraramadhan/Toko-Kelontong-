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
    <style>
        .admin-wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #1b5e20; color: white; padding: 20px; }
        .sidebar a { color: #dcedc8; display: block; padding: 10px; margin-bottom: 5px; }
        .sidebar a:hover { color: white; background: rgba(255,255,255,0.1); border-radius: 5px; }
        .content { flex: 1; padding: 20px; background: #f4f4f4; }
        
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #2e7d32; color: white; }
        .btn { padding: 5px 10px; border-radius: 3px; color: white; margin-right: 5px; font-size: 0.9rem; }
        .btn-edit { background-color: #ff9800; }
        .btn-delete { background-color: #f44336; }
        .btn-add { background-color: #2e7d32; padding: 10px 20px; display: inline-block; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="admin-wrapper">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="produk.php" style="background: rgba(255,255,255,0.2);"><i class="fas fa-box"></i> Produk</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="content">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Daftar Produk</h2>
            <a href="tambah_produk.php" class="btn btn-add"><i class="fas fa-plus"></i> Tambah Produk</a>
        </div>

        <table>
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
                        <img src="../assets/images/<?php echo $product['image']; ?>" alt="img" style="width: 50px; height: 50px; object-fit: cover;">
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
                        <a href="edit_produk.php?id=<?php echo $product['id']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i></a>
                        <a href="proses_produk.php?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
