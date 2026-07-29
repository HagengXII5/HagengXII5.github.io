# Klik Madura - Website E-Commerce Warung

Website referensi desain untuk platform belanja online dari warung Madura yang buka 24 jam.

## 🌐 URL Structure

Website ini menggunakan clean URL tanpa ekstensi `.html`:

- **Landing Page**: `https://hagengxii5.github.io/` 
- **Daftar Produk**: `https://hagengxii5.github.io/produk`
- **Checkout**: `https://hagengxii5.github.io/checkout`
- **Riwayat Transaksi**: `https://hagengxii5.github.io/transaksi`
- **Lokasi Toko**: `https://hagengxii5.github.io/toko`

## 📁 Struktur File

```
HagengXII5.github.io/
├── index.html                 # Landing page (/)
├── produk/
│   └── index.html            # Daftar barang (/produk)
├── checkout/
│   └── index.html            # Checkout (/checkout)
├── transaksi/
│   └── index.html            # Riwayat transaksi (/transaksi)
├── toko/
│   └── index.html            # Lokasi toko (/toko)
├── img/
│   ├── logo_klik_madura.png
│   ├── logo_klik_madura.svg
│   └── logo_klik_madura_v3_biru.svg
└── README.md
```

## ✨ Fitur

### 1. Halaman Barang (`/produk`)
- ✅ Daftar produk dengan emoji visual
- ✅ Kategori produk: Kopi & Rokok, Mie Instan, Minuman, Rumah Tangga, Pulsa & Token
- ✅ Tombol tambah/kurang quantity untuk setiap item
- ✅ Data tersimpan di localStorage (persistent)
- ✅ Counter keranjang di header (badge merah)
- ✅ Fitur search/pencarian produk
- ✅ Filter kategori yang berfungsi

### 2. Halaman Checkout (`/checkout`)
- ✅ Menampilkan item dari keranjang (sinkron dengan localStorage)
- ✅ Pilihan pengiriman: Diantar atau Ambil di Toko
- ✅ Pilih toko untuk pengambilan
- ✅ Metode pembayaran: Tunai (COD), Transfer Bank, E-Wallet
- ✅ Catatan untuk warung
- ✅ Ringkasan pembayaran (subtotal, ongkir, biaya layanan)
- ✅ Generate nomor order otomatis
- ✅ Simpan transaksi ke localStorage
- ✅ Notifikasi keranjang kosong
- ✅ Clear keranjang setelah order berhasil

### 3. Halaman Transaksi (`/transaksi`)
- ✅ Tampilkan riwayat transaksi dari localStorage
- ✅ Filter status: Semua, Selesai, Diproses, Dibatalkan
- ✅ Menampilkan data dummy jika belum ada transaksi
- ✅ Badge keranjang di header

### 4. Halaman Toko (`/toko`)
- ✅ Daftar lokasi warung mitra
- ✅ Detail toko: alamat, jarak, jam operasional, telepon
- ✅ Visual map sederhana
- ✅ Badge keranjang di header

### 5. Halaman Index (`/`)
- ✅ Hero section dengan CTA
- ✅ Statistik (500+ warung, 24 jam, 15 menit antar)
- ✅ Kenapa Klik Madura (kelebihan)
- ✅ Kategori produk
- ✅ Badge keranjang di header
- ✅ Tombol CTA ke halaman barang

## 🔧 Teknologi

- **HTML5** - Struktur
- **CSS3** - Styling (inline dalam `<style>`)
- **Vanilla JavaScript** - Interaktivitas
- **localStorage** - Penyimpanan data lokal

## 💾 Data localStorage

Website ini menggunakan 2 key localStorage:

1. **`klikMaduraCart`** - Menyimpan item keranjang
   ```json
   [
     {
       "id": "kopi-robusta",
       "emoji": "☕",
       "name": "Kopi Sachet Robusta",
       "desc": "1 renceng isi 10",
       "price": 12000,
       "qty": 2
     }
   ]
   ```

2. **`klikMaduraTransactions`** - Menyimpan riwayat transaksi
   ```json
   [
     {
       "orderNo": "#KM-20260729-143022",
       "date": "2026-07-29T14:30:22.000Z",
       "items": "Kopi Sachet Robusta x2, Mie Goreng x3",
       "itemsData": [...],
       "store": "Warung Madura Margonda",
       "total": 39500,
       "status": "proses",
       "deliveryMethod": "antar"
     }
   ]
   ```

## 🎨 Design System

- **Font**: Baloo 2 (headings), Plus Jakarta Sans (body)
- **Warna**:
  - Red: `#D4262C` (CTA, aksen)
  - Green: `#0F6B3A` (harga, status)
  - Cream: `#FFFBF2` (background)
  - Ink: `#1E1B16` (teks)
  - Line: `#EDE6D6` (border)

## 🚀 Cara Pakai

### Local Development
1. Buka `index.html` di browser
2. Atau gunakan local server:
   ```bash
   # Menggunakan Python
   python -m http.server 8000
   
   # Menggunakan Node.js (http-server)
   npx http-server
   ```
3. Akses di: `http://localhost:8000`

### Live Demo
- Landing: `https://hagengxii5.github.io/`
- Produk: `https://hagengxii5.github.io/produk`
- Checkout: `https://hagengxii5.github.io/checkout`
- Transaksi: `https://hagengxii5.github.io/transaksi`
- Toko: `https://hagengxii5.github.io/toko`

### Flow Penggunaan
1. Buka landing page
2. Klik "Mulai Belanja" untuk ke halaman produk
3. Tambahkan produk ke keranjang dengan tombol `+`
4. Klik badge keranjang 🛒 atau tombol "Checkout"
5. Isi detail pengiriman dan pilih metode pembayaran
6. Klik "Buat Pesanan"
7. Cek riwayat di "Daftar Transaksi"

## 🐛 Bug Fixed

✅ Sinkronisasi keranjang antara `barang.html` dan `checkout.html`  
✅ Search dan filter kategori di halaman barang  
✅ Nomor order dinamis (bukan hardcoded)  
✅ Transaksi tersimpan dan ditampilkan di riwayat  
✅ Badge keranjang di semua halaman  
✅ Notifikasi keranjang kosong di checkout  
✅ CTA button di index mengarah ke halaman yang benar  
✅ **Clean URL tanpa ekstensi .html** (folder-based structure)  

## 🎯 Struktur URL Bersih

Menggunakan folder structure dengan `index.html`:
- `/` → `index.html` (root)
- `/produk` → `produk/index.html`
- `/checkout` → `checkout/index.html`
- `/transaksi` → `transaksi/index.html`
- `/toko` → `toko/index.html`

Ini memungkinkan URL seperti `hagengxii5.github.io/produk` tanpa `/index.html` atau `.html`.

## 📝 Catatan

Ini adalah **desain referensi**, bukan produk resmi. Semua data adalah contoh/dummy.

---

© 2026 Klik Madura
