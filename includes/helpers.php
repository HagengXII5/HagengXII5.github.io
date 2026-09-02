<?php
// ============================================================
// includes/helpers.php — Utility Functions
// ============================================================

/**
 * Format angka ke format Rupiah.
 * Contoh: 13500 → "Rp13.500"
 */
function formatMoney(int $amount): string
{
    return 'Rp' . number_format($amount, 0, ',', '.');
}

/**
 * Format string ISO 8601 / datetime MySQL ke format Indonesia.
 * Contoh: "2026-09-02 14:30:00" → "2 Sep 2026, 14:30"
 */
function formatDate(string $dateStr): string
{
    $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    $ts = strtotime($dateStr);
    if ($ts === false) return $dateStr;
    $day   = (int) date('j', $ts);
    $mon   = (int) date('n', $ts) - 1;
    $year  = date('Y', $ts);
    $time  = date('H:i', $ts);
    return "{$day} {$months[$mon]} {$year}, {$time}";
}

/**
 * Generate nomor order unik dengan format #KM-YYYYMMDD-HHMMSS-XXXX
 * Suffix 4 digit random agar aman jika ada 2 order di detik yang sama.
 */
function generateOrderNo(): string
{
    $date   = date('Ymd');
    $time   = date('His');
    $suffix = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    return "#KM-{$date}-{$time}-{$suffix}";
}

/**
 * Kirim response JSON lalu exit.
 *
 * @param mixed $data    Data yang akan di-encode ke JSON
 * @param int   $status  HTTP status code (default 200)
 */
function jsonResponse($data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Kirim response error JSON.
 */
function jsonError(string $message, int $status = 400): void
{
    jsonResponse(['success' => false, 'message' => $message], $status);
}

/**
 * Ambil body JSON dari request (untuk API endpoint).
 * Mengembalikan array asosiatif atau array kosong jika invalid.
 */
function getJsonBody(): array
{
    $raw = file_get_contents('php://input');
    if (empty($raw)) return [];
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

/**
 * Sanitize string input — strip tags dan trim whitespace.
 */
function sanitize(string $input): string
{
    return trim(strip_tags($input));
}

/**
 * Validasi apakah string adalah integer positif (untuk ID).
 */
function isPositiveInt($val): bool
{
    return filter_var($val, FILTER_VALIDATE_INT) !== false && (int)$val > 0;
}

/**
 * Helper: ambil nilai dari array dengan fallback.
 */
function arrayGet(array $arr, string $key, $default = null)
{
    return array_key_exists($key, $arr) ? $arr[$key] : $default;
}
