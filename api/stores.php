<?php
// ============================================================
// api/stores.php — REST endpoint untuk toko
// GET    /api/stores.php           → semua toko (public)
// GET    /api/stores.php?open=1    → toko yang buka saja (public)
// GET    /api/stores.php?id=X      → satu toko (public)
// POST   /api/stores.php           → tambah toko (admin)
// PUT    /api/stores.php?id=X      → edit toko (admin)
// PATCH  /api/stores.php?id=X&action=toggle_status → toggle open/closed (admin)
// DELETE /api/stores.php?id=X      → hapus toko (admin)
// ============================================================
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/store_helpers.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = sanitize($_GET['id'] ?? '');

switch ($method) {

    case 'GET':
        if (!empty($id)) {
            $store = getStoreById($id);
            if (!$store) jsonError('Toko tidak ditemukan.', 404);
            jsonResponse(['success' => true, 'data' => $store]);
        }
        if (isset($_GET['open'])) {
            jsonResponse(['success' => true, 'data' => getOpenStores()]);
        }
        jsonResponse(['success' => true, 'data' => getAllStores()]);
        break;

    case 'POST':
        requireAdmin();
        $body   = getJsonBody();
        $result = createStore($body);
        jsonResponse($result, $result['success'] ? 201 : 422);
        break;

    case 'PUT':
        requireAdmin();
        if (empty($id)) jsonError('ID toko diperlukan.');
        $body   = getJsonBody();
        $result = updateStore($id, $body);
        jsonResponse($result, $result['success'] ? 200 : 422);
        break;

    case 'PATCH':
        requireAdmin();
        if (empty($id)) jsonError('ID toko diperlukan.');
        $action = $_GET['action'] ?? '';
        if ($action === 'toggle_status') {
            $result = toggleStoreStatus($id);
            jsonResponse($result, $result['success'] ? 200 : 404);
        }
        jsonError('Action tidak dikenal.');
        break;

    case 'DELETE':
        requireAdmin();
        if (empty($id)) jsonError('ID toko diperlukan.');
        $result = deleteStore($id);
        jsonResponse($result, $result['success'] ? 200 : 404);
        break;

    default:
        jsonError('Method not allowed.', 405);
}
