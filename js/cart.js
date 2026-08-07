function getCart() {
  return JSON.parse(localStorage.getItem('klikMaduraCart') || '[]');
}

function saveCart(cart) {
  localStorage.setItem('klikMaduraCart', JSON.stringify(cart));
}

function getCartTotalQty() {
  const cart = getCart();
  return cart.reduce((sum, item) => sum + item.qty, 0);
}

function updateCartItem(itemId, delta) {
  let cart = getCart();
  const existing = cart.find(i => i.id === itemId);
  
  if (existing) {
    existing.qty += delta;
    if (existing.qty <= 0) {
      cart = cart.filter(i => i.id !== itemId);
    }
  } else if (delta > 0) {
    console.error('Cannot add new item without data. Use addToCart() instead.');
  }
  
  saveCart(cart);
  return cart;
}

function addToCart(itemData, qty = 1) {
  if (!itemData.inStock) {
    console.warn('Cannot add out-of-stock product:', itemData.name);
    return null;
  }
  
  let cart = getCart();
  const existing = cart.find(i => i.id === itemData.id);
  
  if (existing) {
    existing.qty += qty;
  } else {
    cart.push({ ...itemData, qty });
  }
  
  saveCart(cart);
  return cart;
}

function removeFromCart(itemId) {
  let cart = getCart();
  cart = cart.filter(i => i.id !== itemId);
  saveCart(cart);
  return cart;
}

function clearCart() {
  localStorage.removeItem('klikMaduraCart');
}

function updateCartBadge() {
  const totalQty = getCartTotalQty();
  const badge = document.getElementById('cartCount');
  
  if (badge) {
    badge.textContent = totalQty;
    badge.classList.toggle('show', totalQty > 0);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  updateCartBadge();
});
