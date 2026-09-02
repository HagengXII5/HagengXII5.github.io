<?php
// ============================================================
// api/logout.php — POST /api/logout.php
// ============================================================
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

startSession();
session_unset();
session_destroy();

jsonResponse(['success' => true, 'message' => 'Logout berhasil.']);
