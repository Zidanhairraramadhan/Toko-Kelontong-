<?php
session_start();

// Authorization Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
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
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #1b5e20;
            color: white;
            padding: 20px;
        }
        .sidebar h2 {
            margin-bottom: 2rem;
            font-size: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding-bottom: 1rem;
        }
        .sidebar ul li {
            margin-bottom: 1rem;
        }
        .sidebar ul li a {
            color: #dcedc8;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar ul li a:hover, .sidebar ul li a.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        .main-content {
            flex: 1;
            padding: 2rem;
            background-color: #f5f5f5;
        }
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: #e8f5e9;
            color: #2e7d32;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        .stat-info h3 {
            font-size: 2rem;
            margin-bottom: 5px;
        }
        .stat-info p {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li><a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="#"><i class="fas fa-box"></i> Produk</a></li>
            <li><a href="#"><i class="fas fa-shopping-cart"></i> Pesanan</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Pelanggan</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header-bar">
            <h3>Selamat Datang, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h3>
            <a href="../index.php" target="_blank" class="btn-produk"><i class="fas fa-external-link-alt"></i> Lihat Website</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-box"></i></div>
                <div class="stat-info">
                    <h3>120</h3>
                    <p>Total Produk</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #fff3e0; color: #ff9800;"><i class="fas fa-shopping-cart"></i></div>
                <div class="stat-info">
                    <h3>45</h3>
                    <p>Pesanan Baru</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background-color: #e3f2fd; color: #2196f3;"><i class="fas fa-users"></i></div>
                <div class="stat-info">
                    <h3>1,250</h3>
                    <p>Pelanggan</p>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <h3 style="margin-bottom: 1rem;">Pesanan Terakhir</h3>
            <p>Belum ada data pesanan.</p>
        </div>
    </div>
</div>

</body>
</html>
