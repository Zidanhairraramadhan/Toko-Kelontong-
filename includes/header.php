<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Kelontong Modern</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo">
            <a href="index.php">
                <i class="fas fa-store"></i> Toko Kelontong
            </a>
        </div>
        
        <ul class="nav-links">
            <li><a href="index.php">Beranda</a></li>
            <li><a href="index.php#tentang">Tentang Toko</a></li>
            <li><a href="index.php#kontak">Kontak</a></li>
            <li><a href="produk.php">Produk</a></li>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <li><a href="admin/index.php" style="color: var(--accent-color);">Dashboard Admin</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="btn-produk" style="background-color: #d32f2f;">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php" class="btn-produk">Login</a></li>
            <?php endif; ?>
        </ul>

        <div class="burger">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
        </div>
    </nav>
</header>
<main>
