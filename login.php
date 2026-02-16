<?php
session_start();
require_once 'config/database.php';

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: produk.php");
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        // Note: For this demo, if the hash check fails, we might want to temporarily allow plain text for easier testing if the DB wasn't set up with hashes correctly.
        // But we should stick to password_verify.
        if ($user && password_verify($password, $user['password'])) {
            // Login Success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            if ($user['role'] == 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: produk.php");
            }
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Kelontong</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #2e7d32 0%, #81c784 100%);
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h2 {
            color: #333;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background-color: #1b5e20;
        }
        .error-msg {
            background-color: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }
        .back-link {
            text-align: center;
            margin-top: 1rem;
        }
        .back-link a {
            color: #2e7d32;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-header">
        <h2>Login</h2>
        <p>Silakan masuk ke akun Anda</p>
    </div>

    <?php if($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn-login">Masuk</button>
    </form>

    <div class="back-link">
        <a href="index.php">Kembali ke Beranda</a>
    </div>
    
    <!-- Info for Demo -->
    <div style="margin-top: 20px; font-size: 0.8rem; color: #888; text-align: center; border-top: 1px solid #eee; padding-top: 10px;">
        <p>Demo Akun:</p>
        <p>Admin: admin / admin</p>
        <p>User: user / user</p>
    </div>
</div>

</body>
</html>
