<?php
// ============================================================
// api/products.php — REST endpoint untuk produk
// GET    /api/products.php              → semua produk (public)
// GET    /api/products.php?id=X         → satu produk (public)
// GET    /api/products.php?search=X     → cari produk (public)
// GET    /api/products.php?category=X   → filter kategori (public)
// GET    /api/products.php?categories=1 → daftar kategori (public)
// POST   /api/products.php              → tambah produk (admin)
// PUT    /api/products.php?id=X         → edit produk (admin)
// PATCH  /api/products.php?id=X&action=toggle_stock → toggle stok (admin)
// DELETE /api/products.php?id=X         → hapus produk (admin)
// ============================================================
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/product_helpers.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = sanitize($_GET['id'] ?? '');

switch ($method) {

    // ── GET ──────────────────────────────────────────────────
    case 'GET':
        // Daftar kategori
        if (isset($_GET['categories'])) {
            $cats = getAllCategories();
            jsonResponse(['success' => true, 'data' => $cats]);
        }

        // Satu produk
        if (!empty($id)) {
            $product = getProductById($id);
            if (!$product) jsonError('Produk tidak ditemukan.', 404);
            jsonResponse(['success' => true, 'data' => normalizeProduct($product)]);
        }

        // Cari produk
        if (isset($_GET['search']) && $_GET['search'] !== '') {
            $results = searchProducts(sanitize($_GET['search']));
            jsonResponse(['success' => true, 'data' => array_map('normalizeProduct', $results)]);
        }

        // Filter kategori
        if (!empty($_GET['category'])) {
            $results = getProductsByCategory(sanitize($_GET['category']));
            jsonResponse(['success' => true, 'data' => array_map('normalizeProduct', $results)]);
        }

        // Semua produk
        $products = getAllProducts();
        jsonResponse(['success' => true, 'data' => array_map('normalizeProduct', $products)]);
        break;

    // ── POST (tambah) ─────────────────────────────────────────
    case 'POST':
        requireAdmin();
        $body   = getJsonBody();
        $result = createProduct($body);
        jsonResponse($result, $result['success'] ? 201 : 422);
        break;

    // ── PUT (edit) ────────────────────────────────────────────
    case 'PUT':
        requireAdmin();
        if (empty($id)) jsonError('ID produk diperlukan.');
        $body   = getJsonBody();
        $result = updateProduct($id, $body);
        jsonResponse($result, $result['success'] ? 200 : 422);
        break;

    // ── PATCH (toggle stok) ───────────────────────────────────
    case 'PATCH':
        requireAdmin();
        if (empty($id)) jsonError('ID produk diperlukan.');
        $action = $_GET['action'] ?? '';
        if ($action === 'toggle_stock') {
            $result = toggleProductStock($id);
            jsonResponse($result, $result['success'] ? 200 : 404);
        }
        jsonError('Action tidak dikenal.');
        break;

    // ── DELETE (hapus) ────────────────────────────────────────
    case 'DELETE':
        requireAdmin();
        if (empty($id)) jsonError('ID produk diperlukan.');
        $result = deleteProduct($id);
        jsonResponse($result, $result['success'] ? 200 : 404);
        break;

    default:
        jsonError('Method not allowed.', 405);
}
