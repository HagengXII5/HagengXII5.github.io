let PRODUCTS = [];
let PRODUCTS_LOADED = false;

async function loadProductsFromJSON() {
  try {
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
      PRODUCTS_LOADED = true;
      return PRODUCTS;
    }
    
    const response = await fetch('../data/products.json');
    const data = await response.json();
    
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
    PRODUCTS = [];
    PRODUCTS_LOADED = true;
    return PRODUCTS;
  }
}

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

function getProductById(id) {
  return PRODUCTS.find(p => p.id === id);
}

function getProductsByCategory(category) {
  if (category === 'Semua') return PRODUCTS;
  return PRODUCTS.filter(p => p.category === category);
}

function searchProducts(query) {
  const q = query.toLowerCase();
  return PRODUCTS.filter(p => 
    p.name.toLowerCase().includes(q) || 
    p.desc.toLowerCase().includes(q) ||
    (p.tags && p.tags.some(tag => tag.includes(q)))
  );
}

function getCategories() {
  const categories = ['Semua'];
  PRODUCTS.forEach(p => {
    if (!categories.includes(p.category)) {
      categories.push(p.category);
    }
  });
  return categories;
}

function isProductsLoaded() {
  return PRODUCTS_LOADED;
}

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

loadProductsFromJSON();
