<?php
session_start();
require_once 'config/database.php';
require_once 'includes/header.php';

// Initialize Cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add to Cart Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $product_id = $_POST['product_id'];
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]++;
    } else {
        $_SESSION['cart'][$product_id] = 1;
    }
    // Redirect to avoid resubmission
    header("Location: keranjang.php");
    exit;
}

// Remove from Cart
if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $id = $_GET['id'];
    unset($_SESSION['cart'][$id]);
    header("Location: keranjang.php");
    exit;
}

// Fetch Cart Products
$cart_items = [];
$total_price = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $qty = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $qty;
        $total_price += $subtotal;
        $product['qty'] = $qty; // Add qty to product array
        $product['subtotal'] = $subtotal;
        $cart_items[] = $product;
    }
}
?>

<section style="max-width: 800px; margin: 40px auto; padding: 20px;">
    <div class="section-title">
        <h2>Keranjang Belanja</h2>
    </div>

    <?php if (empty($cart_items)): ?>
        <p style="text-align: center;">Keranjang Anda masih kosong. <a href="produk.php">Belanja sekarang</a>.</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem;">
            <thead>
                <tr style="border-bottom: 1px solid #ddd;">
                    <th style="text-align: left; padding: 10px;">Produk</th>
                    <th style="text-align: center; padding: 10px;">Jumlah</th>
                    <th style="text-align: right; padding: 10px;">Harga</th>
                    <th style="text-align: right; padding: 10px;">Subtotal</th>
                    <th style="text-align: center; padding: 10px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;">
                        <span style="font-weight: bold;"><?php echo htmlspecialchars($item['name']); ?></span>
                    </td>
                    <td style="text-align: center; padding: 10px;"><?php echo $item['qty']; ?></td>
                    <td style="text-align: right; padding: 10px;">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                    <td style="text-align: right; padding: 10px;">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                    <td style="text-align: center; padding: 10px;">
                        <a href="keranjang.php?action=remove&id=<?php echo $item['id']; ?>" style="color: red;"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="text-align: right; margin-bottom: 20px;">
            <h3>Total: Rp <?php echo number_format($total_price, 0, ',', '.'); ?></h3>
        </div>

        <div style="text-align: right;">
            <a href="produk.php" style="margin-right: 10px; color: #666;">Lanjut Belanja</a>
            <?php if(isset($_SESSION['user_id'])): ?>
                <form action="checkout.php" method="POST" style="display: inline;">
                    <button type="submit" class="btn-cta" style="border: none; cursor: pointer;">Checkout Sekarang</button>
                </form>
            <?php else: ?>
                <a href="login.php" class="btn-cta">Login untuk Checkout</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
