<?php
require_once 'config/database.php';
require_once 'includes/header.php';

// Fetch Popular Products
try {
    $stmt = $conn->prepare("SELECT * FROM products WHERE is_popular = 1 LIMIT 4");
    $stmt->execute();
    $popular_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!-- Hero Section / Slideshow -->
<section class="hero-slider">
    <!-- Slide 1 -->
    <div class="slide active" style="background-image: url('https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');">
        <div class="slide-content">
            <h2>Belanja Harian Lebih Hemat</h2>
            <p>Dapatkan diskon spesial untuk member baru!</p>
            <a href="produk.php" class="btn-cta">Belanja Sekarang</a>
        </div>
    </div>
    <!-- Slide 2 -->
    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1578916171728-46686eac8d58?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');">
        <div class="slide-content">
            <h2>Produk Segar Setiap Hari</h2>
            <p>Kualitas terbaik langsung dari petani.</p>
            <a href="produk.php" class="btn-cta">Lihat Produk</a>
        </div>
    </div>
    <!-- Slide 3 -->
    <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1604719312566-b7e2b0084adb?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');">
        <div class="slide-content">
            <h2>Pengiriman Cepat</h2>
            <p>Pesan pagi, sampai sore di depan pintu rumah Anda.</p>
            <a href="produk.php" class="btn-cta">Pesan Antar</a>
        </div>
    </div>
</section>

<!-- Popular Products -->
<section id="populer">
    <div class="section-title">
        <h2>Produk Terpopuler</h2>
        <p>Pilihan favorit pelanggan kami bulan ini</p>
    </div>
    
    <div class="product-grid">
        <?php foreach($popular_products as $product): ?>
        <div class="product-card">
            <!-- Placeholder image logic for demo -->
            <div class="product-image" style="background-image: url('assets/images/<?php echo $product['image']; ?>'), url('https://via.placeholder.com/300x200?text=Produk');"></div>
            <div class="product-info">
                <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                <p class="product-description"><?php echo htmlspecialchars(substr($product['description'], 0, 50)) . '...'; ?></p>
                <p class="product-price">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></p>
                <button class="btn-add">Tambah ke Keranjang</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- About Section -->
<section id="tentang" style="background-color: var(--white);">
    <div class="section-title">
        <h2>Tentang Kami</h2>
    </div>
    <div class="about-content">
        <p>
            Toko Kelontong Modern hadir untuk memenuhi kebutuhan sehari-hari keluarga Anda. 
            Kami menyediakan berbagai macam produk sembako, makanan ringan, dan kebutuhan rumah tangga lainnya 
            dengan kualitas terbaik dan harga yang kompetitif. Kenyamanan dan kepuasan Anda adalah prioritas kami.
        </p>
    </div>
</section>

<!-- Contact Section -->
<section id="kontak">
    <div class="section-title">
        <h2>Hubungi Kami</h2>
        <p>Kami siap membantu Anda</p>
    </div>
    <div class="contact-container">
        <div class="contact-item">
            <i class="fas fa-map-marker-alt"></i>
            <h3>Alamat</h3>
            <p>Jl. Merdeka No. 123, Jakarta Pusat</p>
        </div>
        <div class="contact-item">
            <i class="fas fa-envelope"></i>
            <h3>Email</h3>
            <p>info@tokokelontong.com</p>
        </div>
        <div class="contact-item">
            <i class="fas fa-phone"></i>
            <h3>Telepon</h3>
            <p>+62 812-3456-7890</p>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
