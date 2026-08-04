# 📦 Stock Management - Klik Madura

## Overview
Sistem manajemen stok untuk mencegah customer membeli produk yang sudah habis stoknya.

---

## 🎯 Fitur

### 1. **Visual Indicator di Halaman Produk**

#### Produk Stok Tersedia:
- ✅ Normal appearance
- ✅ Buttons aktif (+/-)
- ✅ Bisa ditambahkan ke cart

#### Produk Stok Habis:
- 🔴 Badge "HABIS" di pojok kanan atas card
- 🔴 Opacity 60% (tampilan redup)
- 🔴 Nama produk dengan strikethrough
- 🔴 Buttons +/- disabled (tidak bisa diklik)
- 🔴 Quantity tetap 0

### 2. **Validasi di Cart**

#### Auto-remove saat Checkout:
- System otomatis cek stok saat buka halaman checkout
- Produk yang stok habis akan dihapus dari cart
- Warning notification muncul jika ada produk yang dihapus

#### Final Validation:
- Cek ulang sebelum place order
- Alert muncul jika ada produk yang habis
- Mencegah order dibuat jika ada produk habis

### 3. **Admin Control**

Admin bisa mengubah status stok via Admin Dashboard:
- ✅ **Sediakan** - Ubah status jadi tersedia
- ❌ **Habiskan** - Ubah status jadi habis

Perubahan langsung terlihat di katalog produk.

---

## 🔧 Technical Implementation

### CSS Styling
```css
.item-card.out-of-stock {
  opacity: 0.6;
}

.item-card.out-of-stock .item-name {
  text-decoration: line-through;
}

.stock-badge {
  background: #dc3545;
  color: #fff;
  position: absolute;
  top: 12px;
  right: 12px;
}

.qty-box button:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}
```

### JavaScript Validation

#### 1. Product Page (`produk.js`)
```javascript
if (!itemData.inStock) {
  card.classList.add('out-of-stock');
  
  // Add badge
  const badge = document.createElement('div');
  badge.className = 'stock-badge';
  badge.textContent = 'HABIS';
  card.appendChild(badge);
  
  // Disable buttons
  minus.disabled = true;
  plus.disabled = true;
}
```

#### 2. Cart Management (`cart.js`)
```javascript
function addToCart(itemData, qty = 1) {
  // Check if product is in stock
  if (!itemData.inStock) {
    console.warn('Cannot add out-of-stock product');
    return null;
  }
  
  // Add to cart...
}
```

#### 3. Checkout Validation (`checkout.js`)
```javascript
function validateCartStock() {
  // Remove out-of-stock items
  items = items.filter(item => {
    const product = getProductById(item.id);
    return product && product.inStock;
  });
  
  saveCart(items);
  showStockWarning();
}

// Final check before order
const hasOutOfStock = items.some(item => {
  const product = getProductById(item.id);
  return product && !product.inStock;
});

if (hasOutOfStock) {
  alert('Ada produk yang sudah habis...');
  return;
}
```

---

## 🎬 User Flow

### Scenario 1: Customer mencoba beli produk stok habis
```
1. Customer buka halaman /produk
2. Lihat produk "Aqua" dengan badge HABIS
3. Tombol +/- disabled, tidak bisa diklik
4. Customer tidak bisa menambahkan ke cart
```

### Scenario 2: Produk habis setelah di cart
```
1. Customer add "Aqua" ke cart (stok masih ada)
2. Admin ubah "Aqua" jadi stok habis
3. Customer buka halaman checkout
4. System auto-remove "Aqua" dari cart
5. Warning muncul: "Beberapa produk sudah habis..."
```

### Scenario 3: Admin mengubah stok
```
1. Admin login ke /admin
2. Cari produk "Aqua"
3. Klik "❌ Habiskan"
4. Status berubah jadi badge merah "Habis"
5. Customer refresh /produk
6. Produk "Aqua" tampil dengan indicator habis
```

---

## 📊 Data Structure

### Product Object with Stock Status:
```javascript
{
  id: "aqua-600ml",
  emoji: "🥤",
  name: "Aqua Botol 600ml",
  desc: "Air mineral kemasan",
  price: 4000,
  category: "🥤 Minuman",
  inStock: false,  // ← Stock indicator
  tags: ["aqua", "air", "mineral"]
}
```

---

## 🚀 Testing Checklist

- [x] Produk stok habis tampil dengan badge HABIS
- [x] Produk stok habis tampil redup (opacity 60%)
- [x] Nama produk stok habis dengan strikethrough
- [x] Tombol +/- disabled untuk produk stok habis
- [x] Tidak bisa add to cart produk stok habis
- [x] Cart auto-remove produk stok habis saat checkout
- [x] Warning muncul saat produk dihapus dari cart
- [x] Final validation mencegah order produk habis
- [x] Admin bisa toggle status stok
- [x] Perubahan stok langsung terlihat di katalog

---

## 🎨 Visual States

### Product Card States:

#### Available (In Stock):
```
┌─────────────────┐
│ ☕              │
│ Kapal Api       │
│ Kopi + gula     │
│                 │
│ Rp13.500  [-][+]│
└─────────────────┘
```

#### Out of Stock:
```
┌─────────────────┐
│ ☕      [HABIS] │ ← Red badge
│ Aqua Botol      │ ← Strikethrough
│ Air mineral     │ ← 60% opacity
│                 │
│ Rp4.000  [⊗][⊗]│ ← Disabled buttons
└─────────────────┘
```

---

## 🐛 Edge Cases Handled

1. **Product goes out of stock while in cart**
   - ✅ Removed at checkout page load
   - ✅ Warning notification shown

2. **Customer tries to order out-of-stock item**
   - ✅ Final validation before order creation
   - ✅ Alert shown, order prevented

3. **Admin restocks product**
   - ✅ Status updated in localStorage
   - ✅ Customer sees updated status on refresh

4. **Multiple out-of-stock items in cart**
   - ✅ All removed at once
   - ✅ Single warning notification

---

## 📞 Support

For issues related to stock management:
1. Check product data in `data/products.json`
2. Check localStorage key `klikMaduraProducts`
3. Verify `inStock` boolean value
4. Check console for validation warnings

---

**© 2026 Klik Madura - Stock Management System v1.0**
