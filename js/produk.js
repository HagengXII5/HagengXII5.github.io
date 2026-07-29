/**
 * Produk Page Script
 * Handles product listing, search, and cart interactions
 */

document.addEventListener('DOMContentLoaded', () => {
  // Load cart and update displays
  let cart = getCart();
  
  function updateCartDisplay() {
    const totalQty = getCartTotalQty();
    document.getElementById('cartLabel').textContent = 'Keranjang: ' + totalQty + ' item';
    updateCartBadge();
  }

  // Setup qty boxes dengan data binding
  document.querySelectorAll('.qty-box').forEach((box, index) => {
    const itemData = PRODUCTS[index];
    if (!itemData) return;
    
    const [minus, span, plus] = box.children;
    
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
