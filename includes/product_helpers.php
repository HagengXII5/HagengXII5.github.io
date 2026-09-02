<?php
// ============================================================
// includes/product_helpers.php — Product CRUD via PDO
// ============================================================

require_once __DIR__ . '/../config/db.php';

/**
 * Ambil semua produk beserta nama kategori.
 */
function getAllProducts(): array
{
    $db   = getDB();
    $stmt = $db->query(
        'SELECT p.*, c.name AS category_name, c.icon AS category_icon_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         ORDER BY c.display_order, p.name'
    );
    return $stmt->fetchAll();
}

/**
 * Ambil produk berdasarkan ID.
 */
function getProductById(string $id): ?array
{
    $db   = getDB();
    $stmt = $db->prepare(
        'SELECT p.*, c.name AS category_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.id = ? LIMIT 1'
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Ambil produk berdasarkan kategori.
 */
function getProductsByCategory(string $categoryId): array
{
    $db   = getDB();
    $stmt = $db->prepare(
        'SELECT p.*, c.name AS category_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.category_id = ?
         ORDER BY p.name'
    );
    $stmt->execute([$categoryId]);
    return $stmt->fetchAll();
}

/**
 * Cari produk berdasarkan query (nama, deskripsi, tags JSON).
 */
function searchProducts(string $query): array
{
    $db    = getDB();
    $like  = '%' . $query . '%';
    $stmt  = $db->prepare(
        'SELECT p.*, c.name AS category_name
         FROM products p
         LEFT JOIN categories c ON p.category_id = c.id
         WHERE p.name LIKE ? OR p.description LIKE ? OR JSON_SEARCH(p.tags, \'all\', ?) IS NOT NULL
         ORDER BY c.display_order, p.name'
    );
    $stmt->execute([$like, $like, $like]);
    return $stmt->fetchAll();
}

/**
 * Ambil semua kategori.
 */
function getAllCategories(): array
{
    $db   = getDB();
    $stmt = $db->query('SELECT * FROM categories ORDER BY display_order');
    return $stmt->fetchAll();
}

/**
 * Tambah produk baru.
 * $data: [id, emoji, name, description, price, category_id, in_stock, tags[]]
 */
function createProduct(array $data): array
{
    if (empty($data['name']) || empty($data['category_id']) || !isset($data['price'])) {
        return ['success' => false, 'message' => 'Nama, kategori, dan harga wajib diisi.'];
    }

    $id = $data['id'] ?? ('product-' . time() . '-' . random_int(0, 999));

    // Ambil category icon
    $db       = getDB();
    $catStmt  = $db->prepare('SELECT icon FROM categories WHERE id = ? LIMIT 1');
    $catStmt->execute([$data['category_id']]);
    $cat      = $catStmt->fetch();
    $catIcon  = $cat ? $cat['icon'] : '';

    $tags = isset($data['tags']) ? json_encode($data['tags'], JSON_UNESCAPED_UNICODE) : '[]';

    try {
        $stmt = $db->prepare(
            'INSERT INTO products (id, emoji, name, description, price, category_id, category_icon, in_stock, tags)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $id,
            $data['emoji']       ?? '',
            $data['name'],
            $data['description'] ?? '',
            (int) $data['price'],
            $data['category_id'],
            $catIcon,
            isset($data['in_stock']) ? (int)(bool)$data['in_stock'] : 1,
            $tags,
        ]);
        return ['success' => true, 'message' => 'Produk berhasil ditambahkan.', 'id' => $id];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Gagal menyimpan produk: ' . $e->getMessage()];
    }
}

/**
 * Update produk.
 */
function updateProduct(string $id, array $data): array
{
    $db = getDB();

    // Ambil category icon jika category_id berubah
    $catIcon = null;
    if (!empty($data['category_id'])) {
        $catStmt = $db->prepare('SELECT icon FROM categories WHERE id = ? LIMIT 1');
        $catStmt->execute([$data['category_id']]);
        $cat     = $catStmt->fetch();
        $catIcon = $cat ? $cat['icon'] : '';
    }

    $fields = [];
    $values = [];

    $map = [
        'emoji'       => 'emoji',
        'name'        => 'name',
        'description' => 'description',
        'price'       => 'price',
        'category_id' => 'category_id',
        'in_stock'    => 'in_stock',
    ];

    foreach ($map as $key => $col) {
        if (array_key_exists($key, $data)) {
            $fields[] = "{$col} = ?";
            $val = $data[$key];
            if ($key === 'price')    $val = (int) $val;
            if ($key === 'in_stock') $val = (int)(bool)$val;
            $values[] = $val;
        }
    }

    if ($catIcon !== null) {
        $fields[] = 'category_icon = ?';
        $values[] = $catIcon;
    }

    if (isset($data['tags'])) {
        $fields[] = 'tags = ?';
        $values[] = is_array($data['tags'])
            ? json_encode($data['tags'], JSON_UNESCAPED_UNICODE)
            : $data['tags'];
    }

    if (empty($fields)) {
        return ['success' => false, 'message' => 'Tidak ada data yang diubah.'];
    }

    $values[] = $id;
    try {
        $db->prepare('UPDATE products SET ' . implode(', ', $fields) . ' WHERE id = ?')
           ->execute($values);
        return ['success' => true, 'message' => 'Produk berhasil diperbarui.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Gagal memperbarui produk.'];
    }
}

/**
 * Toggle status stok produk.
 */
function toggleProductStock(string $id): array
{
    $db   = getDB();
    $stmt = $db->prepare('UPDATE products SET in_stock = NOT in_stock WHERE id = ?');
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) {
        return ['success' => false, 'message' => 'Produk tidak ditemukan.'];
    }
    return ['success' => true, 'message' => 'Status stok berhasil diubah.'];
}

/**
 * Hapus produk.
 */
function deleteProduct(string $id): array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan.'];
        }
        return ['success' => true, 'message' => 'Produk berhasil dihapus.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Gagal menghapus produk. Mungkin sedang digunakan di transaksi.'];
    }
}

/**
 * Normalisasi row produk untuk dikirim ke frontend (JSON).
 * Menambahkan field 'category' string (icon + nama) dan decode tags.
 */
function normalizeProduct(array $row): array
{
    $icon     = $row['category_icon'] ?? '';
    $catName  = $row['category_name'] ?? $row['category_id'] ?? '';
    $row['category']  = $icon ? "{$icon} {$catName}" : $catName;
    $row['desc']      = $row['description'] ?? '';
    $row['inStock']   = (bool)$row['in_stock'];
    $row['price']     = (int)$row['price'];
    if (is_string($row['tags'])) {
        $row['tags'] = json_decode($row['tags'], true) ?? [];
    }
    return $row;
}
