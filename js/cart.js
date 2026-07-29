/**
 * Cart Management Module
 * Handles localStorage cart operations
 */

// Load cart dari localStorage
function getCart() {
  return JSON.parse(localStorage.getItem('klikMaduraCart') || '[]');
}

// Save cart ke localStorage
function saveCart(cart) {
  localStorage.setItem('klikMaduraCart', JSON.stringify(cart));
}

// Get total quantity in cart
function getCartTotalQty() {
  const cart = getCart();
  return cart.reduce((sum, item) => sum + item.qty, 0);
}

// Add or update item in cart
function updateCartItem(itemId, delta) {
  let cart = getCart();
  const existing = cart.find(i => i.id === itemId);
  
  if (existing) {
    existing.qty += delta;
    if (existing.qty <= 0) {
      cart = cart.filter(i => i.id !== itemId);
    }
  } else if (delta > 0) {
    // Item should be added by caller with full data
    console.error('Cannot add new item without data. Use addToCart() instead.');
  }
  
  saveCart(cart);
  return cart;
}

// Add item to cart with full data
function addToCart(itemData, qty = 1) {
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

// Remove item from cart
function removeFromCart(itemId) {
  let cart = getCart();
  cart = cart.filter(i => i.id !== itemId);
  saveCart(cart);
  return cart;
}

// Clear entire cart
function clearCart() {
  localStorage.removeItem('klikMaduraCart');
}

// Update cart badge in header
function updateCartBadge() {
  const totalQty = getCartTotalQty();
  const badge = document.getElementById('cartCount');
  
  if (badge) {
    badge.textContent = totalQty;
    badge.classList.toggle('show', totalQty > 0);
  }
}

// Initialize cart badge on page load
document.addEventListener('DOMContentLoaded', () => {
  updateCartBadge();
});
