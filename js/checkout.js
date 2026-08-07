document.addEventListener('DOMContentLoaded', () => {
  let items = getCart();
  
  waitForProducts().then(() => {
    validateCartStock();
  });

  const ongkir = 5000;
  const layanan = 1000;
  const cartEl = document.getElementById('cartItems');

  function validateCartStock() {
    const originalLength = items.length;
    items = items.filter(item => {
      const product = getProductById(item.id);
      if (product && !product.inStock) {
        console.warn('Removed out-of-stock item from cart:', item.name);
        return false;
      }
      return true;
    });
    
    if (items.length < originalLength) {
      saveCart(items);
      showStockWarning();
    }
    
    render();
  }
  
  function showStockWarning() {
    const warningDiv = document.createElement('div');
    warningDiv.style.cssText = 'background:#fff3cd; border:1px solid #ffc107; color:#856404; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13px;';
    warningDiv.innerHTML = '⚠️ Beberapa produk di keranjang Anda sudah habis dan telah dihapus otomatis.';
    
    const pageHead = document.querySelector('.page-head');
    pageHead.parentElement.insertBefore(warningDiv, pageHead.nextSibling);
    
    setTimeout(() => warningDiv.remove(), 5000);
  }

  function render() {
    const container = cartEl;
    
    if (items.length === 0) {
      container.innerHTML = '<div style="text-align:center; padding:40px 20px; color:#999;"><div style="font-size:48px; margin-bottom:12px;">🛒</div><div style="font-weight:600; margin-bottom:6px;">Keranjang Kosong</div><div style="font-size:13px;"><a href="/produk" style="color:var(--red);">Mulai belanja yuk!</a></div></div>';
      updateSummary();
      return;
    }
    
    container.innerHTML = '';
    items.forEach((it, i) => {
      const row = document.createElement('div');
      row.className = 'cart-item';
      row.innerHTML = `
        <div class="emoji">${it.emoji}</div>
        <div class="info">
          <div class="nm">${it.name}</div>
          <div class="pr">${formatMoney(it.price)} / item</div>
        </div>
        <div class="qty-box">
          <button data-act="minus" data-i="${i}">−</button>
          <span>${it.qty}</span>
          <button data-act="plus" data-i="${i}">+</button>
        </div>
        <div class="line-total">${formatMoney(it.price * it.qty)}</div>
        <button class="remove-x" data-act="remove" data-i="${i}">✕</button>
      `;
      container.appendChild(row);
    });
    updateSummary();
  }

  function updateSummary() {
    const subtotal = items.reduce((s, it) => s + it.price * it.qty, 0);
    const feeOngkir = document.getElementById('optAmbil').classList.contains('active') ? 0 : ongkir;
    document.getElementById('sSubtotal').textContent = formatMoney(subtotal);
    document.getElementById('sOngkir').textContent = feeOngkir === 0 ? 'Gratis' : formatMoney(feeOngkir);
    document.getElementById('sLayanan').textContent = formatMoney(layanan);
    document.getElementById('sTotal').textContent = formatMoney(subtotal + feeOngkir + layanan);
    
    const btn = document.getElementById('btnPlace');
    btn.disabled = items.length === 0;
  }

  cartEl.addEventListener('click', e => {
    const btn = e.target.closest('button');
    if (!btn) return;
    const i = +btn.dataset.i;
    
    if (btn.dataset.act === 'plus') items[i].qty++;
    if (btn.dataset.act === 'minus') { 
      items[i].qty--; 
      if (items[i].qty <= 0) items.splice(i, 1); 
    }
    if (btn.dataset.act === 'remove') items.splice(i, 1);
    
    saveCart(items);
    render();
  });

  const optAntar = document.getElementById('optAntar');
  const optAmbil = document.getElementById('optAmbil');
  const antarBlock = document.getElementById('antarBlock');
  const ambilBlock = document.getElementById('ambilBlock');
  
  optAntar.addEventListener('click', () => {
    optAntar.classList.add('active');
    optAmbil.classList.remove('active');
    antarBlock.style.display = '';
    ambilBlock.style.display = 'none';
    updateSummary();
  });
  
  optAmbil.addEventListener('click', () => {
    optAmbil.classList.add('active');
    optAntar.classList.remove('active');
    antarBlock.style.display = 'none';
    ambilBlock.style.display = '';
    updateSummary();
  });

  function renderStorePick() {
    const storePick = document.getElementById('storePick');
    const stores = getStores().filter(s => s.status === 'open');

    storePick.innerHTML = '';

    if (stores.length === 0) {
      storePick.innerHTML = '<p style="font-size:13px; color:#999; padding:8px 0;">Tidak ada toko yang tersedia saat ini.</p>';
      return;
    }

    stores.forEach((store, i) => {
      const opt = document.createElement('div');
      opt.className = 'store-opt' + (i === 0 ? ' active' : '');
      opt.dataset.storeId = store.id;
      opt.dataset.fee = '0';
      opt.innerHTML = `<b>${store.name}</b><span>${store.distance} · ${store.hours}</span>`;
      opt.addEventListener('click', () => {
        storePick.querySelectorAll('.store-opt').forEach(o => o.classList.remove('active'));
        opt.classList.add('active');
      });
      storePick.appendChild(opt);
    });
  }

  renderStorePick();

  document.querySelectorAll('.pay-opt').forEach(opt => {
    opt.addEventListener('click', () => {
      document.querySelectorAll('.pay-opt').forEach(o => o.classList.remove('active'));
      opt.classList.add('active');
      opt.querySelector('input').checked = true;
    });
  });

  document.getElementById('btnPlace').addEventListener('click', () => {
    if (items.length === 0) return;
    
    const hasOutOfStock = items.some(item => {
      const product = getProductById(item.id);
      return product && !product.inStock;
    });
    
    if (hasOutOfStock) {
      alert('⚠️ Maaf, ada produk yang sudah habis. Silakan refresh halaman untuk memperbarui keranjang.');
      validateCartStock();
      return;
    }
    
    const subtotal = items.reduce((s, it) => s + it.price * it.qty, 0);
    const feeOngkir = optAmbil.classList.contains('active') ? 0 : ongkir;
    const total = subtotal + feeOngkir + layanan;
    
    const deliveryMethod = optAntar.classList.contains('active') ? 'antar' : 'ambil';
    let selectedStore = 'Warung Madura Margonda';
    if (deliveryMethod === 'ambil') {
      const activeStoreOpt = document.querySelector('#storePick .store-opt.active');
      if (activeStoreOpt) {
        const storeId = activeStoreOpt.dataset.storeId;
        const storeData = storeId ? getStoreById(storeId) : null;
        selectedStore = storeData ? storeData.name : (activeStoreOpt.querySelector('b')?.textContent || selectedStore);
      }
    } else {
      const firstStore = getStores().find(s => s.status === 'open');
      if (firstStore) selectedStore = firstStore.name;
    }
    
    const itemsText = items.map(it => `${it.name} x${it.qty}`).join(', ');
    
    const transaction = addTransaction({
      items: itemsText,
      itemsData: items,
      store: selectedStore,
      total,
      deliveryMethod
    });
    
    document.querySelector('.ordno').textContent = transaction.orderNo;
    
    clearCart();
    items = [];
    
    document.getElementById('overlay').classList.add('show');
  });

  render();
});
