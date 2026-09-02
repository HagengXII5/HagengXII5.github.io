<?php
// ============================================================
// api/login.php — POST /api/login.php
// ============================================================
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed.', 405);
}

$body     = getJsonBody();
$username = sanitize($body['username'] ?? '');
$password = $body['password'] ?? '';

if (empty($username) || empty($password)) {
    jsonError('Username dan password harus diisi.');
}

$result = loginUser($username, $password);

if ($result['success']) {
    jsonResponse($result);
} else {
    jsonResponse($result, 401);
}
