<?php
// ============================================================
// includes/cart_helpers.php — Server-side Cart CRUD via PDO
// ============================================================

require_once __DIR__ . '/../config/db.php';

/**
 * Ambil semua item keranjang milik user, join dengan data produk.
 */
function getCartItems(int $userId): array
{
    $db   = getDB();
    $stmt = $db->prepare(
        'SELECT ci.id AS cart_id, ci.qty, ci.product_id,
                p.name, p.emoji, p.price, p.in_stock,
                p.description AS desc,
                c.name AS category_name, c.icon AS category_icon
         FROM cart_items ci
         JOIN products p ON ci.product_id = p.id
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE ci.user_id = ?
         ORDER BY ci.added_at'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Hitung total quantity item di keranjang.
 */
function getCartTotalQty(int $userId): int
{
    $db   = getDB();
    $stmt = $db->prepare('SELECT COALESCE(SUM(qty), 0) FROM cart_items WHERE user_id = ?');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}

/**
 * Tambah produk ke keranjang atau increment qty jika sudah ada.
 * Gagal jika produk out of stock.
 */
function addToCart(int $userId, string $productId, int $qty = 1): array
{
    if ($qty < 1) {
        return ['success' => false, 'message' => 'Jumlah tidak valid.'];
    }

    $db = getDB();

    // Cek stok produk
    $pStmt = $db->prepare('SELECT in_stock, name FROM products WHERE id = ? LIMIT 1');
    $pStmt->execute([$productId]);
    $product = $pStmt->fetch();

    if (!$product) {
        return ['success' => false, 'message' => 'Produk tidak ditemukan.'];
    }
    if (!$product['in_stock']) {
        return ['success' => false, 'message' => "Produk \"{$product['name']}\" sedang habis."];
    }

    try {
        $stmt = $db->prepare(
            'INSERT INTO cart_items (user_id, product_id, qty)
             VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE qty = qty + VALUES(qty), updated_at = NOW()'
        );
        $stmt->execute([$userId, $productId, $qty]);
        return ['success' => true, 'message' => 'Produk ditambahkan ke keranjang.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Gagal menambahkan ke keranjang.'];
    }
}

/**
 * Update qty item di keranjang (set nilai absolute, bukan delta).
 * Jika qty <= 0, hapus item dari keranjang.
 */
function updateCartItem(int $userId, string $productId, int $qty): array
{
    $db = getDB();

    if ($qty <= 0) {
        return removeFromCart($userId, $productId);
    }

    $stmt = $db->prepare(
        'UPDATE cart_items SET qty = ?, updated_at = NOW() WHERE user_id = ? AND product_id = ?'
    );
    $stmt->execute([$qty, $userId, $productId]);

    if ($stmt->rowCount() === 0) {
        // Item belum ada, tambahkan
        return addToCart($userId, $productId, $qty);
    }
    return ['success' => true, 'message' => 'Keranjang diperbarui.'];
}

/**
 * Hapus satu item dari keranjang.
 */
function removeFromCart(int $userId, string $productId): array
{
    $db   = getDB();
    $stmt = $db->prepare('DELETE FROM cart_items WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$userId, $productId]);
    return ['success' => true, 'message' => 'Item dihapus dari keranjang.'];
}

/**
 * Kosongkan seluruh keranjang user.
 */
function clearCart(int $userId): void
{
    $db = getDB();
    $db->prepare('DELETE FROM cart_items WHERE user_id = ?')->execute([$userId]);
}

/**
 * Validasi stok semua item di keranjang.
 * Mengembalikan daftar item yang habis (jika ada).
 */
function validateCartStock(int $userId): array
{
    $db   = getDB();
    $stmt = $db->prepare(
        'SELECT ci.product_id, p.name, p.in_stock
         FROM cart_items ci
         JOIN products p ON ci.product_id = p.id
         WHERE ci.user_id = ? AND p.in_stock = 0'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Hapus item yang habis dari keranjang user.
 * Digunakan saat validasi checkout.
 */
function removeOutOfStockItems(int $userId): int
{
    $db   = getDB();
    $stmt = $db->prepare(
        'DELETE ci FROM cart_items ci
         JOIN products p ON ci.product_id = p.id
         WHERE ci.user_id = ? AND p.in_stock = 0'
    );
    $stmt->execute([$userId]);
    return $stmt->rowCount(); // jumlah item yang dihapus
}

/**
 * Merge cart dari session/localStorage ke server (saat login).
 * $localItems = [['product_id' => ..., 'qty' => ...], ...]
 */
function mergeLocalCart(int $userId, array $localItems): void
{
    foreach ($localItems as $item) {
        if (!empty($item['product_id']) && isset($item['qty']) && (int)$item['qty'] > 0) {
            addToCart($userId, $item['product_id'], (int)$item['qty']);
        }
    }
}
