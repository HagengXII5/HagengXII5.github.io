<?php
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/helpers.php';

if (isLoggedIn()) {
    header('Location: /produk');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar — Klik Madura</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{--red:#D4262C;--green:#0F6B3A;--cream:#FFFBF2;--ink:#1E1B16;--line:#EDE6D6;}
  *{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--cream);color:var(--ink);line-height:1.55;min-height:100vh;display:flex;flex-direction:column;}
  h1,h2,h3{font-family:'Baloo 2',sans-serif;}
  a{text-decoration:none;color:inherit;}
  header{display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--line);}
  .logo{display:flex;align-items:center;gap:10px;font-weight:800;font-size:20px;}
  .logo img{height:50px;width:auto;display:block;}
  .register-container{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 24px;}
  .register-box{background:#fff;border:1px solid var(--line);border-radius:20px;padding:40px;max-width:480px;width:100%;box-shadow:0 4px 6px rgba(0,0,0,0.05);}
  .register-box h1{font-size:28px;margin-bottom:8px;text-align:center;}
  .register-box .subtitle{text-align:center;color:#666;font-size:14px;margin-bottom:32px;}
  .form-group{margin-bottom:18px;}
  .form-group label{display:block;font-weight:600;font-size:14px;margin-bottom:6px;}
  .form-group input,.form-group textarea{width:100%;padding:12px 16px;border:1px solid var(--line);border-radius:10px;font-family:inherit;font-size:14px;background:#fff;}
  .form-group input:focus,.form-group textarea:focus{outline:none;border-color:var(--red);}
  .form-group small{display:block;margin-top:4px;font-size:12px;color:#666;}
  .btn-register{width:100%;padding:14px;background:var(--red);color:#fff;border:none;border-radius:10px;font-weight:700;font-size:15px;cursor:pointer;font-family:inherit;margin-top:8px;}
  .btn-register:hover{background:#b81f24;}
  .btn-register:disabled{background:#ccc;cursor:not-allowed;}
  .divider{text-align:center;margin:20px 0;color:#999;font-size:13px;}
  .login-link{text-align:center;font-size:14px;}
  .login-link a{color:var(--red);font-weight:600;}
  .alert{padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:14px;}
  .alert-error{background:#ffe6e6;color:#d32f2f;border:1px solid #ffcccc;}
  .alert-success{background:#e6f4ea;color:#0f6b3a;border:1px solid #b7e4c7;}
  footer{text-align:center;padding:20px 24px;font-size:13px;color:#999;border-top:1px solid var(--line);}
</style>
</head>
<body>

<header>
  <a href="/" class="logo">
    <img src="../img/logo_klik_madura_v3_biru.svg" alt="Klik Madura">Klik Madura
  </a>
</header>

<div class="register-container">
  <div class="register-box">
    <h1>Daftar Akun</h1>
    <p class="subtitle">Buat akun dengan username dan password</p>

    <div id="alertBox"></div>

    <form id="registerForm">
      <div class="form-group">
        <label for="username">Username *</label>
        <input type="text" id="username" name="username" required autocomplete="username">
        <small>Gunakan huruf kecil tanpa spasi</small>
      </div>
      <div class="form-group">
        <label for="password">Password *</label>
        <input type="password" id="password" name="password" required autocomplete="new-password">
        <small>Minimal 6 karakter</small>
      </div>
      <div class="form-group">
        <label for="confirmPassword">Konfirmasi Password *</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required autocomplete="new-password">
      </div>
      <button type="submit" class="btn-register" id="btnRegister">Daftar</button>
    </form>

    <div class="divider">atau</div>
    <div class="login-link">Sudah punya akun? <a href="/login">Masuk sekarang</a></div>
  </div>
</div>

<footer>© 2026 Klik Madura</footer>

<script>
(function () {
  const form      = document.getElementById('registerForm');
  const alertBox  = document.getElementById('alertBox');
  const btnReg    = document.getElementById('btnRegister');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    alertBox.innerHTML = '';

    const username        = document.getElementById('username').value.trim().toLowerCase();
    const password        = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    // Client-side validasi
    if (!username || !password) return showAlert('error', 'Username dan password harus diisi.');
    if (password.length < 6)    return showAlert('error', 'Password minimal 6 karakter.');
    if (password !== confirmPassword) return showAlert('error', 'Password dan konfirmasi tidak sama.');
    if (username.includes(' ')) return showAlert('error', 'Username tidak boleh mengandung spasi.');

    btnReg.disabled     = true;
    btnReg.textContent  = 'Mendaftar...';

    try {
      const res  = await fetch('/api/register.php', {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify({
          username,
          password,
          confirm_password: confirmPassword,
          full_name: username,
        }),
      });
      const data = await res.json();

      if (data.success) {
        showAlert('success', data.message + ' Mengalihkan ke halaman login...');
        setTimeout(() => window.location.href = '/login', 1500);
      } else {
        showAlert('error', data.message || 'Registrasi gagal.');
        btnReg.disabled    = false;
        btnReg.textContent = 'Daftar';
      }
    } catch (err) {
      showAlert('error', 'Terjadi kesalahan. Coba lagi.');
      btnReg.disabled    = false;
      btnReg.textContent = 'Daftar';
    }
  });

  function showAlert(type, msg) {
    alertBox.innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
  }
})();
</script>
</body>
</html>
