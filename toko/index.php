<?php
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/cart_helpers.php';
require_once __DIR__ . '/../includes/helpers.php';

$user    = getCurrentUser();
$cartQty = $user ? getCartTotalQty((int)$user['id']) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Toko — Klik Madura</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{--red:#D4262C;--green:#0F6B3A;--cream:#FFFBF2;--ink:#1E1B16;--line:#EDE6D6;}
  *{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--cream);color:var(--ink);line-height:1.55;}
  h1,h2,h3{font-family:'Baloo 2',sans-serif;}
  a{text-decoration:none;color:inherit;}
  .wrap{max-width:960px;margin:0 auto;padding:0 24px;}
  header{display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--line);}
  .logo{display:flex;align-items:center;gap:10px;font-weight:800;font-size:20px;}
  .logo img{height:50px;width:auto;display:block;}
  nav{display:flex;align-items:center;gap:28px;}
  nav a{font-size:15px;font-weight:600;color:var(--ink);opacity:.7;}
  nav a:hover,nav a.active{opacity:1;color:var(--red);}
  .cart-badge{position:relative;display:inline-flex;align-items:center;gap:4px;}
  .cart-badge .count{position:absolute;top:-8px;right:-10px;background:var(--red);color:#fff;font-size:10px;font-weight:800;padding:2px 6px;border-radius:999px;min-width:18px;text-align:center;display:none;}
  .cart-badge .count.show{display:block;}
  .auth-link{font-size:14px;font-weight:600;padding:8px 16px;border-radius:8px;transition:all .2s;}
  .auth-link:hover{background:var(--line);}
  .auth-register{background:var(--red);color:#fff;}
  .user-menu{position:relative;}
  .user-name{font-size:14px;font-weight:600;padding:8px 14px;cursor:pointer;border-radius:8px;display:flex;align-items:center;gap:6px;}
  .user-menu:hover .user-name{background:var(--line);}
  .user-dropdown{display:none;position:absolute;top:100%;right:0;margin-top:2px;background:#fff;border:1px solid var(--line);border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.1);min-width:180px;z-index:100;}
  .user-dropdown a{display:block;padding:12px 16px;font-size:14px;border-bottom:1px solid var(--line);}
  .user-dropdown a:last-child{border-bottom:none;}
  .user-dropdown a:hover{background:var(--cream);}
  .page-head{padding:40px 0 24px;text-align:center;}
  .page-head h1{font-size:clamp(26px,4vw,36px);margin-bottom:8px;}
  .page-head p{color:#5c5648;font-size:15px;}
  .layout{display:grid;grid-template-columns:1.1fr 1fr;gap:24px;align-items:start;padding-bottom:60px;}
  @media(max-width:800px){.layout{grid-template-columns:1fr;}}
  .store-list{display:flex;flex-direction:column;gap:14px;}
  .store-card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;cursor:pointer;transition:border-color .15s;}
  .store-card.active{border-color:var(--green);box-shadow:0 0 0 2px rgba(15,107,58,.12);}
  .store-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;}
  .store-name{font-weight:800;font-size:16px;}
  .store-status{font-size:11px;font-weight:700;padding:3px 10px;border-radius:999px;background:#E7F5EC;color:var(--green);}
  .store-status--closed{background:#FFE8E8;color:var(--red);}
  .store-addr{font-size:13px;color:#666;margin-bottom:10px;}
  .store-meta{display:flex;gap:14px;font-size:12px;color:#888;flex-wrap:wrap;}
  .store-meta b{color:var(--ink);}
  .map-panel{background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;position:sticky;top:20px;}
  .map-visual{height:220px;border-radius:12px;margin-bottom:16px;background:radial-gradient(circle at 30% 40%,#DCEFE1 0,transparent 40%),repeating-linear-gradient(0deg,#F2EEDD,#F2EEDD 1px,transparent 1px,transparent 32px),repeating-linear-gradient(90deg,#F2EEDD,#F2EEDD 1px,transparent 1px,transparent 32px),#FBF7EA;display:flex;align-items:center;justify-content:center;}
  .map-visual .pin{font-size:34px;filter:drop-shadow(0 4px 4px rgba(0,0,0,.15));}
  .map-detail-name{font-size:17px;font-weight:800;margin-bottom:4px;}
  .map-detail-addr{font-size:13px;color:#666;margin-bottom:14px;}
  .map-detail-row{display:flex;justify-content:space-between;font-size:13px;padding:8px 0;border-top:1px solid var(--line);}
  .map-detail-row b{color:var(--ink);}
  .btn-dir{display:block;text-align:center;margin-top:16px;padding:12px;border-radius:12px;background:var(--green);color:#fff;font-weight:700;font-size:14px;}
  footer{text-align:center;padding:30px 24px;font-size:13px;color:#999;}
</style>
</head>
<body>

<header>
  <a href="/" class="logo"><img src="../img/logo_klik_madura_v3_biru.svg" alt="Klik Madura">Klik Madura</a>
  <nav>
    <a href="/produk">Daftar Barang</a>
    <a href="/transaksi">Daftar Transaksi</a>
    <a href="/toko" class="active">Toko</a>
    <a href="/checkout" class="cart-badge">🛒
      <span class="count<?= $cartQty > 0 ? ' show' : '' ?>"><?= $cartQty ?></span>
    </a>
    <?php if ($user): ?>
      <div class="user-menu">
        <div class="user-name">👤 <?= htmlspecialchars($user['full_name'] ?: $user['username']) ?></div>
        <div class="user-dropdown">
          <?php if ($user['role'] === 'admin'): ?><a href="/admin">⚙️ Admin Panel</a><?php endif; ?>
          <a href="/transaksi">📋 Transaksi Saya</a>
          <a href="#" onclick="fetch('/api/logout.php',{method:'POST'}).then(()=>location.href='/')">🚪 Logout</a>
        </div>
      </div>
    <?php else: ?>
      <a href="/login" class="auth-link">Masuk</a>
      <a href="/register" class="auth-link auth-register">Daftar</a>
    <?php endif; ?>
  </nav>
</header>

<div class="wrap">
  <div class="page-head">
    <h1>Lokasi Toko</h1>
    <p>Warung mitra Klik Madura di sekitar kamu, buka 24 jam.</p>
  </div>
  <div class="layout">
    <div class="store-list" id="storeList">
      <p style="padding:20px;color:#999;">Memuat data toko...</p>
    </div>
    <div class="map-panel">
      <div class="map-visual"><span class="pin">📍</span></div>
      <div class="map-detail-name" id="dName">—</div>
      <div class="map-detail-addr" id="dAddr">—</div>
      <div class="map-detail-row"><span>Jarak</span><b id="dJarak">—</b></div>
      <div class="map-detail-row"><span>Jam Operasional</span><b id="dJam">—</b></div>
      <div class="map-detail-row"><span>Telepon</span><b id="dHp">—</b></div>
      <a href="#" class="btn-dir">Lihat Arah</a>
    </div>
  </div>
</div>

<footer>© 2026 Klik Madura</footer>

<script>
(function () {
  function setDetail(store) {
    document.getElementById('dName').textContent  = store.name;
    document.getElementById('dAddr').textContent  = store.address;
    document.getElementById('dJarak').textContent = store.distance;
    document.getElementById('dJam').textContent   = store.hours;
    document.getElementById('dHp').textContent    = store.phone;
  }

  async function init() {
    const res    = await fetch('/api/stores.php');
    const data   = await res.json();
    const stores = data.data || [];
    const list   = document.getElementById('storeList');
    list.innerHTML = '';

    if (!stores.length) {
      list.innerHTML = '<p style="padding:20px;color:#999;">Belum ada toko terdaftar.</p>';
      return;
    }

    stores.forEach((store, i) => {
      const isOpen      = store.status === 'open';
      const statusLabel = isOpen ? 'Buka' : 'Tutup';
      const statusCls   = isOpen ? 'store-status' : 'store-status store-status--closed';
      const card = document.createElement('div');
      card.className = 'store-card' + (i === 0 ? ' active' : '');
      card.innerHTML = `
        <div class="store-top">
          <span class="store-name">${store.name}</span>
          <span class="${statusCls}">${statusLabel}</span>
        </div>
        <div class="store-addr">${store.address}</div>
        <div class="store-meta">
          <span>📍 <b>${store.distance}</b></span>
          <span>🕛 <b>${store.hours}</b></span>
        </div>`;
      card.addEventListener('click', () => {
        document.querySelectorAll('.store-card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
        setDetail(store);
      });
      list.appendChild(card);
      if (i === 0) setDetail(store);
    });
  }

  // Dropdown hover
  (function(){
    const menu = document.querySelector('.user-menu');
    if (!menu) return;
    const drop = menu.querySelector('.user-dropdown');
    let t;
    menu.addEventListener('mouseenter', () => { clearTimeout(t); drop.style.display = 'block'; });
    menu.addEventListener('mouseleave', () => { t = setTimeout(() => drop.style.display = 'none', 200); });
    drop.addEventListener('mouseenter', () => clearTimeout(t));
    drop.addEventListener('mouseleave', () => { t = setTimeout(() => drop.style.display = 'none', 200); });
  })();

  init();
})();
</script>
</body>
</html>
