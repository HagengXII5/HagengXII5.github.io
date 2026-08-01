# Data Directory

Folder ini berisi file JSON untuk semua data produk yang dijual di Klik Madura.

## 📁 Files

### `products.json`
Database lengkap produk dan kategori dalam format JSON.

## 📋 Structure

### Products Array
Setiap produk memiliki struktur:

```json
{
  "id": "kopi-robusta",           // Unique identifier
  "emoji": "☕",                   // Display emoji
  "name": "Kopi Sachet Robusta",  // Product name
  "description": "1 renceng isi 10", // Product description
  "price": 12000,                 // Price in IDR
  "category": "Kopi & Minuman Hangat", // Category name
  "categoryIcon": "☕",            // Category icon
  "inStock": true,                // Availability status
  "tags": ["kopi", "minuman", "hangat"] // Search tags
}
```

### Categories Array
Setiap kategori memiliki struktur:

```json
{
  "id": "kopi-minuman-hangat",    // Unique identifier
  "name": "Kopi & Minuman Hangat", // Display name
  "icon": "☕",                     // Category icon
  "displayOrder": 1                // Sort order
}
```

### Metadata Object
Informasi tentang data:

```json
{
  "version": "1.0.0",              // Data version
  "lastUpdated": "2026-07-30...",  // Last update timestamp
  "totalProducts": 18,             // Total product count
  "totalCategories": 6,            // Total category count
  "currency": "IDR"                // Currency code
}
```

## 🎯 Usage

### Loading Products Data

**Option 1: Fetch API (Async)**
```javascript
async function loadProducts() {
  const response = await fetch('../data/products.json');
  const data = await response.json();
  return data.products;
}

// Usage
loadProducts().then(products => {
  console.log(products);
});
```

**Option 2: jQuery (if available)**
```javascript
$.getJSON('../data/products.json', function(data) {
  console.log(data.products);
});
```

**Option 3: XMLHttpRequest**
```javascript
const xhr = new XMLHttpRequest();
xhr.open('GET', '../data/products.json');
xhr.onload = function() {
  if (xhr.status === 200) {
    const data = JSON.parse(xhr.responseText);
    console.log(data.products);
  }
};
xhr.send();
```

### Filtering Products

```javascript
// Get products by category
const coffeeProducts = data.products.filter(
  p => p.category === 'Kopi & Minuman Hangat'
);

// Search by tags
const searchResults = data.products.filter(
  p => p.tags.includes('kopi')
);

// Get in-stock products only
const availableProducts = data.products.filter(
  p => p.inStock === true
);

// Get by price range
const affordableProducts = data.products.filter(
  p => p.price <= 10000
);
```

### Sorting Products

```javascript
// Sort by price (ascending)
const sortedByPrice = [...data.products].sort((a, b) => a.price - b.price);

// Sort by name (alphabetical)
const sortedByName = [...data.products].sort((a, b) => 
  a.name.localeCompare(b.name)
);
```

## 🔍 Search Implementation

```javascript
function searchProducts(query) {
  const q = query.toLowerCase();
  return data.products.filter(product => 
    product.name.toLowerCase().includes(q) ||
    product.description.toLowerCase().includes(q) ||
    product.tags.some(tag => tag.includes(q))
  );
}

// Usage
const results = searchProducts('kopi');
```

## 📊 Statistics

```javascript
// Get total products
const totalProducts = data.metadata.totalProducts;

// Get products per category
const productsByCategory = data.products.reduce((acc, product) => {
  acc[product.category] = (acc[product.category] || 0) + 1;
  return acc;
}, {});

// Get average price
const avgPrice = data.products.reduce((sum, p) => sum + p.price, 0) / data.products.length;

// Get price range
const prices = data.products.map(p => p.price);
const minPrice = Math.min(...prices);
const maxPrice = Math.max(...prices);
```

## 🔄 Integration with Current System

Untuk mengintegrasikan dengan `js/products.js`:

**Before (Hardcoded):**
```javascript
const PRODUCTS = [
  { id: 'kopi-robusta', name: 'Kopi...', ... },
  // ...
];
```

**After (Dynamic):**
```javascript
let PRODUCTS = [];

// Load on init
fetch('../data/products.json')
  .then(res => res.json())
  .then(data => {
    PRODUCTS = data.products.map(p => ({
      id: p.id,
      emoji: p.emoji,
      name: p.name,
      desc: p.description,
      price: p.price,
      category: `${p.categoryIcon} ${p.category}`
    }));
    
    // Initialize app after data loaded
    initApp();
  });
```

## 📝 Data Maintenance

### Adding New Product
1. Open `products.json`
2. Add new object to `products` array
3. Update `metadata.totalProducts`
4. Update `metadata.lastUpdated`

### Updating Product
1. Find product by `id`
2. Modify fields
3. Update `metadata.lastUpdated`

### Removing Product
1. Remove product from `products` array
2. Update `metadata.totalProducts`
3. Update `metadata.lastUpdated`

## 🎨 Display Examples

### Product Card
```javascript
function renderProduct(product) {
  return `
    <div class="product-card">
      <div class="emoji">${product.emoji}</div>
      <h3>${product.name}</h3>
      <p>${product.description}</p>
      <span class="price">Rp${product.price.toLocaleString('id-ID')}</span>
      <button>Add to Cart</button>
    </div>
  `;
}
```

### Category List
```javascript
function renderCategories(categories) {
  return categories.map(cat => `
    <button class="category-tab" data-category="${cat.id}">
      ${cat.icon} ${cat.name}
    </button>
  `).join('');
}
```

## 🔐 Validation

Produk harus memiliki:
- ✅ Unique `id`
- ✅ Valid `price` (number > 0)
- ✅ Non-empty `name` and `description`
- ✅ Valid `category` (must exist in categories array)
- ✅ Boolean `inStock`
- ✅ Array of `tags`

## 🚀 Future Enhancements

Potential additions:
- [ ] Product images (URLs)
- [ ] Stock quantity
- [ ] Discount/promo data
- [ ] Product variants (size, flavor)
- [ ] Supplier information
- [ ] Nutritional information
- [ ] Product reviews/ratings

## 📦 Backup

Always backup before making changes:
```bash
cp products.json products.json.backup
```

---

© 2026 Klik Madura
