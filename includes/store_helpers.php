<?php
// ============================================================
// includes/store_helpers.php — Store CRUD via PDO
// ============================================================

require_once __DIR__ . '/../config/db.php';

/**
 * Ambil semua toko.
 */
function getAllStores(): array
{
    $db   = getDB();
    $stmt = $db->query('SELECT * FROM stores ORDER BY name');
    return $stmt->fetchAll();
}

/**
 * Ambil toko yang berstatus 'open'.
 */
function getOpenStores(): array
{
    $db   = getDB();
    $stmt = $db->query("SELECT * FROM stores WHERE status = 'open' ORDER BY name");
    return $stmt->fetchAll();
}

/**
 * Ambil toko berdasarkan ID.
 */
function getStoreById(string $id): ?array
{
    $db   = getDB();
    $stmt = $db->prepare('SELECT * FROM stores WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row  = $stmt->fetch();
    return $row ?: null;
}

/**
 * Tambah toko baru.
 */
function createStore(array $data): array
{
    if (empty($data['name']) || empty($data['address'])) {
        return ['success' => false, 'message' => 'Nama dan alamat toko wajib diisi.'];
    }

    $id = 'store-' . time() . '-' . random_int(0, 999);

    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'INSERT INTO stores (id, name, address, distance, hours, phone, status, lat, lng)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $id,
            $data['name'],
            $data['address'],
            $data['distance'] ?? '-',
            $data['hours']    ?? '24 Jam',
            $data['phone']    ?? '',
            ($data['status'] ?? 'open') === 'open' ? 'open' : 'closed',
            (float)($data['lat'] ?? 0),
            (float)($data['lng'] ?? 0),
        ]);
        return ['success' => true, 'message' => 'Toko berhasil ditambahkan.', 'id' => $id];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Gagal menyimpan toko.'];
    }
}

/**
 * Update toko.
 */
function updateStore(string $id, array $data): array
{
    $allowed = ['name', 'address', 'distance', 'hours', 'phone', 'status', 'lat', 'lng'];
    $fields  = [];
    $values  = [];

    foreach ($allowed as $field) {
        if (array_key_exists($field, $data)) {
            $fields[] = "{$field} = ?";
            $val = $data[$field];
            if ($field === 'status') $val = ($val === 'open') ? 'open' : 'closed';
            if (in_array($field, ['lat','lng'])) $val = (float)$val;
            $values[] = $val;
        }
    }

    if (empty($fields)) {
        return ['success' => false, 'message' => 'Tidak ada data yang diubah.'];
    }

    $values[] = $id;
    try {
        $db = getDB();
        $db->prepare('UPDATE stores SET ' . implode(', ', $fields) . ' WHERE id = ?')
           ->execute($values);
        return ['success' => true, 'message' => 'Toko berhasil diperbarui.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Gagal memperbarui toko.'];
    }
}

/**
 * Toggle status open/closed toko.
 */
function toggleStoreStatus(string $id): array
{
    $db   = getDB();
    $stmt = $db->prepare(
        "UPDATE stores SET status = IF(status = 'open', 'closed', 'open') WHERE id = ?"
    );
    $stmt->execute([$id]);
    if ($stmt->rowCount() === 0) {
        return ['success' => false, 'message' => 'Toko tidak ditemukan.'];
    }
    return ['success' => true, 'message' => 'Status toko berhasil diubah.'];
}

/**
 * Hapus toko.
 */
function deleteStore(string $id): array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare('DELETE FROM stores WHERE id = ?');
        $stmt->execute([$id]);
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Toko tidak ditemukan.'];
        }
        return ['success' => true, 'message' => 'Toko berhasil dihapus.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Gagal menghapus toko.'];
    }
}

/**
 * Hitung statistik toko (total, open, closed).
 */
function getStoreStats(): array
{
    $db   = getDB();
    $stmt = $db->query(
        "SELECT
            COUNT(*) AS total,
            SUM(status = 'open') AS open_count,
            SUM(status = 'closed') AS closed_count
         FROM stores"
    );
    $row = $stmt->fetch();
    return [
        'total'  => (int)$row['total'],
        'open'   => (int)$row['open_count'],
        'closed' => (int)$row['closed_count'],
    ];
}
