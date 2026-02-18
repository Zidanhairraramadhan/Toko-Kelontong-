<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];

try {
    // Start Transaction
    $conn->beginTransaction();

    // 1. Calculate Total
    $total_price = 0;
    $order_items = [];
    
    // Prepare statements
    $stmt_product = $conn->prepare("SELECT price, stock FROM products WHERE id = :id"); // Added stock check
    $stmt_update_stock = $conn->prepare("UPDATE products SET stock = stock - :qty WHERE id = :id");

    foreach ($cart as $product_id => $qty) {
        $stmt_product->execute([':id' => $product_id]);
        $product = $stmt_product->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception("Produk ID $product_id tidak ditemukan.");
        }
        
        // Check Stock
        if ($product['stock'] < $qty) {
            throw new Exception("Stok tidak mencukupi untuk salah satu produk.");
            // Ideally we'd rollback and show specific error to user
        }

        $subtotal = $product['price'] * $qty;
        $total_price += $subtotal;
        
        $order_items[] = [
            'product_id' => $product_id,
            'qty' => $qty,
            'price' => $product['price']
        ];
    }

    // 2. Create Order
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cod';
    
    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_price, status, payment_method) VALUES (:user_id, :total_price, 'pending', :payment_method)");
    $stmt_order->execute([
        ':user_id' => $user_id,
        ':total_price' => $total_price,
        ':payment_method' => $payment_method
    ]);
    $order_id = $conn->lastInsertId();

    // 3. Insert Order Items & Deduct Stock
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :qty, :price)");

    foreach ($order_items as $item) {
        $stmt_item->execute([
            ':order_id' => $order_id,
            ':product_id' => $item['product_id'],
            ':qty' => $item['qty'],
            ':price' => $item['price']
        ]);

        $stmt_update_stock->execute([
            ':qty' => $item['qty'],
            ':id' => $item['product_id']
        ]);
    }

    // Commit Transaction
    $conn->commit();
    
    // 4. Create Notification for Admin
    // We do this AFTER commit (or inside, but if inside, make sure to use the same connection). 
    // Since we committed, let's use a new try-catch or just execute. 
    // Actually, it's safer to include it in the transaction, but if it fails, we don't want to revert the order?
    // Let's include it in the transaction for consistency.
    
    // Re-opening transaction or just running query if we assumed commit closed it?
    // Wait, $conn->commit() commits the transaction. The connection is back to auto-commit mode.
    
    try {
        $stmt_notif = $conn->prepare("INSERT INTO notifications (order_id, message) VALUES (:order_id, :message)");
        $notif_msg = "Pesanan baru #" . $order_id . " dari " . htmlspecialchars($_SESSION['full_name']); // Assuming name is in session or query it.
        // Let's query user name if not in session, but session usually has it.
        // In checkout.php we have $user_id.
        $stmt_notif->execute([
            ':order_id' => $order_id,
            ':message' => $notif_msg
        ]);
    } catch (Exception $e) {
        // Ignore notification error, don't fail the order
    }

    // Clear Cart
    unset($_SESSION['cart']);

    // Show Success with Payment Info
    $msg = "Pesanan berhasil! ";
    if ($payment_method == 'cod') {
        $msg .= "Silakan siapkan uang tunai saat kurir datang.";
    } else {
        $msg .= "Silakan lakukan transfer sesuai rekening yang dipilih.";
    }
    
    echo "<script>alert('$msg'); window.location.href='index.php';</script>";

} catch (Exception $e) {
    // Rollback if error
    $conn->rollBack();
    echo "<script>alert('Gagal memproses pesanan: " . $e->getMessage() . "'); window.location.href='keranjang.php';</script>";
}
?>
