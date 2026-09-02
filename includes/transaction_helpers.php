<?php
// ============================================================
// includes/transaction_helpers.php — Transaction CRUD via PDO
// ============================================================

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/helpers.php';

/**
 * Ambil semua transaksi (admin).
 */
function getAllTransactions(): array
{
    $db   = getDB();
    $stmt = $db->query(
        'SELECT t.*, u.username
         FROM transactions t
         LEFT JOIN users u ON t.user_id = u.id
         ORDER BY t.created_at DESC'
    );
    return $stmt->fetchAll();
}

/**
 * Ambil transaksi milik satu user.
 */
function getUserTransactions(int $userId): array
{
    $db   = getDB();
    $stmt = $db->prepare(
        'SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Ambil transaksi berdasarkan order_no.
 */
function getTransactionByOrderNo(string $orderNo): ?array
{
    $db   = getDB();
    $stmt = $db->prepare('SELECT * FROM transactions WHERE order_no = ? LIMIT 1');
    $stmt->execute([$orderNo]);
    $row  = $stmt->fetch();
    return $row ?: null;
}

/**
 * Ambil transaksi berdasarkan ID internal.
 */
function getTransactionById(int $id): ?array
{
    $db   = getDB();
    $stmt = $db->prepare('SELECT * FROM transactions WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row  = $stmt->fetch();
    return $row ?: null;
}

/**
 * Ambil item-item dari satu transaksi.
 */
function getTransactionItems(int $transactionId): array
{
    $db   = getDB();
    $stmt = $db->prepare(
        'SELECT ti.*, p.emoji AS product_emoji_current
         FROM transaction_items ti
         LEFT JOIN products p ON ti.product_id = p.id
         WHERE ti.transaction_id = ?'
    );
    $stmt->execute([$transactionId]);
    return $stmt->fetchAll();
}

/**
 * Buat transaksi baru beserta line item-nya.
 *
 * $data = [
 *   'user_id'          => int|null,
 *   'user_name'        => string,
 *   'store_name'       => string,
 *   'items'            => [['product_id', 'product_name', 'product_emoji', 'price', 'qty'], ...],
 *   'delivery_method'  => 'antar'|'ambil',
 *   'delivery_address' => string,
 *   'payment_method'   => string,
 *   'order_notes'      => string,
 * ]
 */
function createTransaction(array $data): array
{
    if (empty($data['items'])) {
        return ['success' => false, 'message' => 'Keranjang kosong.'];
    }

    $db = getDB();

    // Hitung subtotal dari items yang valid
    $subtotal = 0;
    foreach ($data['items'] as $item) {
        $subtotal += (int)$item['price'] * (int)$item['qty'];
    }

    $deliveryMethod = ($data['delivery_method'] ?? 'antar') === 'ambil' ? 'ambil' : 'antar';
    $deliveryFee    = $deliveryMethod === 'antar' ? 5000 : 0;
    $serviceFee     = 1000;
    $total          = $subtotal + $deliveryFee + $serviceFee;

    $orderNo = generateOrderNo();

    // Summary string: "Indomie x2, Aqua x1"
    $summaryParts = array_map(
        fn($i) => $i['product_name'] . ' x' . (int)$i['qty'],
        $data['items']
    );
    $itemsSummary = implode(', ', $summaryParts);

    try {
        $db->beginTransaction();

        $stmt = $db->prepare(
            'INSERT INTO transactions
               (order_no, user_id, user_name, store_name, total, subtotal,
                delivery_fee, service_fee, status, delivery_method,
                delivery_address, payment_method, order_notes, items_summary)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, \'proses\', ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $orderNo,
            $data['user_id'] ?? null,
            $data['user_name'] ?? 'Guest',
            $data['store_name'] ?? '',
            $total,
            $subtotal,
            $deliveryFee,
            $serviceFee,
            $deliveryMethod,
            $data['delivery_address'] ?? '',
            $data['payment_method'] ?? 'cod',
            $data['order_notes'] ?? '',
            $itemsSummary,
        ]);

        $transactionId = (int) $db->lastInsertId();

        // Insert transaction items
        $itemStmt = $db->prepare(
            'INSERT INTO transaction_items
               (transaction_id, product_id, product_name, product_emoji, price, qty, line_total)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        foreach ($data['items'] as $item) {
            $qty       = (int)$item['qty'];
            $price     = (int)$item['price'];
            $lineTotal = $qty * $price;
            $itemStmt->execute([
                $transactionId,
                $item['product_id'] ?? null,
                $item['product_name'],
                $item['product_emoji'] ?? '',
                $price,
                $qty,
                $lineTotal,
            ]);
        }

        $db->commit();

        return [
            'success'       => true,
            'message'       => 'Pesanan berhasil dibuat!',
            'order_no'      => $orderNo,
            'transaction_id'=> $transactionId,
            'total'         => $total,
        ];

    } catch (PDOException $e) {
        $db->rollBack();
        return ['success' => false, 'message' => 'Gagal membuat pesanan: ' . $e->getMessage()];
    }
}

/**
 * Update status transaksi (proses → selesai / batal).
 * Hanya admin yang bisa mengubah status.
 */
function updateTransactionStatus(string $orderNo, string $newStatus): array
{
    $allowed = ['proses', 'selesai', 'batal'];
    if (!in_array($newStatus, $allowed)) {
        return ['success' => false, 'message' => 'Status tidak valid.'];
    }

    $db   = getDB();
    $stmt = $db->prepare('UPDATE transactions SET status = ? WHERE order_no = ?');
    $stmt->execute([$newStatus, $orderNo]);

    if ($stmt->rowCount() === 0) {
        return ['success' => false, 'message' => 'Transaksi tidak ditemukan.'];
    }
    return ['success' => true, 'message' => 'Status transaksi berhasil diperbarui.'];
}

/**
 * Hapus transaksi beserta item-nya (cascade).
 */
function deleteTransaction(string $orderNo): array
{
    try {
        $db   = getDB();
        $stmt = $db->prepare('DELETE FROM transactions WHERE order_no = ?');
        $stmt->execute([$orderNo]);
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Transaksi tidak ditemukan.'];
        }
        return ['success' => true, 'message' => 'Transaksi berhasil dihapus.'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Gagal menghapus transaksi.'];
    }
}

/**
 * Hitung statistik transaksi + pendapatan.
 */
function getTransactionStats(): array
{
    $db   = getDB();
    $stmt = $db->query(
        "SELECT
            COUNT(*) AS total,
            SUM(status = 'selesai') AS selesai,
            SUM(status = 'proses') AS proses,
            SUM(status = 'batal') AS batal,
            COALESCE(SUM(CASE WHEN status = 'selesai' THEN total ELSE 0 END), 0) AS revenue
         FROM transactions"
    );
    $row = $stmt->fetch();
    return [
        'total'   => (int)$row['total'],
        'selesai' => (int)$row['selesai'],
        'proses'  => (int)$row['proses'],
        'batal'   => (int)$row['batal'],
        'revenue' => (int)$row['revenue'],
    ];
}
