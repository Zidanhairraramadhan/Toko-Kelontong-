<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Search Logic
$search = isset($_GET['cari']) ? $_GET['cari'] : '';
$sql = "SELECT * FROM products";
if($search) {
    $sql .= " WHERE name LIKE :search OR category LIKE :search";
}

try {
    $stmt = $conn->prepare($sql);
    if($search) {
        $stmt->bindValue(':search', "%$search%");
    }
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<section class="dashboard-produk">
    <div class="section-title">
        <h2>Dashboard Produk</h2>
        <p>Temukan semua kebutuhan Anda di sini</p>
    </div>

    <!-- Search Bar -->
    <div class="search-container" style="text-align: center; margin-bottom: 2rem;">
        <form action="" method="GET">
            <input type="text" name="cari" placeholder="Cari produk..." value="<?php echo htmlspecialchars($search); ?>" 
                   style="padding: 10px; width: 300px; border: 1px solid #ddd; border-radius: 5px;">
            <button type="submit" style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-search"></i> Cari
            </button>
        </form>
    </div>

    <!-- Product Grid -->
    <?php if(count($products) > 0): ?>
    <div class="product-grid">
        <?php foreach($products as $product): ?>
        <div class="product-card">
            <div class="product-image" style="background-image: url('assets/images/<?php echo $product['image']; ?>'), url('https://via.placeholder.com/300x200?text=Produk');"></div>
            <div class="product-info">
                <span style="font-size: 0.8rem; background: #eee; padding: 2px 8px; border-radius: 10px; color: #666;">
                    <?php echo htmlspecialchars($product['category']); ?>
                </span>
                <h3 class="product-title" style="margin-top: 10px;"><?php echo htmlspecialchars($product['name']); ?></h3>
                
                <p class="product-price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                
                <!-- Stock Display -->
                <p style="font-size: 0.9rem; color: #666; margin-bottom: 10px;">
                    Stok: <strong><?php echo $product['stock']; ?></strong>
                </p>

                <div style="display: flex; gap: 10px;">
                    <?php if($product['stock'] > 0): ?>
                        <form action="keranjang.php" method="POST" style="flex: 1;">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" class="btn-add" style="width: 100%;"><i class="fas fa-shopping-cart"></i> + Keranjang</button>
                        </form>
                    <?php else: ?>
                        <button class="btn-add" style="flex: 1; background-color: #ccc; cursor: not-allowed;" disabled>Habis</button>
                    <?php endif; ?>
                    
                    <button class="btn-add" style="flex: 0; background: var(--accent-color);"><i class="fas fa-heart"></i></button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <p style="text-align: center;">Produk tidak ditemukan.</p>
    <?php endif; ?>

</section>

<?php require_once 'includes/footer.php'; ?>
