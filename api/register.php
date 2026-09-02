<?php
// ============================================================
// api/register.php — POST /api/register.php
// ============================================================
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth_helpers.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed.', 405);
}

$body            = getJsonBody();
$username        = sanitize($body['username'] ?? '');
$password        = $body['password'] ?? '';
$confirmPassword = $body['confirm_password'] ?? '';
$fullName        = sanitize($body['full_name'] ?? $username);
$email           = sanitize($body['email'] ?? '');
$phone           = sanitize($body['phone'] ?? '');
$address         = sanitize($body['address'] ?? '');

// Validasi sisi server
if (empty($username) || empty($password)) {
    jsonError('Username dan password harus diisi.');
}
if (strlen($password) < 6) {
    jsonError('Password minimal 6 karakter.');
}
if (!empty($confirmPassword) && $password !== $confirmPassword) {
    jsonError('Password dan konfirmasi password tidak sama.');
}
if (str_contains($username, ' ')) {
    jsonError('Username tidak boleh mengandung spasi.');
}

$result = registerUser([
    'username'  => $username,
    'password'  => $password,
    'full_name' => $fullName,
    'email'     => $email,
    'phone'     => $phone,
    'address'   => $address,
]);

$httpCode = $result['success'] ? 201 : 422;
jsonResponse($result, $httpCode);
