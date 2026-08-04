/**
 * Produk Page Script
 * Handles product listing, search, and cart interactions
 */

document.addEventListener('DOMContentLoaded', async () => {
  // Wait for products to load from JSON
  await waitForProducts();
  
  // Load cart and update displays
  let cart = getCart();
  
  function updateCartDisplay() {
    const totalQty = getCartTotalQty();
    document.getElementById('cartLabel').textContent = 'Keranjang: ' + totalQty + ' item';
    updateCartBadge();
  }

  // Map product IDs to HTML elements by reading data from HTML
  const productMap = {};
  document.querySelectorAll('.item-card').forEach(card => {
    const name = card.querySelector('.item-name').textContent;
    const product = PRODUCTS.find(p => p.name === name);
    if (product) {
      productMap[product.id] = card;
    }
  });

  // Setup qty boxes with data binding
  document.querySelectorAll('.qty-box').forEach((box) => {
    const card = box.closest('.item-card');
    const name = card.querySelector('.item-name').textContent;
    const itemData = PRODUCTS.find(p => p.name === name);
    
    if (!itemData) {
      console.warn('Product not found in JSON:', name);
      return;
    }
    
    // Add out-of-stock styling if needed
    if (!itemData.inStock) {
      card.classList.add('out-of-stock');
      
      // Add stock badge
      const badge = document.createElement('div');
      badge.className = 'stock-badge';
      badge.textContent = 'HABIS';
      card.appendChild(badge);
    }
    
    const [minus, span, plus] = box.children;
    
    // Disable buttons if out of stock
    if (!itemData.inStock) {
      minus.disabled = true;
      plus.disabled = true;
      span.textContent = 0;
      box.dataset.itemId = itemData.id;
      return;
    }
    
    // Load initial qty dari cart
    const cartItem = cart.find(i => i.id === itemData.id);
    const initialQty = cartItem ? cartItem.qty : 0;
    span.textContent = initialQty;

    // Set data-id untuk tracking
    box.dataset.itemId = itemData.id;

    minus.addEventListener('click', () => {
      const current = parseInt(span.textContent);
      if (current > 0) {
        span.textContent = current - 1;
        updateCartItem(itemData.id, -1);
        cart = getCart();
        updateCartDisplay();
      }
    });

    plus.addEventListener('click', () => {
      const current = parseInt(span.textContent);
      span.textContent = current + 1;
      
      if (current === 0) {
        addToCart(itemData, 1);
      } else {
        updateCartItem(itemData.id, 1);
      }
      
      cart = getCart();
      updateCartDisplay();
    });
  });

  // Category tabs filter
  document.querySelectorAll('.cat-tabs button').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.cat-tabs button').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      
      const catText = btn.textContent.trim();
      document.querySelectorAll('.cat-block').forEach(block => {
        if (catText === 'Semua') {
          block.style.display = '';
        } else {
          const heading = block.querySelector('h2').textContent.trim();
          block.style.display = heading === catText ? '' : 'none';
        }
      });
    });
  });

  // Search functionality
  const searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      const query = e.target.value.toLowerCase();
      document.querySelectorAll('.cat-block').forEach(block => {
        let hasMatch = false;
        block.querySelectorAll('.item-card').forEach(card => {
          const name = card.querySelector('.item-name').textContent.toLowerCase();
          const desc = card.querySelector('.item-desc').textContent.toLowerCase();
          const match = name.includes(query) || desc.includes(query);
          card.style.display = match ? '' : 'none';
          if (match) hasMatch = true;
        });
        block.style.display = hasMatch || query === '' ? '' : 'none';
      });
    });
  }

  // Initial update
  updateCartDisplay();
});
