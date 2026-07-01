<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    $cart_count = array_sum($_SESSION['cart']);
}

$user_points = 0;
if (isset($_SESSION['user_id'])) {
    require_once 'config/database.php';
    try {
        $stmt_user = $conn->prepare("SELECT points FROM users WHERE id = :id LIMIT 1");
        $stmt_user->execute([':id' => $_SESSION['user_id']]);
        $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);
        if ($user_data) {
            $user_points = intval($user_data['points']);
        }
    } catch (Exception $e) {
        // Fallback silently
    }
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
            <li>
                <a href="keranjang.php" class="nav-cart" title="Keranjang Belanja">
                    <i class="fas fa-shopping-cart" style="font-size: 1.1rem;"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <li><a href="admin/index.php" style="color: var(--accent-color);">Dashboard Admin</a></li>
                <?php else: ?>
                    <li style="display: flex; align-items: center;">
                        <span class="member-badge" title="Poin Member Anda">
                            <i class="fas fa-crown"></i> <?php echo number_format($user_points, 0, ',', '.'); ?> Poin
                        </span>
                    </li>
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
