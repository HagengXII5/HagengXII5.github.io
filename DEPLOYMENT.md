# 🚀 Deployment Guide - Klik Madura

## GitHub Pages Setup

Website ini sudah dikonfigurasi untuk GitHub Pages dengan clean URLs.

### URL Structure

Setelah deploy ke GitHub Pages, website akan dapat diakses di:

```
https://hagengxii5.github.io/              → Landing page
https://hagengxii5.github.io/produk        → Daftar barang
https://hagengxii5.github.io/checkout      → Checkout
https://hagengxii5.github.io/transaksi     → Riwayat transaksi
https://hagengxii5.github.io/toko          → Lokasi toko
```

### Cara Deploy

1. **Push ke GitHub**
   ```bash
   git add .
   git commit -m "Restructure: Clean URLs dengan folder structure"
   git push origin main
   ```

2. **Aktifkan GitHub Pages**
   - Buka repository di GitHub
   - Pergi ke **Settings** → **Pages**
   - Di **Source**, pilih branch `main` dan folder `/ (root)`
   - Klik **Save**
   - Tunggu 1-2 menit untuk deployment

3. **Verifikasi**
   - Buka `https://hagengxii5.github.io/`
   - Test navigasi ke semua halaman
   - Test fitur keranjang dan checkout

## Local Development

### Menggunakan Python
```bash
python -m http.server 8000
```
Akses: `http://localhost:8000`

### Menggunakan Node.js
```bash
npx http-server -p 8000
```
Akses: `http://localhost:8000`

### Menggunakan PHP
```bash
php -S localhost:8000
```
Akses: `http://localhost:8000`

### Menggunakan VS Code Live Server
1. Install extension "Live Server"
2. Right-click `index.html` → "Open with Live Server"

## 🔍 Testing Checklist

Setelah deploy, test semua fitur:

- [ ] Landing page terbuka
- [ ] Navigasi ke `/produk` work
- [ ] Tambah item ke keranjang
- [ ] Badge keranjang update
- [ ] Checkout page menampilkan item
- [ ] Order berhasil dibuat
- [ ] Transaksi muncul di `/transaksi`
- [ ] Logo dan gambar load dengan benar
- [ ] Semua link internal work
- [ ] Data persist di localStorage

## 🐛 Troubleshooting

### Logo tidak muncul
- **Root page**: Gunakan `./img/logo.svg`
- **Subfolder page**: Gunakan `../img/logo.svg`

### 404 Error saat navigasi
- Pastikan setiap folder punya `index.html`
- Check case sensitivity di nama folder
- Pastikan GitHub Pages sudah aktif

### CSS tidak load
- Check path relatif di `<link>` tag
- Pastikan tidak ada typo di path

### localStorage tidak work di GitHub Pages
- localStorage work di HTTPS (GitHub Pages default)
- Clear browser cache jika ada masalah

## 📝 File Structure

```
HagengXII5.github.io/
├── index.html              # Root landing page
├── produk/
│   └── index.html         # /produk
├── checkout/
│   └── index.html         # /checkout
├── transaksi/
│   └── index.html         # /transaksi
├── toko/
│   └── index.html         # /toko
├── img/                   # Assets
│   └── *.svg, *.png
├── README.md
└── DEPLOYMENT.md          # This file
```

## 🎯 Benefits of This Structure

✅ **Clean URLs**: `hagengxii5.github.io/produk` (bukan `.html`)  
✅ **SEO Friendly**: URL lebih mudah diingat  
✅ **Professional**: Seperti website modern lainnya  
✅ **GitHub Pages Compatible**: Work tanpa konfigurasi tambahan  
✅ **Maintainable**: Struktur folder yang jelas  

## 🔗 Internal Links

Semua internal link menggunakan absolute path dari root:

```html
<!-- ✅ Correct -->
<a href="/">Home</a>
<a href="/produk">Produk</a>
<a href="/checkout">Checkout</a>

<!-- ❌ Incorrect (jangan pakai) -->
<a href="index.html">Home</a>
<a href="barang.html">Produk</a>
```

## 🖼️ Image Paths

```html
<!-- Root page (index.html) -->
<img src="./img/logo.svg">

<!-- Subfolder page (produk/index.html) -->
<img src="../img/logo.svg">
```

## 🎨 Custom Domain (Optional)

Jika ingin pakai custom domain:

1. Beli domain (misal: `klikmadura.com`)
2. Tambahkan CNAME record:
   ```
   CNAME  @  hagengxii5.github.io
   ```
3. Buat file `CNAME` di root dengan isi:
   ```
   klikmadura.com
   ```
4. Push ke GitHub
5. Update di GitHub Pages settings

---

Happy Deploying! 🚀
