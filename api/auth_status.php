<?php
// ============================================================
// api/auth_status.php — GET → status sesi user saat ini
// Dipakai oleh JS frontend untuk mengecek login status
// ============================================================
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/cart_helpers.php';

$user = getCurrentUser();

if ($user) {
    $cartQty = getCartTotalQty((int)$user['id']);
    jsonResponse([
        'logged_in'  => true,
        'user'       => $user,
        'cart_qty'   => $cartQty,
    ]);
} else {
    jsonResponse([
        'logged_in' => false,
        'user'      => null,
        'cart_qty'  => 0,
    ]);
}
