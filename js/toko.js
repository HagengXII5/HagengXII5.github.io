function renderTokoList() {
  const storeList = document.getElementById('storeList');
  const stores = getStores();

  storeList.innerHTML = '';

  if (stores.length === 0) {
    storeList.innerHTML = '<p style="padding:20px; color:#999;">Belum ada toko terdaftar.</p>';
    return;
  }

  stores.forEach((store, i) => {
    const statusLabel = store.status === 'open' ? 'Buka' : 'Tutup';
    const statusClass = store.status === 'open' ? 'store-status' : 'store-status store-status--closed';

    const card = document.createElement('div');
    card.className = 'store-card' + (i === 0 ? ' active' : '');
    card.dataset.name  = store.name;
    card.dataset.addr  = store.address;
    card.dataset.jarak = store.distance;
    card.dataset.jam   = store.hours;
    card.dataset.hp    = store.phone;

    card.innerHTML = `
      <div class="store-top">
        <span class="store-name">${store.name}</span>
        <span class="${statusClass}">${statusLabel}</span>
      </div>
      <div class="store-addr">${store.address}</div>
      <div class="store-meta">
        <span>📍 <b>${store.distance}</b></span>
        <span>🕛 <b>${store.hours}</b></span>
      </div>
    `;

    card.addEventListener('click', () => {
      document.querySelectorAll('.store-card').forEach(c => c.classList.remove('active'));
      card.classList.add('active');

      document.getElementById('dName').textContent  = store.name;
      document.getElementById('dAddr').textContent  = store.address;
      document.getElementById('dJarak').textContent = store.distance;
      document.getElementById('dJam').textContent   = store.hours;
      document.getElementById('dHp').textContent    = store.phone;
    });

    storeList.appendChild(card);
  });

  const first = stores[0];
  document.getElementById('dName').textContent  = first.name;
  document.getElementById('dAddr').textContent  = first.address;
  document.getElementById('dJarak').textContent = first.distance;
  document.getElementById('dJam').textContent   = first.hours;
  document.getElementById('dHp').textContent    = first.phone;
}

document.addEventListener('DOMContentLoaded', () => {
  renderTokoList();

  updateCartBadge();
});
