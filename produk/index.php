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
<title>Daftar Barang — Klik Madura</title>
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
  .auth-register:hover{background:#b81f24;}
  .user-menu{position:relative;}
  .user-name{font-size:14px;font-weight:600;padding:8px 14px;cursor:pointer;border-radius:8px;display:flex;align-items:center;gap:6px;}
  .user-menu:hover .user-name{background:var(--line);}
  .user-dropdown{display:none;position:absolute;top:100%;right:0;margin-top:2px;background:#fff;border:1px solid var(--line);border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,.1);min-width:180px;z-index:100;}
  .user-dropdown a{display:block;padding:12px 16px;font-size:14px;border-bottom:1px solid var(--line);}
  .user-dropdown a:last-child{border-bottom:none;}
  .user-dropdown a:hover{background:var(--cream);}
  @media(max-width:700px){nav{gap:16px;}nav a{font-size:13px;}.auth-link{font-size:12px;padding:6px 12px;}.user-name{font-size:12px;padding:6px 10px;}}
  .page-head{padding:40px 0 24px;text-align:center;}
  .page-head h1{font-size:clamp(26px,4vw,36px);margin-bottom:8px;}
  .page-head p{color:#5c5648;font-size:15px;}
  .search-row{display:flex;justify-content:center;margin-bottom:28px;}
  .search-row input{width:100%;max-width:420px;padding:12px 16px;border-radius:12px;border:1px solid var(--line);font-family:inherit;font-size:14px;background:#fff;}
  .cat-tabs{display:flex;flex-wrap:wrap;gap:8px;justify-content:center;margin-bottom:32px;}
  .cat-tabs button{padding:8px 16px;border-radius:999px;border:1px solid var(--line);background:#fff;font-family:inherit;font-weight:600;font-size:13px;color:var(--ink);cursor:pointer;}
  .cat-tabs button.active{background:var(--green);color:#fff;border-color:var(--green);}
  .cat-block{margin-bottom:40px;}
  .cat-block h2{font-size:19px;margin-bottom:14px;}
  .item-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;}
  @media(max-width:700px){.item-grid{grid-template-columns:1fr 1fr;}}
  .item-card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:18px;position:relative;}
  .item-card.out-of-stock{opacity:.6;}
  .item-card.out-of-stock .item-name{text-decoration:line-through;}
  .stock-badge{position:absolute;top:12px;right:12px;background:#dc3545;color:#fff;font-size:10px;font-weight:800;padding:4px 8px;border-radius:999px;}
  .item-emoji{font-size:30px;margin-bottom:10px;}
  .item-name{font-size:14px;font-weight:700;margin-bottom:4px;}
  .item-desc{font-size:12px;color:#888;margin-bottom:10px;min-height:28px;}
  .item-row{display:flex;align-items:center;justify-content:space-between;}
  .item-price{font-weight:800;color:var(--green);font-size:14px;}
  .qty-box{display:flex;align-items:center;gap:8px;}
  .qty-box button{width:26px;height:26px;border-radius:8px;border:1px solid var(--line);background:var(--cream);font-weight:700;cursor:pointer;font-size:14px;line-height:1;}
  .qty-box button:disabled{opacity:.3;cursor:not-allowed;}
  .qty-box span{min-width:14px;text-align:center;font-weight:700;font-size:13px;}
  .cart-bar{position:sticky;bottom:16px;margin:32px auto 0;max-width:420px;background:var(--ink);color:#fff;border-radius:14px;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;font-weight:700;font-size:14px;}
  .cart-bar .btn-mini{background:var(--red);padding:8px 16px;border-radius:10px;font-size:13px;}
  footer{text-align:center;padding:30px 24px;font-size:13px;color:#999;}
</style>
</head>
<body>

<header>
  <a href="/" class="logo"><img src="../img/logo_klik_madura_v3_biru.svg" alt="Klik Madura">Klik Madura</a>
  <nav>
    <a href="/produk" class="active">Daftar Barang</a>
    <a href="/transaksi">Daftar Transaksi</a>
    <a href="/toko">Toko</a>
    <a href="/checkout" class="cart-badge">🛒
      <span class="count<?= $cartQty > 0 ? ' show' : '' ?>" id="cartCount"><?= $cartQty ?></span>
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
    <h1>Daftar Barang</h1>
    <p>Semua yang biasa kamu beli di warung, tinggal klik.</p>
  </div>
  <div class="search-row">
    <input type="text" id="searchInput" placeholder="Cari barang, misalnya: kopi, mie, pulsa...">
  </div>
  <div class="cat-tabs" id="catTabs"></div>
  <div id="productBlocks"></div>
  <div class="cart-bar">
    <span id="cartLabel">Keranjang: <?= $cartQty ?> item</span>
    <a href="/checkout" class="btn-mini">Checkout</a>
  </div>
</div>

<footer>© 2026 Klik Madura</footer>

<script>
(function () {
  // ── State ──────────────────────────────────────────────────
  let PRODUCTS   = [];
  let cartQty    = <?= $cartQty ?>;
  const isLoggedIn = <?= $user ? 'true' : 'false' ?>;

  // ── Helpers ───────────────────────────────────────────────
  function formatMoney(n) { return 'Rp' + Number(n).toLocaleString('id-ID'); }

  function updateCartUI(qty) {
    cartQty = qty;
    const badge = document.getElementById('cartCount');
    const label = document.getElementById('cartLabel');
    if (badge) { badge.textContent = qty; badge.classList.toggle('show', qty > 0); }
    if (label)   label.textContent = 'Keranjang: ' + qty + ' item';
  }

  // ── Cart API helpers ──────────────────────────────────────
  async function apiAddToCart(productId, qty = 1) {
    if (!isLoggedIn) {
      // Simpan ke localStorage untuk guest
      const cart = JSON.parse(localStorage.getItem('klikMaduraCart') || '[]');
      const existing = cart.find(i => i.id === productId);
      if (existing) { existing.qty += qty; } else {
        const p = PRODUCTS.find(p => p.id === productId);
        if (p) cart.push({ id: p.id, name: p.name, emoji: p.emoji, price: p.price, qty });
      }
      localStorage.setItem('klikMaduraCart', JSON.stringify(cart));
      const totalQty = cart.reduce((s, i) => s + i.qty, 0);
      updateCartUI(totalQty);
      return;
    }
    const res  = await fetch('/api/cart.php', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_id: productId, qty }),
    });
    const data = await res.json();
    if (data.success) {
      const r = await fetch('/api/cart.php');
      const d = await r.json();
      updateCartUI(d.total_qty || 0);
    }
  }

  async function apiUpdateCart(productId, qty) {
    if (!isLoggedIn) {
      let cart = JSON.parse(localStorage.getItem('klikMaduraCart') || '[]');
      if (qty <= 0) { cart = cart.filter(i => i.id !== productId); }
      else { const item = cart.find(i => i.id === productId); if (item) item.qty = qty; }
      localStorage.setItem('klikMaduraCart', JSON.stringify(cart));
      updateCartUI(cart.reduce((s, i) => s + i.qty, 0));
      return;
    }
    await fetch('/api/cart.php', {
      method: 'PUT', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_id: productId, qty }),
    });
    const r = await fetch('/api/cart.php');
    const d = await r.json();
    updateCartUI(d.total_qty || 0);
  }

  async function getCartQtyForProduct(productId) {
    if (!isLoggedIn) {
      const cart = JSON.parse(localStorage.getItem('klikMaduraCart') || '[]');
      const item = cart.find(i => i.id === productId);
      return item ? item.qty : 0;
    }
    const r = await fetch('/api/cart.php');
    const d = await r.json();
    const item = (d.data || []).find(i => i.product_id === productId);
    return item ? item.qty : 0;
  }

  // ── Build card ────────────────────────────────────────────
  function buildCard(product, currentQty = 0) {
    const outOfStock = !product.inStock;
    const card = document.createElement('div');
    card.className = 'item-card' + (outOfStock ? ' out-of-stock' : '');
    card.dataset.productId = product.id;
    card.innerHTML = `
      ${outOfStock ? '<div class="stock-badge">HABIS</div>' : ''}
      <div class="item-emoji">${product.emoji}</div>
      <div class="item-name">${product.name}</div>
      <div class="item-desc">${product.desc || ''}</div>
      <div class="item-row">
        <span class="item-price">${formatMoney(product.price)}</span>
        <div class="qty-box">
          <button class="btn-minus" ${outOfStock ? 'disabled' : ''}>−</button>
          <span>${currentQty}</span>
          <button class="btn-plus"  ${outOfStock ? 'disabled' : ''}>+</button>
        </div>
      </div>`;

    const span  = card.querySelector('.qty-box span');
    const minus = card.querySelector('.btn-minus');
    const plus  = card.querySelector('.btn-plus');

    minus.addEventListener('click', async () => {
      const cur = parseInt(span.textContent);
      if (cur > 0) { span.textContent = cur - 1; await apiUpdateCart(product.id, cur - 1); }
    });
    plus.addEventListener('click', async () => {
      const cur = parseInt(span.textContent);
      span.textContent = cur + 1;
      if (cur === 0) await apiAddToCart(product.id, 1);
      else           await apiUpdateCart(product.id, cur + 1);
    });
    return card;
  }

  // ── Render ────────────────────────────────────────────────
  async function renderProducts(list) {
    const container = document.getElementById('productBlocks');
    container.innerHTML = '';
    if (!list.length) {
      container.innerHTML = '<div style="text-align:center;padding:60px 20px;color:#999;"><div style="font-size:48px;margin-bottom:12px;">🔍</div><div style="font-weight:600;color:#555;">Produk tidak ditemukan</div></div>';
      return;
    }

    // Ambil cart sekali untuk seluruh render
    let cartMap = {};
    if (isLoggedIn) {
      const r = await fetch('/api/cart.php');
      const d = await r.json();
      (d.data || []).forEach(i => { cartMap[i.product_id] = i.qty; });
    } else {
      const localCart = JSON.parse(localStorage.getItem('klikMaduraCart') || '[]');
      localCart.forEach(i => { cartMap[i.id] = i.qty; });
    }

    const grouped = {};
    list.forEach(p => {
      if (!grouped[p.category]) grouped[p.category] = [];
      grouped[p.category].push(p);
    });

    Object.entries(grouped).forEach(([cat, products]) => {
      const block = document.createElement('div');
      block.className = 'cat-block';
      const grid = document.createElement('div');
      grid.className = 'item-grid';
      products.forEach(p => grid.appendChild(buildCard(p, cartMap[p.id] || 0)));
      block.innerHTML = `<h2>${cat}</h2>`;
      block.appendChild(grid);
      container.appendChild(block);
    });
  }

  function renderCategoryTabs(products) {
    const tabs = document.getElementById('catTabs');
    tabs.innerHTML = '';
    const cats = ['Semua', ...new Set(products.map(p => p.category))];
    cats.forEach((cat, i) => {
      const btn = document.createElement('button');
      btn.textContent = cat;
      if (i === 0) btn.classList.add('active');
      btn.addEventListener('click', () => {
        tabs.querySelectorAll('button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('searchInput').value = '';
        renderProducts(cat === 'Semua' ? PRODUCTS : PRODUCTS.filter(p => p.category === cat));
      });
      tabs.appendChild(btn);
    });
  }

  // ── Init ──────────────────────────────────────────────────
  async function init() {
    const res  = await fetch('/api/products.php');
    const data = await res.json();
    PRODUCTS   = data.data || [];

    renderCategoryTabs(PRODUCTS);
    await renderProducts(PRODUCTS);

    // Search
    document.getElementById('searchInput').addEventListener('input', e => {
      const q = e.target.value.trim().toLowerCase();
      document.querySelectorAll('#catTabs button').forEach(b => b.classList.remove('active'));
      const semua = document.querySelector('#catTabs button');
      if (semua) semua.classList.add('active');
      if (!q) { renderProducts(PRODUCTS); return; }
      const results = PRODUCTS.filter(p =>
        p.name.toLowerCase().includes(q) ||
        (p.desc  && p.desc.toLowerCase().includes(q)) ||
        (p.tags  && p.tags.some(t => t.toLowerCase().includes(q)))
      );
      renderProducts(results);
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
