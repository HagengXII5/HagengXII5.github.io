/**
 * Admin Dashboard Script
 * Handles product and transaction management
 */

// Require admin access
if (!requireAdmin()) {
  // Will redirect automatically
}

let currentEditingProductId = null;

document.addEventListener('DOMContentLoaded', async () => {
  // Wait for products to load
  await waitForProducts();
  
  // Load stores
  loadStores();
  
  // Render everything
  updateStats();
  renderProducts();
  renderStores();
  renderTransactions();
});

// Switch tabs
function switchTab(tab) {
  // Update tab buttons
  document.querySelectorAll('.tabs button').forEach(btn => btn.classList.remove('active'));
  event.target.classList.add('active');
  
  // Update tab content
  document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
  document.getElementById('tab-' + tab).classList.add('active');
}

// Update statistics
function updateStats() {
  const products = PRODUCTS;
  const transactions = getTransactions();
  const stores = getStores();
  
  const inStockCount = products.filter(p => p.inStock).length;
  const totalRevenue = transactions
    .filter(t => t.status === 'selesai')
    .reduce((sum, t) => sum + t.total, 0);
  
  document.getElementById('statProducts').textContent = products.length;
  document.getElementById('statStores').textContent = stores.length;
  document.getElementById('statTransactions').textContent = transactions.length;
  document.getElementById('statRevenue').textContent = formatMoney(totalRevenue);
}

// ===== PRODUCT MANAGEMENT =====

