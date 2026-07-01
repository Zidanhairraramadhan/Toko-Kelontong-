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
    $payment_method   = isset($_POST['payment_method']) ? $_POST['payment_method'] : 'cod';
    $shipping_type    = isset($_POST['shipping_type']) ? $_POST['shipping_type'] : 'pickup';
    $shipping_fee     = ($shipping_type == 'delivery') ? 10000 : 0;
    $shipping_address = isset($_POST['shipping_address']) && $shipping_type == 'delivery' ? trim($_POST['shipping_address']) : null;
    
    $voucher_code     = isset($_POST['voucher_code']) && !empty($_POST['voucher_code']) ? trim($_POST['voucher_code']) : null;
    $discount_amount  = isset($_POST['discount_amount']) ? floatval($_POST['discount_amount']) : 0;
    
    // Calculate final payment total
    $final_payment = max(0, $total_price + $shipping_fee - $discount_amount);
    
    // Points calculation: 100 points for every Rp 10.000 spent
    $points_earned = floor($final_payment / 10000) * 100;
    
    // Add bonus points if voucher type is cashback_points
    if ($voucher_code) {
        $stmt_v = $conn->prepare("SELECT type, value FROM vouchers WHERE code = :code AND active = 1 LIMIT 1");
        $stmt_v->execute([':code' => $voucher_code]);
        $v_data = $stmt_v->fetch(PDO::FETCH_ASSOC);
        if ($v_data && $v_data['type'] == 'cashback_points') {
            $points_earned += intval($v_data['value']);
        }
    }
    
    $stmt_order = $conn->prepare("
        INSERT INTO orders 
        (user_id, total_price, payment_method, shipping_type, shipping_fee, shipping_address, voucher_code, discount_amount, points_earned, status) 
        VALUES 
        (:user_id, :total_price, :payment_method, :shipping_type, :shipping_fee, :shipping_address, :voucher_code, :discount_amount, :points_earned, 'pending')
    ");
    $stmt_order->execute([
        ':user_id'          => $user_id,
        ':total_price'      => $final_payment,
        ':payment_method'   => $payment_method,
        ':shipping_type'    => $shipping_type,
        ':shipping_fee'     => $shipping_fee,
        ':shipping_address' => $shipping_address,
        ':voucher_code'     => $voucher_code,
        ':discount_amount'  => $discount_amount,
        ':points_earned'    => $points_earned
    ]);
    $order_id = $conn->lastInsertId();

    // Update User's Membership Points
    $stmt_user_points = $conn->prepare("UPDATE users SET points = points + :points WHERE id = :id");
    $stmt_user_points->execute([
        ':points' => $points_earned,
        ':id'     => $user_id
    ]);

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
