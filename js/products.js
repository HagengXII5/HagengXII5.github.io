/**
 * Products Data Module
 * Contains all product information
 */

const PRODUCTS = [
  // Kopi & Rokok
  { id: 'kopi-robusta', emoji: '☕', name: 'Kopi Sachet Robusta', desc: '1 renceng isi 10', price: 12000, category: '☕ Kopi & Rokok' },
  { id: 'rokok-filter', emoji: '🚬', name: 'Rokok Filter (bungkus)', desc: 'Isi 16 batang', price: 28000, category: '☕ Kopi & Rokok' },
  { id: 'teh-celup', emoji: '🍵', name: 'Teh Celup', desc: 'Kotak isi 25', price: 9500, category: '☕ Kopi & Rokok' },
  
  // Mie Instan
  { id: 'mie-goreng', emoji: '🍜', name: 'Mie Goreng', desc: '1 bungkus', price: 3500, category: '🍜 Mie Instan' },
  { id: 'mie-kuah', emoji: '🍲', name: 'Mie Kuah Ayam Bawang', desc: '1 bungkus', price: 3500, category: '🍜 Mie Instan' },
  { id: 'mie-cup', emoji: '🥡', name: 'Mie Instan Cup', desc: '1 cup', price: 7000, category: '🍜 Mie Instan' },
  
  // Minuman
  { id: 'air-mineral', emoji: '🥤', name: 'Air Mineral Botol 600ml', desc: 'Dingin dari kulkas', price: 4000, category: '🥤 Minuman' },
  { id: 'teh-kotak', emoji: '🧃', name: 'Teh Kotak', desc: 'Kemasan 250ml', price: 5000, category: '🥤 Minuman' },
  { id: 'susu-cokelat', emoji: '🥛', name: 'Susu Kotak Cokelat', desc: 'Kemasan 200ml', price: 6000, category: '🥤 Minuman' },
  
  // Rumah Tangga
  { id: 'tisu-gulung', emoji: '🧻', name: 'Tisu Gulung', desc: '1 gulung', price: 5500, category: '🧻 Rumah Tangga' },
  { id: 'sabun-cuci', emoji: '🧼', name: 'Sabun Cuci Piring Sachet', desc: 'Isi 1 sachet', price: 1500, category: '🧻 Rumah Tangga' },
  { id: 'korek-api', emoji: '🕯️', name: 'Korek Api Gas', desc: '1 buah', price: 3000, category: '🧻 Rumah Tangga' },
  
  // Pulsa & Token
  { id: 'pulsa-20k', emoji: '📱', name: 'Pulsa Rp20.000', desc: 'Semua operator', price: 22000, category: '📶 Pulsa & Token' },
  { id: 'token-50k', emoji: '⚡', name: 'Token Listrik Rp50.000', desc: 'Untuk semua meteran', price: 52500, category: '📶 Pulsa & Token' },
  { id: 'paket-data', emoji: '💳', name: 'Paket Data 3GB', desc: 'Masa aktif 30 hari', price: 25000, category: '📶 Pulsa & Token' }
];

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
    p.desc.toLowerCase().includes(q)
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
