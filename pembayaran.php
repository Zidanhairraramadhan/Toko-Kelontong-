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

require_once 'includes/header.php';
?>

<style>
    .checkout-container {
        max-width: var(--wrapper-width);
        margin: 40px auto;
        padding: 0 20px;
    }
    
    .checkout-grid {
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 30px;
    }
    
    .checkout-card {
        background: var(--white);
        padding: 30px;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        margin-bottom: 25px;
    }
    
    .checkout-card h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: var(--text-color);
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 10px;
    }
    
    /* Option Cards */
    .option-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .select-card {
        border: 2px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 18px;
        cursor: pointer;
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        gap: 15px;
        position: relative;
    }
    
    .select-card:hover {
        border-color: rgba(16, 185, 129, 0.4);
        background-color: var(--primary-light);
    }
    
    .select-card.selected {
        border-color: var(--primary-color);
        background-color: var(--primary-light);
    }
    
    .select-card input[type="radio"] {
        transform: scale(1.3);
        accent-color: var(--primary-color);
    }
    
    .select-card i {
        font-size: 1.8rem;
        color: var(--primary-dark);
        width: 40px;
        text-align: center;
    }
    
    .select-card div strong {
        display: block;
        font-size: 0.95rem;
        color: var(--text-color);
        margin-bottom: 3px;
    }
    
    .select-card div span {
        font-size: 0.82rem;
        color: var(--text-muted);
    }
    
    /* Summary List */
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 0.95rem;
        color: var(--text-muted);
    }
    
    .summary-row.total {
        border-top: 1px dashed var(--border-color);
        padding-top: 15px;
        margin-top: 15px;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-color);
    }
    
    .summary-row.points {
        background-color: #fffbeb;
        color: #d97706;
        padding: 10px 14px;
        border-radius: var(--radius-sm);
        margin-top: 10px;
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    /* Voucher Input */
    .voucher-flex {
        display: flex;
        gap: 10px;
        margin-bottom: 8px;
    }
    
    .voucher-flex input {
        flex: 1;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        font-size: 0.9rem;
        text-transform: uppercase;
    }
    
    .voucher-flex input:focus {
        outline: none;
        border-color: var(--primary-color);
    }
    
    .btn-apply {
        background-color: var(--text-color);
        color: var(--white);
        border: none;
        padding: 10px 18px;
        border-radius: var(--radius-sm);
        font-weight: 600;
        cursor: pointer;
        font-size: 0.9rem;
        transition: var(--transition-smooth);
    }
    
    .btn-apply:hover {
        background-color: #020617;
    }
    
    .btn-confirm {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
        color: var(--white);
        border: none;
        padding: 15px;
        width: 100%;
        border-radius: var(--radius-md);
        font-size: 1.05rem;
        font-weight: 700;
        cursor: pointer;
        transition: var(--transition-smooth);
        margin-top: 15px;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);
    }
    
    .btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(16, 185, 129, 0.35);
    }
    
    @media (max-width: 900px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }
        
        .option-group {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="checkout-container">
    <div class="section-title" style="margin-bottom: 2rem; text-align: left;">
        <h2>Selesaikan Pembayaran</h2>
        <p>Silakan lengkapi opsi pengiriman dan metode pembayaran Anda.</p>
    </div>

    <form action="checkout.php" method="POST" id="checkout-form">
        <!-- Hidden Inputs for Form Submission -->
        <input type="hidden" name="shipping_type" id="form-shipping-type" value="pickup">
        <input type="hidden" name="shipping_fee" id="form-shipping-fee" value="0">
        <input type="hidden" name="shipping_address" id="form-shipping-address" value="">
        <input type="hidden" name="voucher_code" id="form-voucher-code" value="">
        <input type="hidden" name="discount_amount" id="form-discount-amount" value="0">

        <div class="checkout-grid">
            <!-- Left Column: Shipping & Payment -->
            <div>
                <!-- Opsi Pengiriman -->
                <div class="checkout-card">
                    <h3>Opsi Pengiriman</h3>
                    <div class="option-group">
                        <div class="select-card shipping-option selected" id="ship-pickup" onclick="selectShipping('pickup')">
                            <input type="radio" name="shipping_type_radio" id="radio-ship-pickup" value="pickup" checked>
                            <i class="fas fa-store-alt"></i>
                            <div>
                                <strong>Ambil Sendiri</strong>
                                <span>Ambil di toko kami (Gratis)</span>
                            </div>
                        </div>
                        <div class="select-card shipping-option" id="ship-delivery" onclick="selectShipping('delivery')">
                            <input type="radio" name="shipping_type_radio" id="radio-ship-delivery" value="delivery">
                            <i class="fas fa-motorcycle"></i>
                            <div>
                                <strong>Antar ke Rumah</strong>
                                <span>Flat ongkir Rp 10.000</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="premium-form-group" id="address-group" style="display: none; margin-top: 15px;">
                        <label>Alamat Pengantaran Lengkap</label>
                        <textarea id="delivery-address" rows="3" placeholder="Masukkan alamat lengkap pengiriman produk..." style="width: 100%; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 10px;" oninput="updateSummary()"></textarea>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div class="checkout-card">
                    <h3>Metode Pembayaran</h3>
                    
                    <div class="payment-option select-card selected" id="opt-cod" onclick="selectPayment('cod')" style="margin-bottom: 12px; border-width: 2px;">
                        <input type="radio" name="payment_method" id="radio-cod" value="cod" checked required>
                        <i class="fas fa-hand-holding-usd"></i>
                        <div>
                            <strong>Bayar Ditempat (COD)</strong>
                            <span>Bayar tunai saat kurir sampai di rumah Anda.</span>
                        </div>
                    </div>

                    <div class="payment-option select-card" id="opt-bca" onclick="selectPayment('bca')" style="margin-bottom: 12px; border-width: 2px;">
                        <input type="radio" name="payment_method" id="radio-bca" value="transfer_bca">
                        <i class="fas fa-university"></i>
                        <div>
                            <strong>Transfer Bank BCA</strong>
                            <div class="bank-details" id="detail-bca" style="display: none; font-size: 0.82rem; color: #475569; margin-top: 6px; border-top: 1px dashed var(--border-color); padding-top: 6px;">
                                A/N: Toko Kelontong Modern<br>
                                No. Rek: <strong>123-456-7890</strong>
                            </div>
                        </div>
                    </div>

                    <div class="payment-option select-card" id="opt-bri" onclick="selectPayment('bri')" style="margin-bottom: 12px; border-width: 2px;">
                        <input type="radio" name="payment_method" id="radio-bri" value="transfer_bri">
                        <i class="fas fa-university"></i>
                        <div>
                            <strong>Transfer Bank BRI</strong>
                            <div class="bank-details" id="detail-bri" style="display: none; font-size: 0.82rem; color: #475569; margin-top: 6px; border-top: 1px dashed var(--border-color); padding-top: 6px;">
                                A/N: Toko Kelontong Modern<br>
                                No. Rek: <strong>0000-1111-2222</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Summary & Voucher -->
            <div>
                <!-- Promo & Voucher -->
                <div class="checkout-card">
                    <h3>Gunakan Voucher Promo</h3>
                    <div class="voucher-flex">
                        <input type="text" id="voucher-code-input" placeholder="Masukkan Kode Voucher (misal: DISKON15)">
                        <button type="button" id="btn-apply-voucher" class="btn-apply" onclick="applyVoucher()">Gunakan</button>
                    </div>
                    <div id="voucher-message" style="font-size: 0.85rem; font-weight: 500; min-height: 20px; margin-top: 5px;"></div>
                    
                    <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 12px; border-top: 1px solid var(--border-color); padding-top: 10px;">
                        <p>Voucher tersedia untuk dicoba:</p>
                        <ul style="list-style: disc; margin-left: 15px; margin-top: 4px;">
                            <li><strong>GRATISONGKIR</strong> (Min. Blj Rp 50.000)</li>
                            <li><strong>DISKON15</strong> (Potongan Rp 15.000, Min. Blj Rp 100.000)</li>
                            <li><strong>CASHBACKPOIN</strong> (Cashback +10.000 Poin, Min. Blj Rp 75.000)</li>
                        </ul>
                    </div>
                </div>

                <!-- Rincian Pembayaran -->
                <div class="checkout-card" style="position: sticky; top: 100px;">
                    <h3>Rincian Pembayaran</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal Belanja</span>
                        <span id="summary-subtotal">Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Biaya Pengiriman</span>
                        <span id="summary-shipping">Rp 0</span>
                    </div>
                    <div class="summary-row">
                        <span>Diskon Voucher</span>
                        <span id="summary-discount">- Rp 0</span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total Pembayaran</span>
                        <span id="summary-total" style="color: var(--primary-dark);">Rp <?php echo number_format($total_belanja, 0, ',', '.'); ?></span>
                    </div>
                    
                    <div class="summary-row points">
                        <span style="display: flex; align-items: center; gap: 5px;"><i class="fas fa-crown"></i> Poin Diperoleh</span>
                        <span id="summary-points">+<?php echo number_format(floor($total_belanja / 10000) * 100, 0, ',', '.'); ?> Poin</span>
                    </div>

                    <button type="submit" class="btn-confirm">Bayar Sekarang & Selesaikan Pesanan</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Global checkout calculation state
    const subtotal = <?php echo $total_belanja; ?>;
    let shippingFee = 0;
    let discount = 0;
    let voucherApplied = null;

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    function updateSummary() {
        const finalTotal = Math.max(0, subtotal + shippingFee - discount);
        
        // Calculate points based on final total: 100 points per Rp 10.000
        let currentPoints = Math.floor(finalTotal / 10000) * 100;
        
        // If voucher is cashback points, add it to points earned
        if (voucherApplied && voucherApplied.type === 'cashback_points') {
            currentPoints += parseInt(voucherApplied.value);
        }
        
        document.getElementById('summary-subtotal').innerText = 'Rp ' + formatRupiah(subtotal);
        document.getElementById('summary-shipping').innerText = 'Rp ' + formatRupiah(shippingFee);
        document.getElementById('summary-discount').innerText = '- Rp ' + formatRupiah(discount);
        document.getElementById('summary-total').innerText = 'Rp ' + formatRupiah(finalTotal);
        document.getElementById('summary-points').innerText = '+' + formatRupiah(currentPoints) + ' Poin';
        
        // Map states to form hidden input elements
        document.getElementById('form-shipping-type').value = document.querySelector('input[name="shipping_type_radio"]:checked').value;
        document.getElementById('form-shipping-fee').value = shippingFee;
        document.getElementById('form-shipping-address').value = document.getElementById('delivery-address').value;
        document.getElementById('form-voucher-code').value = voucherApplied ? voucherApplied.code : '';
        document.getElementById('form-discount-amount').value = discount;
    }

    function selectShipping(type) {
        document.querySelectorAll('.shipping-option').forEach(el => el.classList.remove('selected'));
        document.getElementById('ship-'+type).classList.add('selected');
        document.getElementById('radio-ship-'+type).checked = true;
        
        const addressGroup = document.getElementById('address-group');
        if (type === 'delivery') {
            addressGroup.style.display = 'block';
            shippingFee = 10000;
            document.getElementById('delivery-address').setAttribute('required', 'required');
        } else {
            addressGroup.style.display = 'none';
            shippingFee = 0;
            document.getElementById('delivery-address').removeAttribute('required');
        }
        
        // Recalculate voucher discount if it's free shipping
        if (voucherApplied) {
            if (voucherApplied.type === 'free_shipping') {
                discount = shippingFee; // discounts the shipping cost
            }
        }
        
        updateSummary();
    }

    function selectPayment(id) {
        document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
        document.querySelectorAll('.bank-details').forEach(el => el.style.display = 'none');
        
        document.getElementById('opt-'+id).classList.add('selected');
        document.getElementById('radio-'+id).checked = true;
        
        if (id === 'bca' || id === 'bri') {
            document.getElementById('detail-'+id).style.display = 'block';
        }
    }

    function applyVoucher() {
        const codeInput = document.getElementById('voucher-code-input');
        const code = codeInput.value.trim();
        const msgEl = document.getElementById('voucher-message');
        
        if (!code) {
            msgEl.innerText = 'Silakan masukkan kode voucher!';
            msgEl.style.color = '#ef4444';
            return;
        }
        
        fetch('cek_voucher.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                code: code,
                total: subtotal
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                voucherApplied = {
                    code: data.code,
                    type: data.type,
                    value: data.value
                };
                
                if (data.type === 'discount') {
                    discount = data.discount;
                } else if (data.type === 'free_shipping') {
                    discount = shippingFee; // discount the shipping fee
                } else if (data.type === 'cashback_points') {
                    discount = 0; // points reward, cash total doesn't reduce
                }
                
                msgEl.innerText = data.message;
                msgEl.style.color = 'var(--primary-dark)';
                
                // Disable inputs on success
                codeInput.setAttribute('disabled', 'disabled');
                document.getElementById('btn-apply-voucher').setAttribute('disabled', 'disabled');
            } else {
                msgEl.innerText = data.message;
                msgEl.style.color = '#ef4444';
                discount = 0;
                voucherApplied = null;
            }
            updateSummary();
        })
        .catch(err => {
            console.error('Error validation:', err);
            msgEl.innerText = 'Terjadi kesalahan sistem.';
            msgEl.style.color = '#ef4444';
        });
    }

    // Set initial values
    document.addEventListener('DOMContentLoaded', () => {
        updateSummary();
    });
</script>

<?php include 'includes/footer.php'; ?>
