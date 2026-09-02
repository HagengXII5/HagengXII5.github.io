<?php
require_once __DIR__ . '/../includes/auth_helpers.php';
require_once __DIR__ . '/../includes/helpers.php';

requireAdmin(); // redirect non-admin ke /

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel — Klik Madura</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{--red:#D4262C;--green:#0F6B3A;--amber:#c47a00;--cream:#FFFBF2;--ink:#1E1B16;--line:#EDE6D6;--sidebar:220px;}
  *{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Plus Jakarta Sans',sans-serif;background:#f5f3ef;color:var(--ink);line-height:1.55;display:flex;flex-direction:column;min-height:100vh;}
  h1,h2,h3{font-family:'Baloo 2',sans-serif;}
  a{text-decoration:none;color:inherit;}
  button,input,select,textarea{font-family:inherit;}

  /* ── Top bar ─────────────────────────────────────────── */
  .topbar{background:#fff;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:14px 28px;position:sticky;top:0;z-index:40;}
  .topbar-left{display:flex;align-items:center;gap:16px;}
  .topbar-logo{font-family:'Baloo 2';font-weight:800;font-size:18px;display:flex;align-items:center;gap:8px;}
  .topbar-logo img{height:38px;}
  .admin-badge{font-size:11px;font-weight:700;background:#FCF3F1;color:var(--red);border:1px solid #f5c6c6;padding:3px 10px;border-radius:999px;}
  .topbar-right{display:flex;align-items:center;gap:16px;}
  .topbar-user{font-size:13.5px;font-weight:600;}
  .btn-logout{font-size:13px;font-weight:700;color:#777;cursor:pointer;background:none;border:1px solid var(--line);border-radius:8px;padding:6px 14px;}
  .btn-logout:hover{border-color:#aaa;color:var(--ink);}

  /* ── Main layout ─────────────────────────────────────── */
  .main{flex:1;padding:28px 28px 60px;}
  @media(max-width:700px){.main{padding:16px;}}

  /* ── Stats grid ──────────────────────────────────────── */
  .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px;}
  @media(max-width:900px){.stats-grid{grid-template-columns:repeat(2,1fr);}}
  @media(max-width:500px){.stats-grid{grid-template-columns:1fr;}}
  .stat-card{background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px 22px;}
  .stat-label{font-size:12px;color:#888;font-weight:600;margin-bottom:4px;}
  .stat-val{font-size:26px;font-family:'Baloo 2';font-weight:800;}
  .stat-val.green{color:var(--green);}
  .stat-val.red{color:var(--red);}
  .stat-val.amber{color:var(--amber);}

  /* ── Tabs ────────────────────────────────────────────── */
  .tabs{display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--line);}
  .tabs button{padding:10px 20px;border:none;background:none;font-family:inherit;font-weight:700;font-size:14px;color:#888;cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;}
  .tabs button.active{color:var(--red);border-bottom-color:var(--red);}
  .tab-content{display:none;}
  .tab-content.active{display:block;}

  /* ── Section header ──────────────────────────────────── */
  .sec-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;gap:12px;flex-wrap:wrap;}
  .sec-head h2{font-size:18px;}
  .sec-alerts{margin-bottom:12px;}

  /* ── Alert ───────────────────────────────────────────── */
  .alert{padding:11px 16px;border-radius:10px;font-size:13.5px;margin-bottom:12px;}
  .alert-success{background:#E7F5EC;color:var(--green);border:1px solid #b7e4c7;}
  .alert-error{background:#ffe6e6;color:#c0392b;border:1px solid #ffcccc;}

  /* ── Table ───────────────────────────────────────────── */
  .tbl-wrap{background:#fff;border:1px solid var(--line);border-radius:16px;overflow:auto;}
  table{width:100%;border-collapse:collapse;}
  th,td{padding:11px 14px;text-align:left;font-size:13px;vertical-align:middle;}
  th{font-size:11.5px;font-weight:800;color:#888;border-bottom:1px solid var(--line);background:#fafaf8;}
  tr:not(:last-child) td{border-bottom:1px solid var(--line);}
  tr:hover td{background:#fcfaf6;}
  .product-emoji{font-size:22px;margin-right:8px;}
  .product-name{font-weight:700;font-size:13.5px;}
  .product-desc{font-size:12px;color:#888;margin-top:2px;}
  .product-price{font-weight:800;color:var(--green);}
  .product-actions{display:flex;gap:6px;flex-wrap:wrap;}
  .empty-row td{text-align:center;padding:44px;color:#999;font-size:13px;}

  /* ── Badges ──────────────────────────────────────────── */
  .badge{font-size:11px;font-weight:700;padding:3px 10px;border-radius:999px;}
  .badge-success{background:#E7F5EC;color:var(--green);}
  .badge-danger{background:#ffe6e6;color:var(--red);}
  .badge-warning{background:#FFF3CD;color:var(--amber);}
  .badge-secondary{background:#EFEAE1;color:#8a8375;}

  /* ── Buttons ─────────────────────────────────────────── */
  .btn{display:inline-flex;align-items:center;gap:5px;padding:9px 16px;border-radius:10px;border:none;font-weight:700;font-size:13px;cursor:pointer;transition:opacity .15s;}
  .btn:hover{opacity:.85;}
  .btn-primary{background:var(--red);color:#fff;}
  .btn-success{background:var(--green);color:#fff;}
  .btn-outline{background:#fff;border:1px solid var(--line);color:var(--ink);}
  .btn-danger{background:#FFE8E8;color:var(--red);border:1px solid #ffc0c0;}
  .btn-sm{padding:6px 12px;font-size:12px;border-radius:8px;}

  /* ── Modal ───────────────────────────────────────────── */
  .overlay{position:fixed;inset:0;background:rgba(30,27,22,.5);display:none;align-items:flex-start;justify-content:center;padding:40px 20px;z-index:50;overflow-y:auto;}
  .overlay.show{display:flex;}
  .modal{background:#fff;border-radius:20px;padding:32px;max-width:520px;width:100%;margin:auto;}
  .modal h3{font-size:20px;margin-bottom:20px;}
  .modal-close{float:right;background:none;border:none;font-size:20px;cursor:pointer;color:#aaa;margin-top:-6px;}
  .modal-close:hover{color:var(--ink);}
  .form-group{margin-bottom:16px;}
  .form-group label{display:block;font-size:13px;font-weight:700;margin-bottom:6px;}
  .form-group input,.form-group select,.form-group textarea{width:100%;padding:11px 14px;border-radius:10px;border:1px solid var(--line);font-size:14px;background:#fff;}
  .form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:var(--red);}
  .form-group.two-col{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
  .form-group.two-col .fg-inner label{display:block;font-size:13px;font-weight:700;margin-bottom:6px;}
  .form-group.two-col .fg-inner input{width:100%;padding:11px 14px;border-radius:10px;border:1px solid var(--line);font-size:14px;}
  .checkbox-row{display:flex;align-items:center;gap:8px;cursor:pointer;}
  .checkbox-row input{width:16px;height:16px;accent-color:var(--green);}
  .modal-footer{display:flex;gap:10px;justify-content:flex-end;margin-top:24px;}
  .trx-id-cell{font-size:11.5px;font-weight:700;color:#888;}
  .trx-user-cell{font-weight:600;font-size:13px;}
</style>
</head>
<body>

<!-- ── Top bar ───────────────────────────────────────────── -->
<div class="topbar">
  <div class="topbar-left">
    <div class="topbar-logo">
      <img src="../img/logo_klik_madura_v3_biru.svg" alt="Klik Madura">Klik Madura
    </div>
    <span class="admin-badge">Admin Panel</span>
  </div>
  <div class="topbar-right">
    <span class="topbar-user">👤 <?= htmlspecialchars($user['full_name'] ?: $user['username']) ?></span>
    <a href="/"><button class="btn-logout">← Ke Toko</button></a>
    <button class="btn-logout" onclick="fetch('/api/logout.php',{method:'POST'}).then(()=>location.href='/')">Logout</button>
  </div>
</div>

<!-- ── Main ──────────────────────────────────────────────── -->
<div class="main">

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card"><div class="stat-label">Total Produk</div><div class="stat-val" id="statProducts">—</div></div>
    <div class="stat-card"><div class="stat-label">Total Toko</div><div class="stat-val green" id="statStores">—</div></div>
    <div class="stat-card"><div class="stat-label">Total Transaksi</div><div class="stat-val amber" id="statTransactions">—</div></div>
    <div class="stat-card"><div class="stat-label">Total Pendapatan</div><div class="stat-val red" id="statRevenue">—</div></div>
  </div>

  <!-- Tabs -->
  <div class="tabs">
    <button class="active" onclick="switchTab('products', this)">📦 Produk</button>
    <button onclick="switchTab('stores', this)">🏪 Toko</button>
    <button onclick="switchTab('transactions', this)">📋 Transaksi</button>
  </div>

  <!-- ── Tab: Produk ──────────────────────────────────── -->
  <div class="tab-content active" id="tab-products">
    <div class="sec-head">
      <h2>Daftar Produk</h2>
      <button class="btn btn-primary" onclick="openAddProductModal()">+ Tambah Produk</button>
    </div>
    <div class="sec-alerts" id="alertProducts"></div>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr>
        </thead>
        <tbody id="productTableBody">
          <tr class="empty-row"><td colspan="5">Memuat produk...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ── Tab: Toko ─────────────────────────────────────── -->
  <div class="tab-content" id="tab-stores">
    <div class="sec-head">
      <h2>Daftar Toko</h2>
      <button class="btn btn-primary" onclick="openAddStoreModal()">+ Tambah Toko</button>
    </div>
    <div class="sec-alerts" id="alertStores"></div>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr><th>Toko</th><th>Alamat</th><th>Jam</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody id="storeTableBody">
          <tr class="empty-row"><td colspan="5">Memuat toko...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ── Tab: Transaksi ────────────────────────────────── -->
  <div class="tab-content" id="tab-transactions">
    <div class="sec-head">
      <h2>Daftar Transaksi</h2>
    </div>
    <div class="sec-alerts" id="alertTransactions"></div>
    <div class="tbl-wrap">
      <table>
        <thead>
          <tr><th>No. Order</th><th>User</th><th>Barang</th><th>Total</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr>
        </thead>
        <tbody id="transactionTableBody">
          <tr class="empty-row"><td colspan="7">Memuat transaksi...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

</div><!-- /main -->


<!-- ══════════════════════════════════════════════════════════
     MODAL: Tambah/Edit Produk
════════════════════════════════════════════════════════════ -->
<div class="overlay" id="productModal">
  <div class="modal">
    <button class="modal-close" onclick="closeProductModal()">✕</button>
    <h3 id="modalProductTitle">Tambah Produk</h3>
    <div id="modalProductAlert"></div>
    <form id="productForm" onsubmit="saveProduct(event)">
      <input type="hidden" id="productId">
      <div class="form-group two-col">
        <div class="fg-inner">
          <label for="productEmoji">Emoji</label>
          <input type="text" id="productEmoji" placeholder="☕" maxlength="4">
        </div>
        <div class="fg-inner">
          <label for="productPrice">Harga (Rp)</label>
          <input type="number" id="productPrice" placeholder="13500" min="0" required>
        </div>
      </div>
      <div class="form-group">
        <label for="productName">Nama Produk *</label>
        <input type="text" id="productName" placeholder="Kapal Api Special Mix" required>
      </div>
      <div class="form-group">
        <label for="productDesc">Deskripsi</label>
        <input type="text" id="productDesc" placeholder="Kopi + gula, 10 sachet">
      </div>
      <div class="form-group">
        <label for="productCategory">Kategori *</label>
        <select id="productCategory" required>
          <option value="">— Pilih Kategori —</option>
        </select>
      </div>
      <div class="form-group">
        <label class="checkbox-row">
          <input type="checkbox" id="productInStock" checked>
          <span>Stok tersedia</span>
        </label>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeProductModal()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- ══════════════════════════════════════════════════════════
     MODAL: Tambah/Edit Toko
════════════════════════════════════════════════════════════ -->
<div class="overlay" id="storeModal">
  <div class="modal">
    <button class="modal-close" onclick="closeStoreModal()">✕</button>
    <h3 id="modalStoreTitle">Tambah Toko</h3>
    <div id="modalStoreAlert"></div>
    <form id="storeForm" onsubmit="saveStore(event)">
      <input type="hidden" id="storeId">
      <div class="form-group">
        <label for="storeName">Nama Toko *</label>
        <input type="text" id="storeName" placeholder="Warung Madura Margonda" required>
      </div>
      <div class="form-group">
        <label for="storeAddress">Alamat *</label>
        <input type="text" id="storeAddress" placeholder="Jl. Margonda Raya No. 45, Depok" required>
      </div>
      <div class="form-group two-col">
        <div class="fg-inner">
          <label for="storeHours">Jam Operasional</label>
          <input type="text" id="storeHours" placeholder="24 Jam">
        </div>
        <div class="fg-inner">
          <label for="storePhone">Nomor HP</label>
          <input type="text" id="storePhone" placeholder="0812-3456-7890">
        </div>
      </div>
      <div class="form-group">
        <label for="storeDistance">Jarak (tampilan)</label>
        <input type="text" id="storeDistance" placeholder="0,8 km">
      </div>
      <div class="form-group">
        <label class="checkbox-row">
          <input type="checkbox" id="storeStatus" checked>
          <span>Toko sedang buka</span>
        </label>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline" onclick="closeStoreModal()">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>


<script>
/* ════════════════════════════════════════════════════════════
   Globals & helpers
═══════════════════════════════════════════════════════════ */
let PRODUCTS      = [];
let STORES        = [];
let TRANSACTIONS  = [];
let CATEGORIES    = [];
let editingProductId = null;
let editingStoreId   = null;

function fmt(n)  { return 'Rp' + Number(n).toLocaleString('id-ID'); }
function fmtDate(s) {
  const months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  const d = new Date(s);
  if (isNaN(d)) return s;
  return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}, ${String(d.getHours()).padStart(2,'0')}:${String(d.getMinutes()).padStart(2,'0')}`;
}

async function apiFetch(url, opts = {}) {
  const res  = await fetch(url, { headers:{ 'Content-Type':'application/json' }, ...opts });
  return res.json();
}

function showAlert(containerId, type, msg) {
  const el = document.getElementById(containerId);
  if (!el) return;
  el.innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
  setTimeout(() => { if(el) el.innerHTML = ''; }, 3500);
}

/* ════════════════════════════════════════════════════════════
   Tabs
═══════════════════════════════════════════════════════════ */
function switchTab(name, btn) {
  document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
  document.querySelectorAll('.tabs button').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-' + name).classList.add('active');
  if (btn) btn.classList.add('active');
}

/* ════════════════════════════════════════════════════════════
   Stats
═══════════════════════════════════════════════════════════ */
function updateStats() {
  const revenue = TRANSACTIONS
    .filter(t => t.status === 'selesai')
    .reduce((s, t) => s + parseInt(t.total || 0), 0);

  document.getElementById('statProducts').textContent    = PRODUCTS.length;
  document.getElementById('statStores').textContent      = STORES.length;
  document.getElementById('statTransactions').textContent= TRANSACTIONS.length;
  document.getElementById('statRevenue').textContent     = fmt(revenue);
}

/* ════════════════════════════════════════════════════════════
   ██ PRODUK ██
═══════════════════════════════════════════════════════════ */
function renderProducts() {
  const tbody = document.getElementById('productTableBody');
  if (!PRODUCTS.length) {
    tbody.innerHTML = '<tr class="empty-row"><td colspan="5">Belum ada produk.</td></tr>';
    return;
  }
  tbody.innerHTML = '';
  PRODUCTS.forEach(p => {
    const inStock    = p.inStock ?? p.in_stock ?? true;
    const stockCls   = inStock ? 'badge-success' : 'badge-danger';
    const stockLabel = inStock ? 'Tersedia' : 'Habis';
    const catDisplay = p.category || (p.category_icon ? p.category_icon + ' ' + p.category_name : p.category_id);
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>
        <div style="display:flex;align-items:center;gap:10px;">
          <span class="product-emoji">${p.emoji||''}</span>
          <div>
            <div class="product-name">${p.name}</div>
            <div class="product-desc">${p.desc || p.description || ''}</div>
          </div>
        </div>
      </td>
      <td style="font-size:12px;color:#666;">${catDisplay}</td>
      <td class="product-price">${fmt(p.price)}</td>
      <td><span class="badge ${stockCls}">${stockLabel}</span></td>
      <td>
        <div class="product-actions">
          <button class="btn btn-sm btn-outline"  onclick="toggleProductStock('${p.id}')">
            ${inStock ? '❌ Habiskan' : '✅ Sediakan'}
          </button>
          <button class="btn btn-sm btn-outline" onclick="openEditProductModal('${p.id}')">✏️ Edit</button>
          <button class="btn btn-sm btn-danger"  onclick="deleteProduct('${p.id}', '${p.name.replace(/'/g,"\\'")}')">🗑️ Hapus</button>
        </div>
      </td>`;
    tbody.appendChild(tr);
  });
}

function openAddProductModal() {
  editingProductId = null;
  document.getElementById('modalProductTitle').textContent = 'Tambah Produk';
  document.getElementById('productForm').reset();
  document.getElementById('productId').value  = '';
  document.getElementById('productInStock').checked = true;
  document.getElementById('modalProductAlert').innerHTML = '';
  document.getElementById('productModal').classList.add('show');
}

function openEditProductModal(productId) {
  const p = PRODUCTS.find(x => x.id === productId);
  if (!p) return;
  editingProductId = productId;
  document.getElementById('modalProductTitle').textContent = 'Edit Produk';
  document.getElementById('productId').value       = p.id;
  document.getElementById('productEmoji').value    = p.emoji || '';
  document.getElementById('productName').value     = p.name;
  document.getElementById('productDesc').value     = p.desc || p.description || '';
  document.getElementById('productPrice').value    = p.price;
  document.getElementById('productCategory').value = p.category_id || '';
  document.getElementById('productInStock').checked = !!(p.inStock ?? p.in_stock);
  document.getElementById('modalProductAlert').innerHTML = '';
  document.getElementById('productModal').classList.add('show');
}

function closeProductModal() {
  document.getElementById('productModal').classList.remove('show');
}

async function saveProduct(e) {
  e.preventDefault();
  const id          = document.getElementById('productId').value;
  const emoji       = document.getElementById('productEmoji').value.trim();
  const name        = document.getElementById('productName').value.trim();
  const description = document.getElementById('productDesc').value.trim();
  const price       = parseInt(document.getElementById('productPrice').value);
  const category_id = document.getElementById('productCategory').value;
  const in_stock    = document.getElementById('productInStock').checked;

  if (!name || !category_id || isNaN(price)) {
    document.getElementById('modalProductAlert').innerHTML =
      '<div class="alert alert-error">Nama, kategori, dan harga wajib diisi.</div>';
    return;
  }

  const payload = { emoji, name, description, price, category_id, in_stock, tags: name.toLowerCase().split(' ') };

  let result;
  if (editingProductId) {
    result = await apiFetch(`/api/products.php?id=${encodeURIComponent(editingProductId)}`, {
      method: 'PUT', body: JSON.stringify(payload),
    });
  } else {
    result = await apiFetch('/api/products.php', { method: 'POST', body: JSON.stringify(payload) });
  }

  if (result.success) {
    closeProductModal();
    await loadProducts();
    showAlert('alertProducts', 'success', result.message);
    updateStats();
  } else {
    document.getElementById('modalProductAlert').innerHTML =
      `<div class="alert alert-error">${result.message}</div>`;
  }
}

async function toggleProductStock(id) {
  const res = await apiFetch(`/api/products.php?id=${encodeURIComponent(id)}&action=toggle_stock`, { method: 'PATCH' });
  if (res.success) {
    await loadProducts();
    showAlert('alertProducts', 'success', res.message);
    updateStats();
  } else {
    showAlert('alertProducts', 'error', res.message);
  }
}

async function deleteProduct(id, name) {
  if (!confirm(`Hapus produk "${name}"?\n\nProduk yang sudah dihapus tidak bisa dikembalikan.`)) return;
  const res = await apiFetch(`/api/products.php?id=${encodeURIComponent(id)}`, { method: 'DELETE' });
  if (res.success) {
    await loadProducts();
    showAlert('alertProducts', 'success', res.message);
    updateStats();
  } else {
    showAlert('alertProducts', 'error', res.message);
  }
}

async function loadProducts() {
  const res = await apiFetch('/api/products.php');
  PRODUCTS  = res.data || [];
  renderProducts();
}

async function loadCategories() {
  const res   = await apiFetch('/api/products.php?categories=1');
  CATEGORIES  = res.data || [];
  const sel   = document.getElementById('productCategory');
  sel.innerHTML = '<option value="">— Pilih Kategori —</option>';
  CATEGORIES.forEach(c => {
    const opt = document.createElement('option');
    opt.value       = c.id;
    opt.textContent = `${c.icon} ${c.name}`;
    sel.appendChild(opt);
  });
}

/* ════════════════════════════════════════════════════════════
   ██ TOKO ██
═══════════════════════════════════════════════════════════ */
function renderStores() {
  const tbody = document.getElementById('storeTableBody');
  if (!STORES.length) {
    tbody.innerHTML = '<tr class="empty-row"><td colspan="5">Belum ada toko.</td></tr>';
    return;
  }
  tbody.innerHTML = '';
  STORES.forEach(s => {
    const isOpen    = s.status === 'open';
    const statusCls = isOpen ? 'badge-success' : 'badge-danger';
    const statusLbl = isOpen ? 'Buka' : 'Tutup';
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>
        <div style="font-weight:700;">${s.name}</div>
        <div style="font-size:12px;color:#888;">${s.phone || '—'}</div>
      </td>
      <td style="font-size:12px;color:#666;max-width:200px;">${s.address}</td>
      <td style="font-size:13px;">${s.hours}</td>
      <td><span class="badge ${statusCls}">${statusLbl}</span></td>
      <td>
        <div class="product-actions">
          <button class="btn btn-sm btn-outline" onclick="toggleStoreStatus('${s.id}')">
            ${isOpen ? '🔒 Tutup' : '🔓 Buka'}
          </button>
          <button class="btn btn-sm btn-outline" onclick="openEditStoreModal('${s.id}')">✏️ Edit</button>
          <button class="btn btn-sm btn-danger"  onclick="deleteStore('${s.id}', '${s.name.replace(/'/g,"\\'")}')">🗑️ Hapus</button>
        </div>
      </td>`;
    tbody.appendChild(tr);
  });
}

function openAddStoreModal() {
  editingStoreId = null;
  document.getElementById('modalStoreTitle').textContent = 'Tambah Toko';
  document.getElementById('storeForm').reset();
  document.getElementById('storeId').value = '';
  document.getElementById('storeStatus').checked = true;
  document.getElementById('modalStoreAlert').innerHTML = '';
  document.getElementById('storeModal').classList.add('show');
}

function openEditStoreModal(storeId) {
  const s = STORES.find(x => x.id === storeId);
  if (!s) return;
  editingStoreId = storeId;
  document.getElementById('modalStoreTitle').textContent = 'Edit Toko';
  document.getElementById('storeId').value        = s.id;
  document.getElementById('storeName').value      = s.name;
  document.getElementById('storeAddress').value   = s.address;
  document.getElementById('storeHours').value     = s.hours;
  document.getElementById('storePhone').value     = s.phone;
  document.getElementById('storeDistance').value  = s.distance;
  document.getElementById('storeStatus').checked  = s.status === 'open';
  document.getElementById('modalStoreAlert').innerHTML = '';
  document.getElementById('storeModal').classList.add('show');
}

function closeStoreModal() {
  document.getElementById('storeModal').classList.remove('show');
}

async function saveStore(e) {
  e.preventDefault();
  const id       = document.getElementById('storeId').value;
  const name     = document.getElementById('storeName').value.trim();
  const address  = document.getElementById('storeAddress').value.trim();
  const hours    = document.getElementById('storeHours').value.trim() || '24 Jam';
  const phone    = document.getElementById('storePhone').value.trim();
  const distance = document.getElementById('storeDistance').value.trim() || '-';
  const status   = document.getElementById('storeStatus').checked ? 'open' : 'closed';

  if (!name || !address) {
    document.getElementById('modalStoreAlert').innerHTML =
      '<div class="alert alert-error">Nama dan alamat toko wajib diisi.</div>';
    return;
  }

  const payload = { name, address, hours, phone, distance, status };
  let result;
  if (editingStoreId) {
    result = await apiFetch(`/api/stores.php?id=${encodeURIComponent(editingStoreId)}`, {
      method: 'PUT', body: JSON.stringify(payload),
    });
  } else {
    result = await apiFetch('/api/stores.php', { method: 'POST', body: JSON.stringify(payload) });
  }

  if (result.success) {
    closeStoreModal();
    await loadStores();
    showAlert('alertStores', 'success', result.message);
    updateStats();
  } else {
    document.getElementById('modalStoreAlert').innerHTML =
      `<div class="alert alert-error">${result.message}</div>`;
  }
}

async function toggleStoreStatus(id) {
  const res = await apiFetch(`/api/stores.php?id=${encodeURIComponent(id)}&action=toggle_status`, { method: 'PATCH' });
  if (res.success) {
    await loadStores();
    showAlert('alertStores', 'success', res.message);
  } else {
    showAlert('alertStores', 'error', res.message);
  }
}

async function deleteStore(id, name) {
  if (!confirm(`Hapus toko "${name}"?\n\nToko yang sudah dihapus tidak bisa dikembalikan.`)) return;
  const res = await apiFetch(`/api/stores.php?id=${encodeURIComponent(id)}`, { method: 'DELETE' });
  if (res.success) {
    await loadStores();
    showAlert('alertStores', 'success', res.message);
    updateStats();
  } else {
    showAlert('alertStores', 'error', res.message);
  }
}

async function loadStores() {
  const res = await apiFetch('/api/stores.php');
  STORES    = res.data || [];
  renderStores();
}

/* ════════════════════════════════════════════════════════════
   ██ TRANSAKSI ██
═══════════════════════════════════════════════════════════ */
function renderTransactions() {
  const tbody = document.getElementById('transactionTableBody');
  if (!TRANSACTIONS.length) {
    tbody.innerHTML = '<tr class="empty-row"><td colspan="7">Belum ada transaksi.</td></tr>';
    return;
  }
  tbody.innerHTML = '';
  TRANSACTIONS.forEach(t => {
    const statusMap = {
      selesai:  { cls:'badge-success',   lbl:'Selesai' },
      proses:   { cls:'badge-warning',   lbl:'Proses' },
      batal:    { cls:'badge-secondary', lbl:'Batal' },
    };
    const { cls, lbl } = statusMap[t.status] || { cls:'badge-secondary', lbl:t.status };

    let actions = `<button class="btn btn-sm btn-danger" onclick="deleteTransaction('${t.order_no.replace(/'/g,"\\'")}')">🗑️ Hapus</button>`;
    if (t.status === 'proses') {
      actions = `
        <button class="btn btn-sm btn-success" style="margin-right:4px;" onclick="completeTransaction('${t.order_no.replace(/'/g,"\\'")}')">✅ Selesaikan</button>
        ${actions}`;
    }

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="trx-id-cell">${t.order_no}</td>
      <td class="trx-user-cell">${t.user_name || 'Guest'}</td>
      <td style="max-width:260px;font-size:12px;">${t.items_summary || '—'}</td>
      <td style="font-weight:800;color:var(--green);">${fmt(t.total)}</td>
      <td><span class="badge ${cls}">${lbl}</span></td>
      <td style="font-size:12px;color:#666;">${fmtDate(t.created_at)}</td>
      <td><div class="product-actions">${actions}</div></td>`;
    tbody.appendChild(tr);
  });
}

async function completeTransaction(orderNo) {
  const res = await apiFetch(`/api/transactions.php?id=${encodeURIComponent(orderNo)}&action=update_status`, {
    method: 'PATCH', body: JSON.stringify({ status: 'selesai' }),
  });
  if (res.success) {
    await loadTransactions();
    showAlert('alertTransactions', 'success', res.message);
    updateStats();
  } else {
    showAlert('alertTransactions', 'error', res.message);
  }
}

async function deleteTransaction(orderNo) {
  if (!confirm(`Hapus transaksi ${orderNo}?\n\nTransaksi yang sudah dihapus tidak bisa dikembalikan.`)) return;
  const res = await apiFetch(`/api/transactions.php?id=${encodeURIComponent(orderNo)}`, { method: 'DELETE' });
  if (res.success) {
    await loadTransactions();
    showAlert('alertTransactions', 'success', res.message);
    updateStats();
  } else {
    showAlert('alertTransactions', 'error', res.message);
  }
}

async function loadTransactions() {
  const res    = await apiFetch('/api/transactions.php');
  TRANSACTIONS = res.data || [];
  renderTransactions();
}

/* ════════════════════════════════════════════════════════════
   Init
═══════════════════════════════════════════════════════════ */
(async function init() {
  await Promise.all([loadCategories(), loadProducts(), loadStores(), loadTransactions()]);
  updateStats();
})();

// Close modal on overlay click
document.querySelectorAll('.overlay').forEach(ov => {
  ov.addEventListener('click', e => {
    if (e.target === ov) {
      ov.classList.remove('show');
    }
  });
});
</script>
</body>
</html>
