<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

// Read raw JSON input or post variables
$input = json_decode(file_get_contents('php://input'), true);
$code = isset($input['code']) ? trim($input['code']) : '';
$total = isset($input['total']) ? floatval($input['total']) : 0;

if (empty($code)) {
    echo json_encode(['success' => false, 'message' => 'Kode voucher tidak boleh kosong.']);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM vouchers WHERE code = :code AND active = 1 LIMIT 1");
    $stmt->execute([':code' => $code]);
    $voucher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$voucher) {
        echo json_encode(['success' => false, 'message' => 'Kode voucher tidak ditemukan atau tidak aktif.']);
        exit;
    }

    if ($total < $voucher['min_purchase']) {
        echo json_encode([
            'success' => false,
            'message' => 'Minimal belanja untuk menggunakan voucher ini adalah Rp ' . number_format($voucher['min_purchase'], 0, ',', '.') . '.'
        ]);
        exit;
    }

    // Voucher is valid! Calculate discount/cashback
    $discount = 0;
    $cashback_points = 0;
    $free_shipping = false;

    if ($voucher['type'] == 'discount') {
        $discount = floatval($voucher['value']);
    } else if ($voucher['type'] == 'free_shipping') {
        $free_shipping = true;
        $discount = floatval($voucher['value']); // value represents shipping discount (flat Rp 10.000)
    } else if ($voucher['type'] == 'cashback_points') {
        $cashback_points = intval($voucher['value']);
    }

    echo json_encode([
        'success' => true,
        'code' => $voucher['code'],
        'type' => $voucher['type'],
        'value' => floatval($voucher['value']),
        'discount' => $discount,
        'cashback_points' => $cashback_points,
        'free_shipping' => $free_shipping,
        'message' => 'Voucher ' . htmlspecialchars($voucher['code']) . ' berhasil digunakan!'
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Kesalahan database: ' . $e->getMessage()]);
}
?>
