<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') { header("Location: ../login.php"); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .form-container { max-width: 600px; margin: 50px auto; padding: 20px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 8px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn-submit { background: #2e7d32; color: white; border: none; padding: 10px 20px; cursor: pointer; width: 100%; font-size: 1rem; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Tambah Produk Baru</h2>
        <form action="proses_produk.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="price" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stock" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="category">
                    <option value="Sembako">Sembako</option>
                    <option value="Makanan Instan">Makanan Instan</option>
                    <option value="Minuman">Minuman</option>
                    <option value="Bumbu Dapur">Bumbu Dapur</option>
                    <option value="Perlengkapan Rumah">Perlengkapan Rumah</option>
                </select>
            </div>
            <div class="form-group">
                <label>Gambar Produk</label>
                <input type="file" name="image">
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_popular" value="1"> Produk Populer?
                </label>
            </div>
            <button type="submit" class="btn-submit">Simpan Produk</button>
            <br><br>
            <a href="produk.php">Kembali</a>
        </form>
    </div>
</body>
</html>
