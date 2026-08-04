# 🔧 Admin Dashboard - Klik Madura

## Overview
Admin Dashboard adalah panel kontrol untuk mengelola produk dan transaksi di website Klik Madura. Hanya user dengan role **admin** yang bisa mengakses halaman ini.

---

## 🔑 Akses Admin

### Login sebagai Admin:
```
URL: /login
Username: admin
Password: admin123
```

Setelah login, Anda akan melihat menu **"⚙️ Admin Panel"** di dropdown profile.

---

## 📊 Fitur Dashboard

### 1. **Statistik Dashboard**
Dashboard menampilkan 4 metrik utama:
- 📦 **Total Produk** - Jumlah total produk dalam database
- ✅ **Stok Tersedia** - Jumlah produk yang stoknya tersedia
- 📋 **Total Transaksi** - Jumlah total transaksi yang pernah dibuat
- 💰 **Total Pendapatan** - Total revenue dari transaksi yang selesai

### 2. **Manajemen Produk**

#### ➕ Tambah Produk Baru
1. Klik tombol **"+ Tambah Produk"**
2. Isi form:
   - **Emoji Produk** - Icon emoji untuk produk (contoh: ☕ 🍜 🥤)
   - **Nama Produk** - Nama lengkap produk
   - **Deskripsi** - Deskripsi singkat produk (opsional)
   - **Harga** - Harga dalam Rupiah
   - **Kategori** - Pilih dari 6 kategori yang tersedia
   - **Stok Tersedia** - Centang jika produk tersedia
3. Klik **"Simpan"**

#### ✏️ Edit Produk
1. Klik tombol **"✏️ Edit"** pada produk yang ingin diedit
2. Modal akan terbuka dengan data produk yang sudah terisi
3. Ubah data yang diperlukan
4. Klik **"Simpan"**

#### ❌/✅ Toggle Status Stok
- Klik **"❌ Habiskan"** untuk mengubah status menjadi stok habis
- Klik **"✅ Sediakan"** untuk mengubah status menjadi stok tersedia
- Produk dengan stok habis akan tetap tampil di katalog tapi dengan badge "Habis"

#### 🗑️ Hapus Produk
1. Klik tombol **"🗑️ Hapus"** pada produk yang ingin dihapus
2. Konfirmasi penghapusan
3. Produk akan dihapus permanen dari database

> ⚠️ **Warning**: Produk yang sudah dihapus tidak bisa dikembalikan!

### 3. **Manajemen Transaksi**

Dashboard menampilkan semua transaksi dari semua user dengan informasi:
- Order ID
- Nama user yang checkout
- Daftar items
- Total harga
- Status (Proses / Selesai / Batal)
- Tanggal transaksi

#### 🗑️ Hapus Transaksi
1. Klik tombol **"🗑️ Hapus"** pada transaksi yang ingin dihapus
2. Konfirmasi penghapusan
3. Transaksi akan dihapus permanen

> ⚠️ **Warning**: Transaksi yang sudah dihapus tidak bisa dikembalikan!

---

## 💾 Sistem Penyimpanan

### localStorage Priority
Admin dashboard menggunakan sistem **localStorage priority** untuk manajemen produk:

1. **Saat pertama kali load**:
   - System cek localStorage untuk `klikMaduraProducts`
   - Jika ada → load dari localStorage
   - Jika tidak → load dari `data/products.json`

2. **Saat admin mengubah produk**:
   - Perubahan disimpan ke localStorage sebagai `klikMaduraProducts`
   - File `products.json` tidak diubah (untuk keamanan)
   - Semua halaman lain akan membaca dari localStorage yang sudah dimodifikasi

3. **Reset ke default**:
   - Hapus key `klikMaduraProducts` dari localStorage
   - Refresh halaman
   - Produk akan kembali load dari `products.json`

### localStorage Keys:
```javascript
klikMaduraProducts      // Modified product list by admin
klikMaduraUsers         // User database
klikMaduraCurrentUser   // Current logged in user
klikMaduraCart          // Shopping cart
klikMaduraTransactions  // All transactions
```

---

## 🎯 Use Cases

### Scenario 1: Menambah Produk Musiman
```
Contoh: Tambah produk "Es Teh Pucuk Cup"
1. Login sebagai admin
2. Buka Admin Panel
3. Klik "+ Tambah Produk"
4. Isi data:
   - Emoji: 🧊
   - Nama: Es Teh Pucuk Cup
   - Deskripsi: Teh hijau dingin cup 220ml
   - Harga: 4500
   - Kategori: Minuman
   - Stok: ✅ Tersedia
5. Simpan
6. Produk langsung muncul di katalog produk
```

