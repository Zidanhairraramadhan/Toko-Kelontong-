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
    $product_id = intval($_POST['product_id']);
    $qty = isset($_POST['qty']) ? max(1, intval($_POST['qty'])) : 1;
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = $qty;
    }
    header("Location: keranjang.php");
    exit;
}

// Update Quantity Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_qty') {
    $product_id = intval($_POST['product_id']);
    $new_qty    = intval($_POST['qty']);

    if ($new_qty <= 0) {
        // Remove item if qty <= 0
        unset($_SESSION['cart'][$product_id]);
    } else {
        $_SESSION['cart'][$product_id] = $new_qty;
    }
    header("Location: keranjang.php");
    exit;
}

// Remove from Cart
if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    $id = intval($_GET['id']);
    unset($_SESSION['cart'][$id]);
    header("Location: keranjang.php");
    exit;
}

// Fetch Cart Products
$cart_items  = [];
$total_price = 0;
if (!empty($_SESSION['cart'])) {
    $ids  = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $stmt = $conn->query("SELECT * FROM products WHERE id IN ($ids)");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $qty      = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $qty;
        $total_price += $subtotal;
        $product['qty']      = $qty;
        $product['subtotal'] = $subtotal;
        $cart_items[] = $product;
    }
}
?>

<section style="max-width: 860px; margin: 40px auto; padding: 20px;">
    <div class="section-title">
        <h2>Keranjang Belanja</h2>
    </div>

    <?php if (empty($cart_items)): ?>
        <p style="text-align: center;">Keranjang Anda masih kosong. <a href="produk.php">Belanja sekarang</a>.</p>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem;">
            <thead>
                <tr style="border-bottom: 2px solid #ddd;">
                    <th style="text-align: left; padding: 10px;">Produk</th>
                    <th style="text-align: center; padding: 10px;">Jumlah</th>
                    <th style="text-align: right; padding: 10px;">Harga</th>
                    <th style="text-align: right; padding: 10px;">Subtotal</th>
                    <th style="text-align: center; padding: 10px;">Hapus</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px;">
                        <span style="font-weight: bold;"><?php echo htmlspecialchars($item['name']); ?></span>
                    </td>
                    <td style="text-align: center; padding: 10px;">
                        <!-- Quantity Controls -->
                        <form action="keranjang.php" method="POST"
                              style="display: inline-flex; align-items: center; gap: 6px;">
                            <input type="hidden" name="action"     value="update_qty">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">

                            <!-- Tombol Kurang -->
                            <button type="submit" name="qty" value="<?php echo $item['qty'] - 1; ?>"
                                    style="width:30px; height:30px; font-size:1.1rem; cursor:pointer;
                                           border:1px solid #ccc; border-radius:4px; background:#f5f5f5;
                                           line-height:1;"
                                    title="Kurangi">−</button>

                            <!-- Input Langsung -->
                            <input type="number" name="qty" value="<?php echo $item['qty']; ?>"
                                   min="0" max="<?php echo $item['stock']; ?>"
                                   style="width:50px; text-align:center; border:1px solid #ccc;
                                          border-radius:4px; padding:4px; font-size:0.95rem;"
                                   onchange="this.form.submit()">

                            <!-- Tombol Tambah -->
                            <button type="submit" name="qty" value="<?php echo $item['qty'] + 1; ?>"
                                    <?php if ($item['qty'] >= $item['stock']) echo 'disabled title="Stok habis"'; ?>
                                    style="width:30px; height:30px; font-size:1.1rem; cursor:pointer;
                                           border:1px solid #ccc; border-radius:4px; background:#f5f5f5;
                                           line-height:1;"
                                    title="Tambah">+</button>
                        </form>
                        <div style="font-size:0.78rem; color:#888; margin-top:3px;">
                            Stok: <?php echo $item['stock']; ?>
                        </div>
                    </td>
                    <td style="text-align: right; padding: 10px;">
                        Rp <?php echo number_format($item['price'], 0, ',', '.'); ?>
                    </td>
                    <td style="text-align: right; padding: 10px; font-weight: bold;">
                        Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?>
                    </td>
                    <td style="text-align: center; padding: 10px;">
                        <a href="keranjang.php?action=remove&id=<?php echo $item['id']; ?>"
                           style="color: red;"
                           onclick="return confirm('Hapus produk ini dari keranjang?')">
                            <i class="fas fa-trash"></i>
                        </a>
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <form action="pembayaran.php" method="POST" style="display: inline;">
                    <button type="submit" class="btn-cta" style="border: none; cursor: pointer;">Lanjut ke Pembayaran</button>
                </form>
            <?php else: ?>
                <a href="login.php" class="btn-cta">Login untuk Checkout</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</section>

<?php require_once 'includes/footer.php'; ?>
