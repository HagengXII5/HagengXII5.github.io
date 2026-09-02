<?php
// ============================================================
// api/cart.php — REST endpoint untuk keranjang belanja
// GET    /api/cart.php                         → isi keranjang user login
// POST   /api/cart.php                         → tambah item ke keranjang
// PUT    /api/cart.php                         → update qty item
// DELETE /api/cart.php?product_id=X            → hapus satu item
// DELETE /api/cart.php?clear=1                 → kosongkan keranjang
// POST   /api/cart.php (action=merge)          → merge local cart saat login
// ============================================================
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/cart_helpers.php';

requireLogin();
$user   = getCurrentUser();
$userId = (int)$user['id'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // ── GET (ambil keranjang) ─────────────────────────────────
    case 'GET':
        $items    = getCartItems($userId);
        $totalQty = getCartTotalQty($userId);
        $subtotal = array_reduce($items, fn($sum, $i) => $sum + ($i['price'] * $i['qty']), 0);
        jsonResponse([
            'success'   => true,
            'data'      => $items,
            'total_qty' => $totalQty,
            'subtotal'  => $subtotal,
        ]);
        break;

    // ── POST (tambah item / merge) ────────────────────────────
    case 'POST':
        $body   = getJsonBody();
        $action = $body['action'] ?? 'add';

        if ($action === 'merge') {
            // Merge cart lokal (dari localStorage) saat pertama kali login
            $localItems = $body['items'] ?? [];
            if (!is_array($localItems) || empty($localItems)) {
                jsonResponse(['success' => true, 'message' => 'Tidak ada item untuk di-merge.']);
            }
            mergeLocalCart($userId, $localItems);
            $items = getCartItems($userId);
            jsonResponse(['success' => true, 'message' => 'Cart berhasil di-merge.', 'data' => $items]);
        }

        // Tambah satu item
        $productId = sanitize($body['product_id'] ?? '');
        $qty       = (int)($body['qty'] ?? 1);
        if (empty($productId)) jsonError('product_id diperlukan.');
        $result = addToCart($userId, $productId, $qty);
        jsonResponse($result, $result['success'] ? 200 : 422);
        break;

    // ── PUT (update qty) ──────────────────────────────────────
    case 'PUT':
        $body      = getJsonBody();
        $productId = sanitize($body['product_id'] ?? '');
        $qty       = (int)($body['qty'] ?? 0);
        if (empty($productId)) jsonError('product_id diperlukan.');
        $result = updateCartItem($userId, $productId, $qty);
        jsonResponse($result);
        break;

    // ── DELETE (hapus item / clear) ───────────────────────────
    case 'DELETE':
        if (isset($_GET['clear'])) {
            clearCart($userId);
            jsonResponse(['success' => true, 'message' => 'Keranjang dikosongkan.']);
        }
        $productId = sanitize($_GET['product_id'] ?? '');
        if (empty($productId)) jsonError('product_id diperlukan.');
        $result = removeFromCart($userId, $productId);
        jsonResponse($result);
        break;

    default:
        jsonError('Method not allowed.', 405);
}