### Scenario 2: Mengubah Harga Produk
```
Contoh: Naikkan harga Indomie dari Rp3.500 ke Rp4.000
1. Login sebagai admin
2. Buka Admin Panel
3. Cari produk "Indomie Mie Goreng"
4. Klik "✏️ Edit"
5. Ubah harga dari 3500 ke 4000
6. Simpan
7. Harga di semua halaman langsung terupdate
```

### Scenario 3: Menandai Produk Habis
```
Contoh: Stok Aqua habis sementara
1. Login sebagai admin
2. Buka Admin Panel
3. Cari produk "Aqua Botol 600ml"
4. Klik "❌ Habiskan"
5. Status berubah jadi badge merah "Habis"
6. Customer masih bisa lihat produk tapi tidak bisa order
```

### Scenario 4: Hapus Transaksi Test
```
Contoh: Hapus transaksi test yang tidak valid
1. Login sebagai admin
2. Buka Admin Panel
3. Tab "Transaksi"
4. Cari transaksi test
5. Klik "🗑️ Hapus"
6. Konfirmasi
7. Transaksi dihapus dari list
```

---

## 🔒 Security & Access Control

### Role-Based Access:
- **Admin**: 
  - Akses penuh ke Admin Dashboard
  - Bisa CRUD produk
  - Bisa hapus transaksi
  - Lihat semua transaksi dari semua user

- **Customer**: 
  - Tidak bisa akses Admin Dashboard
  - Hanya lihat transaksi sendiri
  - Bisa belanja dan checkout

### Protected Route:
Admin dashboard menggunakan `requireAdmin()` function yang:
1. Cek apakah user sudah login
2. Cek apakah role user adalah "admin"
3. Jika tidak, redirect ke homepage dengan alert

```javascript
// In admin.js
if (!requireAdmin()) {
  // Auto redirect to homepage
}
```

---

## 🎨 UI/UX Features

### Responsive Design
- Desktop: Full table view dengan semua kolom
- Tablet: Adjusted spacing
- Mobile: Horizontal scroll untuk table

### Real-time Feedback
- ✅ Success alerts setelah aksi berhasil
- ❌ Error alerts jika ada masalah
- Auto-hide alerts setelah 3 detik

### Modal Form
- Clean modal design untuk add/edit produk
- Form validation
- Escape key untuk close modal
- Click outside untuk close modal

### Color Coding
- 🟢 Green badges: Success, Available, Completed
- 🔴 Red badges: Danger, Out of Stock, Cancelled
- 🟡 Yellow badges: Warning, Processing

---

## 🚀 Advanced Features (Future)

### Potensial Enhancement:
1. **Bulk Actions**
   - Select multiple products
   - Bulk delete, bulk stock update

2. **Product Images**
   - Upload gambar produk
   - Image gallery

3. **Analytics**
   - Sales chart
   - Popular products
   - Revenue trends

4. **Export Data**
   - Export products to CSV/Excel
   - Export transactions to CSV/Excel

5. **Advanced Filters**
   - Filter products by category
   - Filter transactions by date range, status, user

6. **Order Management**
   - Change order status
   - Add tracking info
   - Communicate with customers

---

## 📱 Testing Checklist

- [x] Login sebagai admin
- [x] Lihat statistik dashboard
- [x] Tambah produk baru
- [x] Edit produk existing
- [x] Toggle status stok
- [x] Hapus produk
- [x] Lihat semua transaksi
- [x] Hapus transaksi
- [x] Produk yang diubah muncul di katalog
- [x] Stats update setelah perubahan
- [x] Non-admin tidak bisa akses dashboard

---

## 🐛 Troubleshooting

### Produk tidak muncul setelah ditambah
**Solusi**: Refresh halaman `/produk` untuk reload produk dari localStorage

### Perubahan tidak tersimpan
**Solusi**: 
1. Cek browser console untuk error
2. Pastikan localStorage tidak penuh
3. Try clear cache dan reload

### Dashboard tidak bisa diakses
**Solusi**:
1. Pastikan sudah login
2. Pastikan login sebagai admin (username: admin)
3. Cek localStorage untuk `klikMaduraCurrentUser`

### Stats tidak akurat
**Solusi**: Refresh halaman untuk recalculate statistics

---

## 📞 Support

Jika ada pertanyaan atau issue dengan Admin Dashboard:
1. Check console untuk error messages
2. Check localStorage untuk data integrity
3. Refer to `ADMIN_README.md` (file ini)

---

**© 2026 Klik Madura - Admin Dashboard v1.0**
