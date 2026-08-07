document.addEventListener('DOMContentLoaded', async () => {
  await waitForProducts();

  let cart = getCart();


  function updateCartDisplay() {
    const totalQty = getCartTotalQty();
    document.getElementById('cartLabel').textContent = 'Keranjang: ' + totalQty + ' item';
    updateCartBadge();
  }

  function buildCard(product) {
    const cartItem = cart.find(i => i.id === product.id);
    const qty = cartItem ? cartItem.qty : 0;
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
          <span>${qty}</span>
          <button class="btn-plus" ${outOfStock ? 'disabled' : ''}>+</button>
        </div>
      </div>
    `;

    const span   = card.querySelector('.qty-box span');
    const minus  = card.querySelector('.btn-minus');
    const plus   = card.querySelector('.btn-plus');

    minus.addEventListener('click', () => {
      const current = parseInt(span.textContent);
      if (current > 0) {
        span.textContent = current - 1;
        updateCartItem(product.id, -1);
        cart = getCart();
        updateCartDisplay();
      }
    });

    plus.addEventListener('click', () => {
      const current = parseInt(span.textContent);
      span.textContent = current + 1;
      if (current === 0) {
        addToCart(product, 1);
      } else {
        updateCartItem(product.id, 1);
      }
      cart = getCart();
      updateCartDisplay();
    });

    return card;
  }


  function renderProducts(productsToShow) {
    const blocksContainer = document.getElementById('productBlocks');
    blocksContainer.innerHTML = '';

    if (productsToShow.length === 0) {
      blocksContainer.innerHTML = `
        <div style="text-align:center; padding:60px 20px; color:#999;">
          <div style="font-size:48px; margin-bottom:12px;">🔍</div>
          <div style="font-weight:600; font-size:16px; color:#555;">Produk tidak ditemukan</div>
        </div>`;
      return;
    }

    const grouped = {};
    productsToShow.forEach(p => {
      if (!grouped[p.category]) grouped[p.category] = [];
      grouped[p.category].push(p);
    });

    Object.entries(grouped).forEach(([category, products]) => {
      const block = document.createElement('div');
      block.className = 'cat-block';
      block.dataset.category = category;

      const grid = document.createElement('div');
      grid.className = 'item-grid';
      products.forEach(p => grid.appendChild(buildCard(p)));

      block.innerHTML = `<h2>${category}</h2>`;
      block.appendChild(grid);
      blocksContainer.appendChild(block);
    });
  }


  function renderCategoryTabs() {
    const catTabs = document.getElementById('catTabs');
    catTabs.innerHTML = '';

    const categories = getCategories();

    categories.forEach((cat, i) => {
      const btn = document.createElement('button');
      btn.textContent = cat;
      btn.dataset.cat = cat;
      if (i === 0) btn.classList.add('active');

      btn.addEventListener('click', () => {
        catTabs.querySelectorAll('button').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const searchInput = document.getElementById('searchInput');
        if (searchInput) searchInput.value = '';

        if (cat === 'Semua') {
          renderProducts(PRODUCTS);
        } else {
          renderProducts(PRODUCTS.filter(p => p.category === cat));
        }
      });

      catTabs.appendChild(btn);
    });
  }


  function setupSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    searchInput.addEventListener('input', (e) => {
      const query = e.target.value.trim().toLowerCase();

      document.querySelectorAll('#catTabs button').forEach(b => b.classList.remove('active'));
      const semua = document.querySelector('#catTabs button[data-cat="Semua"]');
      if (semua) semua.classList.add('active');

      if (query === '') {
        renderProducts(PRODUCTS);
        return;
      }

      const results = PRODUCTS.filter(p =>
        p.name.toLowerCase().includes(query) ||
        (p.desc && p.desc.toLowerCase().includes(query)) ||
        (p.tags && p.tags.some(tag => tag.toLowerCase().includes(query)))
      );

      renderProducts(results);
    });
  }


  renderCategoryTabs();
  renderProducts(PRODUCTS);
  setupSearch();
  updateCartDisplay();
});
