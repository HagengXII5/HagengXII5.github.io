<?php
// ============================================================
// includes/auth_helpers.php — Session-based Authentication
// ============================================================

require_once __DIR__ . '/../config/db.php';

/**
 * Mulai session jika belum aktif.
 */
function startSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Cek apakah user sedang login.
 */
function isLoggedIn(): bool
{
    startSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Cek apakah user yang login adalah admin.
 */
function isAdmin(): bool
{
    startSession();
    return isLoggedIn() && ($_SESSION['user_role'] ?? '') === 'admin';
}

/**
 * Ambil data user yang sedang login (tanpa password_hash).
 * Mengembalikan array atau null jika tidak login.
 */
function getCurrentUser(): ?array
{
    startSession();
    if (!isLoggedIn()) return null;
    return [
        'id'        => $_SESSION['user_id'],
        'username'  => $_SESSION['user_username'] ?? '',
        'full_name' => $_SESSION['user_full_name'] ?? '',
        'email'     => $_SESSION['user_email'] ?? '',
        'role'      => $_SESSION['user_role'] ?? 'customer',
    ];
}

/**
 * Set session setelah login berhasil.
 */
function setCurrentUser(array $user): void
{
    startSession();
    $_SESSION['user_id']        = $user['id'];
    $_SESSION['user_username']  = $user['username'];
    $_SESSION['user_full_name'] = $user['full_name'];
    $_SESSION['user_email']     = $user['email'];
    $_SESSION['user_role']      = $user['role'];
}

/**
 * Login: verifikasi username + password, update last_login.
 * Mengembalikan ['success'=>bool, 'message'=>string, 'user'=>array|null]
 */
function loginUser(string $username, string $password): array
{
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Username dan password harus diisi.'];
    }

    try {
        $db   = getDB();
        $stmt = $db->prepare(
            'SELECT id, username, password_hash, full_name, email, phone, address, role, is_active
             FROM users WHERE username = ? LIMIT 1'
        );
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !$user['is_active']) {
            return ['success' => false, 'message' => 'Username atau password salah.'];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Username atau password salah.'];
        }

        // Update last_login
        $db->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([$user['id']]);

        setCurrentUser($user);

        unset($user['password_hash']);
        return ['success' => true, 'message' => 'Login berhasil!', 'user' => $user];

    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Terjadi kesalahan server.'];
    }
}

/**
 * Logout: hapus session dan redirect ke halaman utama.
 */
function logoutUser(): void
{
    startSession();
    session_unset();
    session_destroy();
    header('Location: /');
    exit;
}

/**
 * Registrasi user baru (role selalu 'customer').
 */
function registerUser(array $data): array
{
    $username = trim(strtolower($data['username'] ?? ''));
    $password = $data['password'] ?? '';
    $fullName = trim($data['full_name'] ?? $username);
    $email    = trim(strtolower($data['email'] ?? ''));
    $phone    = trim($data['phone'] ?? '');
    $address  = trim($data['address'] ?? '');

    // Validasi
    if (empty($username) || empty($password)) {
        return ['success' => false, 'message' => 'Username dan password harus diisi.'];
    }
    if (strlen($password) < 6) {
        return ['success' => false, 'message' => 'Password minimal 6 karakter.'];
    }
    if (str_contains($username, ' ')) {
        return ['success' => false, 'message' => 'Username tidak boleh mengandung spasi.'];
    }
    if (!preg_match('/^[a-z0-9_]+$/', $username)) {
        return ['success' => false, 'message' => 'Username hanya boleh huruf kecil, angka, dan underscore.'];
    }

    try {
        $db = getDB();

        // Cek username unik
        $stmt = $db->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            return ['success' => false, 'message' => 'Username sudah digunakan.'];
        }

        // Cek email unik (jika diisi)
        if (!empty($email)) {
            $stmt = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email sudah terdaftar.'];
            }
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $db->prepare(
            'INSERT INTO users (username, password_hash, full_name, email, phone, address, role, is_active)
             VALUES (?, ?, ?, ?, ?, ?, \'customer\', 1)'
        );
        $stmt->execute([$username, $hash, $fullName, $email, $phone, $address]);

        $newUser = [
            'id'        => (int) $db->lastInsertId(),
            'username'  => $username,
            'full_name' => $fullName,
            'email'     => $email,
            'role'      => 'customer',
        ];

        return ['success' => true, 'message' => 'Registrasi berhasil!', 'user' => $newUser];

    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Terjadi kesalahan server.'];
    }
}

/**
 * Update profil user.
 */
function updateUserProfile(int $userId, array $updates): array
{
    $allowed = ['full_name', 'email', 'phone', 'address'];
    $fields  = [];
    $values  = [];

    foreach ($allowed as $field) {
        if (array_key_exists($field, $updates)) {
            $fields[] = "{$field} = ?";
            $values[] = trim($updates[$field]);
        }
    }

    if (empty($fields)) {
        return ['success' => false, 'message' => 'Tidak ada data yang diubah.'];
    }

    try {
        $db = getDB();
        $values[] = $userId;
        $db->prepare('UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?')
           ->execute($values);

        // Refresh session jika user yang login
        $current = getCurrentUser();
        if ($current && $current['id'] === $userId) {
            $stmt = $db->prepare('SELECT id, username, full_name, email, role FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            if ($user) setCurrentUser($user);
        }

        return ['success' => true, 'message' => 'Profil berhasil diperbarui!'];

    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Terjadi kesalahan server.'];
    }
}

/**
 * Redirect ke login jika belum login.
 * Gunakan di halaman yang butuh auth.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '/');
        header("Location: /login?redirect={$redirect}");
        exit;
    }
}

/**
 * Redirect ke halaman utama jika bukan admin.
 * Gunakan di halaman admin.
 */
function requireAdmin(): void
{
    startSession();
    if (!isAdmin()) {
        header('Location: /');
        exit;
    }
}