function renderProducts() {
  const tbody = document.getElementById('productTableBody');
  tbody.innerHTML = '';
  
  if (PRODUCTS.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:40px; color:#999;">Belum ada produk.</td></tr>';
    return;
  }
  
  PRODUCTS.forEach(product => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>
        <div style="display:flex; align-items:center; gap:12px;">
          <span class="product-emoji">${product.emoji}</span>
          <div>
            <div class="product-name">${product.name}</div>
            <div class="product-desc">${product.desc || ''}</div>
          </div>
        </div>
      </td>
      <td>${product.category}</td>
      <td class="product-price">${formatMoney(product.price)}</td>
      <td>
        <span class="badge ${product.inStock ? 'badge-success' : 'badge-danger'}">
          ${product.inStock ? 'Tersedia' : 'Habis'}
        </span>
      </td>
      <td class="product-actions">
        <button class="btn btn-sm btn-outline" onclick="toggleStock('${product.id}')">
          ${product.inStock ? '❌ Habiskan' : '✅ Sediakan'}
        </button>
        <button class="btn btn-sm btn-outline" onclick="openEditProductModal('${product.id}')">✏️ Edit</button>
        <button class="btn btn-sm btn-danger" onclick="deleteProduct('${product.id}')">🗑️ Hapus</button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

function openAddProductModal() {
  currentEditingProductId = null;
  document.getElementById('modalTitle').textContent = 'Tambah Produk';
  document.getElementById('productForm').reset();
  document.getElementById('productId').value = '';
  document.getElementById('productInStock').checked = true;
  document.getElementById('modalAlert').innerHTML = '';
  document.getElementById('productModal').classList.add('show');
}

function openEditProductModal(productId) {
  currentEditingProductId = productId;
  const product = PRODUCTS.find(p => p.id === productId);
  
  if (!product) return;
  
  document.getElementById('modalTitle').textContent = 'Edit Produk';
  document.getElementById('productId').value = product.id;
  document.getElementById('productEmoji').value = product.emoji;
  document.getElementById('productName').value = product.name;
  document.getElementById('productDesc').value = product.desc || '';
  document.getElementById('productPrice').value = product.price;
  
  // Extract category name without emoji
  const categoryName = product.category.replace(/^[^\s]+\s/, '');
  document.getElementById('productCategory').value = categoryName;
  document.getElementById('productInStock').checked = product.inStock;
  document.getElementById('modalAlert').innerHTML = '';
  document.getElementById('productModal').classList.add('show');
}

function closeProductModal() {
  document.getElementById('productModal').classList.remove('show');
  currentEditingProductId = null;
}

function saveProduct() {
  const id = currentEditingProductId || generateProductId();
  const emoji = document.getElementById('productEmoji').value.trim();
  const name = document.getElementById('productName').value.trim();
  const desc = document.getElementById('productDesc').value.trim();
  const price = parseInt(document.getElementById('productPrice').value);
  const category = document.getElementById('productCategory').value;
  const inStock = document.getElementById('productInStock').checked;
  
  const modalAlert = document.getElementById('modalAlert');
  
  // Validation
  if (!emoji || !name || !price || !category) {
    modalAlert.innerHTML = '<div class="alert alert-error">Semua field wajib harus diisi.</div>';
    return;
  }
  
  if (price < 0) {
    modalAlert.innerHTML = '<div class="alert alert-error">Harga harus lebih dari 0.</div>';
    return;
  }
  
  // Get category emoji
  const categoryEmojis = {
    'Kopi & Minuman Hangat': '☕',
    'Mie Instan': '🍜',
    'Minuman': '🥤',
    'Snack & Makanan': '🍿',
    'Rumah Tangga': '🧻',
    'Pulsa & Token': '📶'
  };
  
  const categoryIcon = categoryEmojis[category];
  const fullCategory = `${categoryIcon} ${category}`;
  
  const productData = {
    id,
    emoji,
    name,
    desc,
    price,
    category: fullCategory,
    inStock,
    tags: name.toLowerCase().split(' ')
  };
  
  if (currentEditingProductId) {
    // Update existing product
    const index = PRODUCTS.findIndex(p => p.id === currentEditingProductId);
    if (index !== -1) {
      PRODUCTS[index] = productData;
    }
  } else {
    // Add new product
    PRODUCTS.push(productData);
  }
  
  // Save to localStorage (simulating JSON update)
  saveProductsToStorage();
  
  // Close modal and refresh
  closeProductModal();
  renderProducts();
  updateStats();
  
  const alertBox = document.getElementById('alertProducts');
  alertBox.innerHTML = `<div class="alert alert-success">Produk berhasil ${currentEditingProductId ? 'diupdate' : 'ditambahkan'}!</div>`;
  setTimeout(() => alertBox.innerHTML = '', 3000);
}

function toggleStock(productId) {
  const product = PRODUCTS.find(p => p.id === productId);
  if (!product) return;
  
  product.inStock = !product.inStock;
  saveProductsToStorage();
  renderProducts();
  updateStats();
  
  const alertBox = document.getElementById('alertProducts');
  alertBox.innerHTML = `<div class="alert alert-success">Status stok "${product.name}" berhasil diubah!</div>`;
  setTimeout(() => alertBox.innerHTML = '', 3000);
}

function deleteProduct(productId) {
  const product = PRODUCTS.find(p => p.id === productId);
  if (!product) return;
  
  if (!confirm(`Hapus produk "${product.name}"?\n\nProduk yang sudah dihapus tidak bisa dikembalikan.`)) {
    return;
  }
  
  const index = PRODUCTS.findIndex(p => p.id === productId);
  if (index !== -1) {
    PRODUCTS.splice(index, 1);
    saveProductsToStorage();
    renderProducts();
    updateStats();
    
    const alertBox = document.getElementById('alertProducts');
    alertBox.innerHTML = `<div class="alert alert-success">Produk "${product.name}" berhasil dihapus!</div>`;
    setTimeout(() => alertBox.innerHTML = '', 3000);
  }
}

function generateProductId() {
  const timestamp = Date.now();
  const random = Math.floor(Math.random() * 1000);
  return `product-${timestamp}-${random}`;
}

function saveProductsToStorage() {
  // Save products to localStorage (simulating JSON file update)
  localStorage.setItem('klikMaduraProducts', JSON.stringify(PRODUCTS));
  
  // Also reload products from storage for other pages
  loadProductsFromStorage();
}

// ===== TRANSACTION MANAGEMENT =====

function renderTransactions() {
  const tbody = document.getElementById('transactionTableBody');
  const transactions = getTransactions();
  tbody.innerHTML = '';
  
  if (transactions.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center; padding:40px; color:#999;">Belum ada transaksi.</td></tr>';
    return;
  }
  
  transactions.forEach(trx => {
    const row = document.createElement('tr');
    
    const statusClass = trx.status === 'selesai' ? 'badge-success' : 
                       trx.status === 'proses' ? 'badge badge-warning' : 
                       'badge-danger';
    const statusLabel = trx.status === 'selesai' ? 'Selesai' : 
                       trx.status === 'proses' ? 'Proses' : 
                       'Batal';
    
    row.innerHTML = `
      <td><div class="trx-id">${trx.orderNo}</div></td>
      <td><div class="trx-user">${trx.userName || 'Guest'}</div></td>
      <td style="max-width:300px; font-size:12px;">${trx.items}</td>
      <td style="font-weight:800; color:var(--green);">${formatMoney(trx.total)}</td>
      <td><span class="badge ${statusClass}">${statusLabel}</span></td>
      <td style="font-size:12px; color:#666;">${formatDate(trx.date)}</td>
      <td>
        <button class="btn btn-sm btn-danger" onclick="deleteTransaction('${trx.orderNo}')">🗑️ Hapus</button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

function deleteTransaction(orderNo) {
  if (!confirm(`Hapus transaksi ${orderNo}?\n\nTransaksi yang sudah dihapus tidak bisa dikembalikan.`)) {
    return;
  }
  
  let transactions = getTransactions();
  transactions = transactions.filter(t => t.orderNo !== orderNo);
  saveTransactions(transactions);
  
  renderTransactions();
  updateStats();
  
  const alertBox = document.getElementById('alertTransactions');
  alertBox.innerHTML = `<div class="alert alert-success">Transaksi ${orderNo} berhasil dihapus!</div>`;
  setTimeout(() => alertBox.innerHTML = '', 3000);
}

// Close modal when clicking outside
document.getElementById('productModal').addEventListener('click', (e) => {
  if (e.target.id === 'productModal') {
    closeProductModal();
  }
});

document.getElementById('storeModal').addEventListener('click', (e) => {
  if (e.target.id === 'storeModal') {
    closeStoreModal();
  }
});

// ===== STORE MANAGEMENT =====

let currentEditingStoreId = null;

function renderStores() {
  const tbody = document.getElementById('storeTableBody');
  const stores = getStores();
  tbody.innerHTML = '';
  
  if (stores.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center; padding:40px; color:#999;">Belum ada toko.</td></tr>';
    return;
  }
  
  stores.forEach(store => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>
        <div style="font-weight:700;">${store.name}</div>
        <div style="font-size:12px; color:#888; margin-top:2px;">${store.hours}</div>
      </td>
      <td style="max-width:300px; font-size:13px;">${store.address}</td>
      <td>
        <div style="font-size:12px;">📍 ${store.distance}</div>
      </td>
      <td style="font-size:13px;">${store.phone || '-'}</td>
      <td>
        <span class="badge ${store.status === 'open' ? 'badge-success' : 'badge-danger'}">
          ${store.status === 'open' ? 'Buka' : 'Tutup'}
        </span>
      </td>
      <td class="product-actions">
        <button class="btn btn-sm btn-outline" onclick="toggleStoreStatusBtn('${store.id}')">
          ${store.status === 'open' ? '🚫 Tutup' : '✅ Buka'}
        </button>
        <button class="btn btn-sm btn-outline" onclick="openEditStoreModal('${store.id}')">✏️ Edit</button>
        <button class="btn btn-sm btn-danger" onclick="deleteStoreBtn('${store.id}')">🗑️ Hapus</button>
      </td>
    `;
    tbody.appendChild(row);
  });
}

function openAddStoreModal() {
  currentEditingStoreId = null;
  document.getElementById('storeModalTitle').textContent = 'Tambah Toko';
  document.getElementById('storeForm').reset();
  document.getElementById('storeId').value = '';
  document.getElementById('storeStatus').checked = true;
  document.getElementById('storeModalAlert').innerHTML = '';
  document.getElementById('storeModal').classList.add('show');
}

function openEditStoreModal(storeId) {
  currentEditingStoreId = storeId;
  const store = getStoreById(storeId);
  
  if (!store) return;
  
  document.getElementById('storeModalTitle').textContent = 'Edit Toko';
  document.getElementById('storeId').value = store.id;
  document.getElementById('storeName').value = store.name;
  document.getElementById('storeAddress').value = store.address;
  document.getElementById('storeDistance').value = store.distance;
  document.getElementById('storeHours').value = store.hours;
  document.getElementById('storePhone').value = store.phone;
  document.getElementById('storeStatus').checked = store.status === 'open';
  document.getElementById('storeModalAlert').innerHTML = '';
  document.getElementById('storeModal').classList.add('show');
}

function closeStoreModal() {
  document.getElementById('storeModal').classList.remove('show');
  currentEditingStoreId = null;
}

function saveStore() {
  const name = document.getElementById('storeName').value.trim();
  const address = document.getElementById('storeAddress').value.trim();
  const distance = document.getElementById('storeDistance').value.trim();
  const hours = document.getElementById('storeHours').value.trim();
  const phone = document.getElementById('storePhone').value.trim();
  const status = document.getElementById('storeStatus').checked ? 'open' : 'closed';
  
  const modalAlert = document.getElementById('storeModalAlert');
  
  // Validation
  if (!name || !address) {
    modalAlert.innerHTML = '<div class="alert alert-error">Nama toko dan alamat harus diisi.</div>';
    return;
  }
  
  const storeData = {
    name,
    address,
    distance: distance || '-',
    hours: hours || '24 Jam',
    phone,
    status
  };
  
  if (currentEditingStoreId) {
    // Update existing store
    updateStore(currentEditingStoreId, storeData);
  } else {
    // Add new store
    addStore(storeData);
  }
  
  // Close modal and refresh
  closeStoreModal();
  renderStores();
  updateStats();
  
  const alertBox = document.getElementById('alertStores');
  alertBox.innerHTML = `<div class="alert alert-success">Toko berhasil ${currentEditingStoreId ? 'diupdate' : 'ditambahkan'}!</div>`;
  setTimeout(() => alertBox.innerHTML = '', 3000);
}

function toggleStoreStatusBtn(storeId) {
  const store = toggleStoreStatus(storeId);
  
  if (store) {
    renderStores();
    updateStats();
    
    const alertBox = document.getElementById('alertStores');
    alertBox.innerHTML = `<div class="alert alert-success">Status toko "${store.name}" berhasil diubah!</div>`;
    setTimeout(() => alertBox.innerHTML = '', 3000);
  }
}

function deleteStoreBtn(storeId) {
  const store = getStoreById(storeId);
  if (!store) return;
  
  if (!confirm(`Hapus toko "${store.name}"?\n\nToko yang sudah dihapus tidak bisa dikembalikan.`)) {
    return;
  }
  
  deleteStore(storeId);
  renderStores();
  updateStats();
  
  const alertBox = document.getElementById('alertStores');
  alertBox.innerHTML = `<div class="alert alert-success">Toko "${store.name}" berhasil dihapus!</div>`;
  setTimeout(() => alertBox.innerHTML = '', 3000);
}