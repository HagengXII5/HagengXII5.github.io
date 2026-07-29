/**
 * Transaksi Page Script
 * Displays transaction history with filters
 */

document.addEventListener('DOMContentLoaded', () => {
  // Load transactions dari localStorage
  const transactions = getTransactions();
  
  function getStatusClass(status) {
    if (status === 'selesai') return 'status-selesai';
    if (status === 'proses') return 'status-proses';
    return 'status-batal';
  }
  
  function getStatusLabel(status) {
    if (status === 'selesai') return 'Selesai';
    if (status === 'proses') return 'Diproses';
    return 'Dibatalkan';
  }
  
  function renderTransactions(filter = 'semua') {
    const container = document.getElementById('trxList');
    container.innerHTML = '';
    
    const filtered = filter === 'semua' 
      ? transactions 
      : transactions.filter(t => t.status === filter);
    
    if (filtered.length === 0) {
      container.innerHTML = `
        <div style="text-align:center; padding:60px 20px; color:#999;">
          <div style="font-size:48px; margin-bottom:16px;">📋</div>
          <div style="font-weight:600; font-size:16px; margin-bottom:8px; color:#555;">Belum Ada Transaksi</div>
          <div style="font-size:13px; margin-bottom:20px;">Yuk mulai belanja dari warung Klik Madura!</div>
          <a href="/produk" style="display:inline-block; background:var(--red); color:#fff; padding:10px 20px; border-radius:10px; font-weight:700; font-size:14px;">Mulai Belanja</a>
        </div>
      `;
      return;
    }
    
    filtered.forEach(trx => {
      const card = document.createElement('div');
      card.className = 'trx-card';
      card.dataset.status = trx.status;
      card.innerHTML = `
        <div class="trx-top">
          <div>
            <div class="trx-id">${trx.orderNo}</div>
            <div class="trx-date">${formatDate(trx.date)}</div>
          </div>
          <span class="trx-status ${getStatusClass(trx.status)}">${getStatusLabel(trx.status)}</span>
        </div>
        <div class="trx-items">${trx.items}</div>
        <div class="trx-bottom">
          <span class="trx-store">${trx.store}</span>
          <span class="trx-total">Total <b>${formatMoney(trx.total)}</b></span>
        </div>
      `;
      container.appendChild(card);
    });
  }
  
  // Filter tabs
  document.querySelectorAll('.filter-tabs button').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.filter-tabs button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      renderTransactions(btn.dataset.f);
    });
  });
  
  // Initial render
  renderTransactions();
  
  // Update cart badge
  updateCartBadge();
});
