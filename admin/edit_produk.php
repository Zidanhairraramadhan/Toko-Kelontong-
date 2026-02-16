<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit; }

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) { echo "Produk tidak ditemukan!"; exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Produk</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .form-container { max-width: 600px; margin: 50px auto; padding: 20px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 8px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn-submit { background: #ff9800; color: white; border: none; padding: 10px 20px; cursor: pointer; width: 100%; font-size: 1rem; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Produk</h2>
        <form action="proses_produk.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="existing_image" value="<?php echo $product['image']; ?>">
            
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="price" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="category">
                    <?php
                    $categories = ['Sembako', 'Makanan Instan', 'Minuman', 'Bumbu Dapur', 'Perlengkapan Rumah'];
                    foreach ($categories as $cat) {
                        $selected = ($product['category'] == $cat) ? 'selected' : '';
                        echo "<option value='$cat' $selected>$cat</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label>Gambar Produk (Biarkan kosong jika tidak diubah)</label>
                <input type="file" name="image">
                <small>Gambar saat ini: <?php echo $product['image']; ?></small>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_popular" value="1" <?php echo $product['is_popular'] ? 'checked' : ''; ?>> Produk Populer?
                </label>
            </div>
            <button type="submit" class="btn-submit">Update Produk</button>
            <br><br>
            <a href="produk.php">Kembali</a>
        </form>
    </div>
</body>
</html>
