# 🔐 Sistem Autentikasi Klik Madura

## Overview
Sistem login/register telah ditambahkan ke website Klik Madura. Sistem ini menggunakan **localStorage** untuk simulasi database di browser (cocok untuk GitHub Pages), dengan struktur MySQL yang sudah disiapkan untuk migrasi ke backend sesungguhnya.

---

## 📁 File yang Ditambahkan

### JavaScript Modules
- **`js/auth.js`** - Module autentikasi utama dengan fungsi login, logout, register, dan manajemen user

### Halaman HTML
- **`login/index.html`** - Halaman login
- **`register/index.html`** - Halaman registrasi user baru

### Database Schema
- **`db/data-1.sql`** - Schema MySQL lengkap untuk production deployment

---

## 🎯 Fitur Autentikasi

### 1. **Login & Logout**
- Login dengan username dan password
- Session management menggunakan localStorage
- Auto-redirect setelah login sukses
- Dropdown menu untuk user yang sudah login

### 2. **Registrasi User**
- Form registrasi lengkap dengan validasi
- Auto-generate user ID
- Default role: `customer`

### 3. **Role Management**
- **Admin**: Username `admin`, Password `admin123`
- **Customer**: User biasa yang register

### 4. **User-Specific Data**
- Setiap transaksi disimpan dengan `userId`
- User hanya melihat transaksi mereka sendiri
- Admin melihat semua transaksi

### 5. **Protected Pages**
- Fungsi `requireLogin()` untuk halaman yang butuh login
- Fungsi `requireAdmin()` untuk halaman khusus admin

---

## 🔑 Default Credentials

### Admin Account
```
Username: admin
Password: admin123
Role: admin
```

Akun admin sudah ter-initialize otomatis saat pertama kali load.

---

## 💾 Data Storage (localStorage)

### Keys Used:
1. **`klikMaduraUsers`** - Array semua user
2. **`klikMaduraCurrentUser`** - User yang sedang login (tanpa password)
3. **`klikMaduraCart`** - Keranjang belanja
4. **`klikMaduraTransactions`** - Semua transaksi

### User Object Structure:
```javascript
{
  id: 1,
  username: "admin",
  password: "admin123", // Plain text untuk demo, hash di production
  fullName: "Administrator",
  email: "admin@klikmadura.com",
  phone: "",
  address: "",
  role: "admin", // atau "customer"
  createdAt: "2026-07-30T...",
  lastLogin: "2026-07-30T...",
  isActive: true
}
```

### Transaction Object (Updated):
```javascript
{
  orderNo: "#KM-20260730-001",
  userId: 1,  // ID user yang checkout
  userName: "Administrator",  // Nama user
  date: "2026-07-30T...",
  items: "Indomie x2, Aqua x1",
  itemsData: [...],
  store: "Warung Madura Margonda",
  total: 15000,
  deliveryMethod: "antar",
  status: "proses"
}
```

---

## 🎨 UI Components

### Header Navigation
Semua halaman sekarang memiliki:
```html
<div id="authContainer"></div>
```

Yang akan di-render menjadi:

**Jika belum login:**
```html
<a href="/login" class="auth-link">Masuk</a>
<a href="/register" class="auth-link auth-register">Daftar</a>
```

**Jika sudah login:**
```html
<div class="user-menu">
  <span class="user-name">👤 Nama User</span>
  <div class="user-dropdown">
    <a href="/admin">Admin Panel</a> <!-- Hanya untuk admin -->
    <a href="/profile">Profil Saya</a>
    <a href="/transaksi">Transaksi Saya</a>
    <a href="#" onclick="logout()">Logout</a>
  </div>
</div>
```

---

## 📊 Admin Features

### Tampilan Transaksi untuk Admin
- Admin melihat **semua transaksi** dari semua user
- Setiap transaksi menampilkan nama user yang checkout
- Badge khusus: "👤 Mode Admin: Menampilkan semua transaksi dari semua user"

---

## 🚀 Cara Menggunakan

### Untuk Testing di Browser:

1. **Buka `index.html`**
2. **Klik tombol "Daftar"** atau **"Masuk"**
3. **Login sebagai admin:**
   - Username: `admin`
   - Password: `admin123`
4. **Atau register akun baru** sebagai customer
5. **Belanja dan checkout** - transaksi akan tersimpan per user
6. **Lihat transaksi** di halaman "Daftar Transaksi"

---

## 🗄️ Migrasi ke MySQL Backend

### Langkah Migrasi:

#### 1. Setup Database
```bash
mysql -u root -p < db/data-1.sql
```

#### 2. Buat Backend API (Node.js/PHP/Python)
Contoh endpoint yang perlu dibuat:
- `POST /api/auth/login` - Login user
- `POST /api/auth/register` - Register user
- `GET /api/auth/me` - Get current user
- `POST /api/auth/logout` - Logout
- `GET /api/transactions` - Get user transactions
- `POST /api/transactions` - Create transaction
- `GET /api/cart` - Get user cart
- `POST /api/cart` - Update cart

#### 3. Update `js/auth.js`
Ganti fungsi localStorage dengan API calls:
```javascript
async function login(username, password) {
  const response = await fetch('/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username, password })
  });
  const result = await response.json();
  
  if (result.success) {
    setCurrentUser(result.user);
  }
  
  return result;
}
```

#### 4. Password Hashing
Di backend, gunakan bcrypt untuk hash password:
```php
// PHP example
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
```

---

## 🔒 Security Notes

### ⚠️ Important untuk Production:

1. **Password Hashing**
   - Saat ini password disimpan plain text di localStorage
   - Di production, WAJIB hash menggunakan bcrypt
   - SQL sudah include contoh bcrypt hash

2. **HTTPS**
   - Wajib gunakan HTTPS di production
   - Jangan kirim password via HTTP

3. **Session Management**
   - Gunakan JWT atau secure session cookies
   - Implement token expiration
   - Refresh token mechanism

4. **Input Validation**
   - Validasi semua input di frontend DAN backend
   - Prevent SQL injection dengan prepared statements
   - Sanitize HTML output

5. **Rate Limiting**
   - Limit login attempts
   - Prevent brute force attacks

---

## 📱 Responsive Design

Sistem auth sudah responsive:
- Mobile-friendly forms
- Dropdown menu yang nyaman di mobile
- Touch-optimized buttons

---

## 🧪 Testing Checklist

- [x] Login dengan admin credentials
- [x] Register user baru
- [x] Logout dan login kembali
- [x] Transaksi tersimpan dengan userId
- [x] User hanya lihat transaksi sendiri
- [x] Admin lihat semua transaksi
- [x] Cart tetap ada setelah logout/login
- [x] Dropdown menu berfungsi
- [x] Redirect setelah login
- [x] Validasi form register

---

## 🎯 Next Steps (Optional)

1. **Profile Page** - Halaman edit profil user
2. **Password Reset** - Forgot password feature
3. **Email Verification** - Verifikasi email saat register
4. **Social Login** - Login dengan Google/Facebook
5. **Admin Dashboard** - Panel admin untuk manage users & orders
6. **Order Tracking** - Real-time tracking pesanan
7. **Push Notifications** - Notifikasi status order

---

## 📞 Support

Jika ada pertanyaan atau issue dengan sistem autentikasi, silakan check:
1. Browser console untuk error messages
2. localStorage untuk melihat data yang tersimpan
3. `AUTH_README.md` (file ini) untuk dokumentasi

---

**© 2026 Klik Madura - Sistem Autentikasi v1.0**
