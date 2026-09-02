<?php
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/cart_helpers.php';
require_once __DIR__ . '/../includes/helpers.php';

requireLogin();
$user    = getCurrentUser();
$cartQty = getCartTotalQty((int)$user['id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Transaksi — Klik Madura</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{--red:#D4262C;--green:#0F6B3A;--amber:#B8860B;--cream:#FFFBF2;--ink:#1E1B16;--line:#EDE6D6;}
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
  .auth-link{font-size:14px;font-weight:600;padding:8px 16px;border-radius:8px;}
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
  .filter-tabs{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin-bottom:28px;}
  .filter-tabs button{padding:8px 16px;border-radius:999px;border:1px solid var(--line);background:#fff;font-family:inherit;font-weight:600;font-size:13px;color:var(--ink);cursor:pointer;}
  .filter-tabs button.active{background:var(--ink);color:#fff;border-color:var(--ink);}
  .trx-list{display:flex;flex-direction:column;gap:14px;padding-bottom:60px;}
  .trx-card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px 22px;}
  .trx-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;gap:12px;}
  .trx-id{font-size:12px;color:#999;margin-bottom:2px;}
  .trx-date{font-size:13px;color:#666;}
  .trx-status{font-size:11px;font-weight:700;padding:4px 12px;border-radius:999px;white-space:nowrap;}
  .status-selesai{background:#E7F5EC;color:var(--green);}
  .status-proses{background:#FCEDE3;color:var(--red);}
  .status-batal{background:#EFEAE1;color:#8a8375;}
  .trx-items{font-size:13px;color:#555;margin-bottom:12px;padding-left:2px;}
  .trx-bottom{display:flex;justify-content:space-between;align-items:center;border-top:1px solid var(--line);padding-top:12px;}
  .trx-store{font-size:12px;color:#888;}
  .trx-total{font-weight:800;color:var(--ink);font-size:15px;}
  .trx-total b{color:var(--green);}
  footer{text-align:center;padding:30px 24px;font-size:13px;color:#999;}
</style>
</head>
<body>

<header>
  <a href="/" class="logo"><img src="../img/logo_klik_madura_v3_biru.svg" alt="Klik Madura">Klik Madura</a>
  <nav>
    <a href="/produk">Daftar Barang</a>
    <a href="/transaksi" class="active">Daftar Transaksi</a>
    <a href="/toko">Toko</a>
    <a href="/checkout" class="cart-badge">🛒
      <span class="count<?= $cartQty > 0 ? ' show' : '' ?>"><?= $cartQty ?></span>
    </a>
    <div class="user-menu">
      <div class="user-name">👤 <?= htmlspecialchars($user['full_name'] ?: $user['username']) ?></div>
      <div class="user-dropdown">
        <?php if ($user['role'] === 'admin'): ?><a href="/admin">⚙️ Admin Panel</a><?php endif; ?>
        <a href="/transaksi">📋 Transaksi Saya</a>
        <a href="#" onclick="fetch('/api/logout.php',{method:'POST'}).then(()=>location.href='/')">🚪 Logout</a>
      </div>
    </div>
  </nav>
</header>

<div class="wrap">
  <div class="page-head">
    <h1>Daftar Transaksi</h1>
    <p>Riwayat belanja kamu dari warung Klik Madura.</p>
  </div>

  <?php if ($user['role'] === 'admin'): ?>
  <div style="background:#f0f7ff;border:1px solid #b3d9ff;padding:12px 16px;border-radius:10px;margin-bottom:20px;font-size:13px;color:#0066cc;">
    👤 Mode Admin: Menampilkan semua transaksi dari semua user
  </div>
  <?php endif; ?>

  <div class="filter-tabs">
    <button class="active" data-f="semua">Semua</button>
    <button data-f="selesai">Selesai</button>
    <button data-f="proses">Diproses</button>
    <button data-f="batal">Dibatalkan</button>
  </div>

  <div class="trx-list" id="trxList">
    <p style="text-align:center;padding:40px;color:#999;">Memuat transaksi...</p>
  </div>
</div>

<footer>© 2026 Klik Madura</footer>

<script>
(function () {
  const isAdmin = <?= $user['role'] === 'admin' ? 'true' : 'false' ?>;
  let allTransactions = [];

  function formatMoney(n) { return 'Rp' + Number(n).toLocaleString('id-ID'); }
  function formatDate(s) {
    const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    const d = new Date(s);
    return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}, ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
  }

  function statusCls(s)   { return s === 'selesai' ? 'status-selesai' : s === 'proses' ? 'status-proses' : 'status-batal'; }
  function statusLabel(s) { return s === 'selesai' ? 'Selesai' : s === 'proses' ? 'Diproses' : 'Dibatalkan'; }

  function render(filter = 'semua') {
    const container = document.getElementById('trxList');
    const list = filter === 'semua' ? allTransactions : allTransactions.filter(t => t.status === filter);
    container.innerHTML = '';

    if (!list.length) {
      container.innerHTML = `
        <div style="text-align:center;padding:60px 20px;color:#999;">
          <div style="font-size:48px;margin-bottom:16px;">📋</div>
          <div style="font-weight:600;font-size:16px;margin-bottom:8px;color:#555;">Belum Ada Transaksi</div>
          <div style="font-size:13px;margin-bottom:20px;">Yuk mulai belanja dari warung Klik Madura!</div>
          <a href="/produk" style="display:inline-block;background:var(--red);color:#fff;padding:10px 20px;border-radius:10px;font-weight:700;font-size:14px;">Mulai Belanja</a>
        </div>`;
      return;
    }

    list.forEach(trx => {
      const card = document.createElement('div');
      card.className = 'trx-card';
      card.innerHTML = `
        <div class="trx-top">
          <div>
            <div class="trx-id">${trx.order_no}</div>
            <div class="trx-date">${formatDate(trx.created_at)}</div>
            ${isAdmin && trx.user_name ? `<div style="font-size:12px;color:#666;margin-top:2px;">👤 ${trx.user_name}</div>` : ''}
          </div>
          <span class="trx-status ${statusCls(trx.status)}">${statusLabel(trx.status)}</span>
        </div>
        <div class="trx-items">${trx.items_summary || '—'}</div>
        <div class="trx-bottom">
          <span class="trx-store">${trx.store_name || '—'}</span>
          <span class="trx-total">Total <b>${formatMoney(trx.total)}</b></span>
        </div>`;
      container.appendChild(card);
    });
  }

  // Filter tabs
  document.querySelectorAll('.filter-tabs button').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-tabs button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      render(btn.dataset.f);
    });
  });

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

  // Load
  async function init() {
    const res  = await fetch('/api/transactions.php');
    const data = await res.json();
    allTransactions = data.data || [];
    render();
  }
  init();
})();
</script>
</body>
</html>
