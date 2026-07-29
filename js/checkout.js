/**
 * Checkout Page Script
 * Handles checkout process, order creation, and payment
 */

document.addEventListener('DOMContentLoaded', () => {
  // Load cart dari localStorage
  let items = getCart();

  const ongkir = 5000;
  const layanan = 1000;
  const cartEl = document.getElementById('cartItems');

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

  // Cart item actions
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

  // Delivery toggle
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

  // Store pick
  document.querySelectorAll('.store-opt').forEach(opt => {
    opt.addEventListener('click', () => {
      document.querySelectorAll('.store-opt').forEach(o => o.classList.remove('active'));
      opt.classList.add('active');
    });
  });

  // Payment pick
  document.querySelectorAll('.pay-opt').forEach(opt => {
    opt.addEventListener('click', () => {
      document.querySelectorAll('.pay-opt').forEach(o => o.classList.remove('active'));
      opt.classList.add('active');
      opt.querySelector('input').checked = true;
    });
  });

  // Place order
  document.getElementById('btnPlace').addEventListener('click', () => {
    if (items.length === 0) return;
    
    // Calculate totals
    const subtotal = items.reduce((s, it) => s + it.price * it.qty, 0);
    const feeOngkir = optAmbil.classList.contains('active') ? 0 : ongkir;
    const total = subtotal + feeOngkir + layanan;
    
    // Get delivery info
    const deliveryMethod = optAntar.classList.contains('active') ? 'antar' : 'ambil';
    const selectedStore = deliveryMethod === 'ambil' 
      ? document.querySelector('.store-opt.active b')?.textContent || 'Warung Madura Margonda'
      : 'Warung Madura Margonda';
    
    // Create items text
    const itemsText = items.map(it => `${it.name} x${it.qty}`).join(', ');
    
    // Save transaction
    const transaction = addTransaction({
      items: itemsText,
      itemsData: items,
      store: selectedStore,
      total,
      deliveryMethod
    });
    
    // Update overlay dengan nomor order
    document.querySelector('.ordno').textContent = transaction.orderNo;
    
    // Clear cart
    clearCart();
    items = [];
    
    // Show success overlay
    document.getElementById('overlay').classList.add('show');
  });

  // Initial render
  render();
});
