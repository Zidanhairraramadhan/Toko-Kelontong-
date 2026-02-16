<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

// Hitung Total
$total_belanja = 0;
foreach ($_SESSION['cart'] as $product_id => $qty) {
    $stmt = $conn->prepare("SELECT price FROM products WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $total_belanja += $product['price'] * $qty;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Toko Kelontong</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .container { max-width: 800px; margin: 40px auto; padding: 20px; }
        .payment-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .total-section { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .payment-option { 
            border: 2px solid #eee; 
            border-radius: 8px; 
            padding: 15px; 
            margin-bottom: 15px; 
            cursor: pointer; 
            transition: 0.3s;
            display: flex;
            align-items: center;
        }
        .payment-option:hover, .payment-option.selected { border-color: #2e7d32; background-color: #f1f8e9; }
        .payment-option input { margin-right: 15px; transform: scale(1.5); }
        .payment-logo { font-size: 2rem; margin-right: 15px; color: #555; width: 50px; text-align: center; }
        
        .bank-details { display: none; margin-left: 45px; margin-top: 10px; color: #666; font-size: 0.9rem; }
        
        .btn-confirm { 
            background: #2e7d32; color: white; border: none; padding: 15px; 
            width: 100%; border-radius: 5px; font-size: 1.1rem; font-weight: bold; cursor: pointer; 
            margin-top: 20px;
        }
        .btn-confirm:hover { background: #1b5e20; }
    </style>
    <script>
        function selectPayment(id) {
            // Reset all
            document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
            document.querySelectorAll('.bank-details').forEach(el => el.style.display = 'none');
            
            // Select current
            document.getElementById('opt-'+id).classList.add('selected');
            document.getElementById('radio-'+id).checked = true;
            
            // Show details if bank
            if(id === 'bca' || id === 'bri') {
                document.getElementById('detail-'+id).style.display = 'block';
            }
        }
    </script>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="payment-card">
        <div class="total-section">
            <h3>Total Pembayaran</h3>
            <h1 style="color: #2e7d32;">Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></h1>
        </div>

        <form action="checkout.php" method="POST">
            <h4 style="margin-bottom: 20px;">Pilih Metode Pembayaran:</h4>

            <!-- COD -->
            <div class="payment-option" id="opt-cod" onclick="selectPayment('cod')">
                <input type="radio" name="payment_method" id="radio-cod" value="cod" required>
                <div class="payment-logo"><i class="fas fa-hand-holding-usd"></i></div>
                <div>
                    <strong>Bayar Ditempat (COD)</strong>
                    <div style="font-size: 0.9rem; color: #777;">Bayar tunai saat kurir sampai di rumah Anda.</div>
                </div>
            </div>

            <!-- Transfer BCA -->
            <div class="payment-option" id="opt-bca" onclick="selectPayment('bca')">
                <input type="radio" name="payment_method" id="radio-bca" value="transfer_bca">
                <div class="payment-logo"><i class="fas fa-university"></i></div>
                <div>
                    <strong>Transfer Bank BCA</strong>
                    <div class="bank-details" id="detail-bca">
                        A/N: Toko Kelontong<br>
                        No. Rek: 123-456-7890
                    </div>
                </div>
            </div>

            <!-- Transfer BRI -->
            <div class="payment-option" id="opt-bri" onclick="selectPayment('bri')">
                <input type="radio" name="payment_method" id="radio-bri" value="transfer_bri">
                <div class="payment-logo"><i class="fas fa-university"></i></div>
                <div>
                    <strong>Transfer Bank BRI</strong>
                    <div class="bank-details" id="detail-bri">
                        A/N: Toko Kelontong<br>
                        No. Rek: 0000-1111-2222
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-confirm">Bayar Sekarang & Selesaikan Pesanan</button>
        </form>
    </div>
</div>

</body>
</html>
