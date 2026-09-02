<?php
// ============================================================
// api/transactions.php — REST endpoint untuk transaksi
// GET    /api/transactions.php           → transaksi user login / semua (admin)
// GET    /api/transactions.php?id=X      → satu transaksi by order_no
// POST   /api/transactions.php           → buat transaksi baru (butuh login)
// PATCH  /api/transactions.php?id=X&action=update_status → ubah status (admin)
// DELETE /api/transactions.php?id=X      → hapus transaksi (admin)
// ============================================================
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/transaction_helpers.php';
require_once __DIR__ . '/../includes/cart_helpers.php';

$method = $_SERVER['REQUEST_METHOD'];
$orderNo = sanitize($_GET['id'] ?? '');

switch ($method) {

    // ── GET ──────────────────────────────────────────────────
    case 'GET':
        requireLogin();
        $user = getCurrentUser();

        if (!empty($orderNo)) {
            $trx = getTransactionByOrderNo($orderNo);
            if (!$trx) jsonError('Transaksi tidak ditemukan.', 404);
            // Pastikan user hanya bisa lihat transaksi miliknya (kecuali admin)
            if ($user['role'] !== 'admin' && (int)$trx['user_id'] !== (int)$user['id']) {
                jsonError('Akses ditolak.', 403);
            }
            $items = getTransactionItems((int)$trx['id']);
            jsonResponse(['success' => true, 'data' => $trx, 'items' => $items]);
        }

        $list = $user['role'] === 'admin'
            ? getAllTransactions()
            : getUserTransactions((int)$user['id']);

        jsonResponse(['success' => true, 'data' => $list]);
        break;

    // ── POST (buat pesanan) ───────────────────────────────────
    case 'POST':
        requireLogin();
        $user = getCurrentUser();
        $body = getJsonBody();

        // Ambil items dari cart server-side jika tidak dikirim di body
        $cartItems = $body['items'] ?? null;
        if (empty($cartItems)) {
            $dbCartItems = getCartItems((int)$user['id']);
            if (empty($dbCartItems)) {
                jsonError('Keranjang kosong.');
            }
            $cartItems = array_map(fn($ci) => [
                'product_id'    => $ci['product_id'],
                'product_name'  => $ci['name'],
                'product_emoji' => $ci['emoji'],
                'price'         => (int)$ci['price'],
                'qty'           => (int)$ci['qty'],
            ], $dbCartItems);
        }

        $data = [
            'user_id'          => (int)$user['id'],
            'user_name'        => $user['full_name'] ?: $user['username'],
            'store_name'       => sanitize($body['store_name'] ?? ''),
            'items'            => $cartItems,
            'delivery_method'  => sanitize($body['delivery_method'] ?? 'antar'),
            'delivery_address' => sanitize($body['delivery_address'] ?? ''),
            'payment_method'   => sanitize($body['payment_method'] ?? 'cod'),
            'order_notes'      => sanitize($body['order_notes'] ?? ''),
        ];

        $result = createTransaction($data);

        if ($result['success']) {
            // Kosongkan keranjang setelah order berhasil
            clearCart((int)$user['id']);
        }

        jsonResponse($result, $result['success'] ? 201 : 422);
        break;

    // ── PATCH (update status) ─────────────────────────────────
    case 'PATCH':
        requireAdmin();
        if (empty($orderNo)) jsonError('Order number diperlukan.');
        $body      = getJsonBody();
        $action    = $_GET['action'] ?? '';
        $newStatus = sanitize($body['status'] ?? '');

        if ($action === 'update_status' && !empty($newStatus)) {
            $result = updateTransactionStatus($orderNo, $newStatus);
            jsonResponse($result, $result['success'] ? 200 : 422);
        }
        jsonError('Action atau status tidak valid.');
        break;

    // ── DELETE ────────────────────────────────────────────────
    case 'DELETE':
        requireAdmin();
        if (empty($orderNo)) jsonError('Order number diperlukan.');
        $result = deleteTransaction($orderNo);
        jsonResponse($result, $result['success'] ? 200 : 404);
        break;

    default:
        jsonError('Method not allowed.', 405);
}
