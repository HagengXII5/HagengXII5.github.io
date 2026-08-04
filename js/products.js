/**
 * Products Data Module
 * Loads product data from JSON file
 */

let PRODUCTS = [];
let PRODUCTS_LOADED = false;

// Load products from JSON
async function loadProductsFromJSON() {
  try {
    // Check if we have products in localStorage (admin modifications)
    const storedProducts = localStorage.getItem('klikMaduraProducts');
    
    if (storedProducts) {
      // Load from localStorage
      const productsArray = JSON.parse(storedProducts);
      PRODUCTS = productsArray.map(p => ({
        id: p.id,
        emoji: p.emoji,
        name: p.name,
        desc: p.desc || p.description,
        price: p.price,
        category: p.category,
        inStock: p.inStock,
        tags: p.tags || []
      }));
      PRODUCTS_LOADED = true;
      return PRODUCTS;
    }
    
    // Otherwise load from JSON file
    const response = await fetch('../data/products.json');
    const data = await response.json();
    
    // Transform JSON format to internal format
    PRODUCTS = data.products.map(p => ({
      id: p.id,
      emoji: p.emoji,
      name: p.name,
      desc: p.description,
      price: p.price,
      category: `${p.categoryIcon} ${p.category}`,
      inStock: p.inStock,
      tags: p.tags
    }));
    
    PRODUCTS_LOADED = true;
    return PRODUCTS;
  } catch (error) {
    console.error('Failed to load products:', error);
    // Fallback to empty array if JSON fails to load
    PRODUCTS = [];
    PRODUCTS_LOADED = true;
    return PRODUCTS;
  }
}

// Load products from localStorage (for admin changes)
function loadProductsFromStorage() {
  const storedProducts = localStorage.getItem('klikMaduraProducts');
  if (storedProducts) {
    const productsArray = JSON.parse(storedProducts);
    PRODUCTS = productsArray.map(p => ({
      id: p.id,
      emoji: p.emoji,
      name: p.name,
      desc: p.desc || p.description,
      price: p.price,
      category: p.category,
      inStock: p.inStock,
      tags: p.tags || []
    }));
  }
  return PRODUCTS;
}

// Get product by ID
function getProductById(id) {
  return PRODUCTS.find(p => p.id === id);
}

// Get products by category
function getProductsByCategory(category) {
  if (category === 'Semua') return PRODUCTS;
  return PRODUCTS.filter(p => p.category === category);
}

// Search products
function searchProducts(query) {
  const q = query.toLowerCase();
  return PRODUCTS.filter(p => 
    p.name.toLowerCase().includes(q) || 
    p.desc.toLowerCase().includes(q) ||
    (p.tags && p.tags.some(tag => tag.includes(q)))
  );
}

// Get all categories
function getCategories() {
  const categories = ['Semua'];
  PRODUCTS.forEach(p => {
    if (!categories.includes(p.category)) {
      categories.push(p.category);
    }
  });
  return categories;
}

// Check if products are loaded
function isProductsLoaded() {
  return PRODUCTS_LOADED;
}

// Wait for products to load
function waitForProducts() {
  return new Promise((resolve) => {
    if (PRODUCTS_LOADED) {
      resolve(PRODUCTS);
    } else {
      const checkInterval = setInterval(() => {
        if (PRODUCTS_LOADED) {
          clearInterval(checkInterval);
          resolve(PRODUCTS);
        }
      }, 50);
    }
  });
}

// Auto-load products when module loads
loadProductsFromJSON();
