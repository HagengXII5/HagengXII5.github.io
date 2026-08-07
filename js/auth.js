
function initializeUsers() {
  const users = getUsers();
  if (users.length === 0) {
    const defaultUsers = [
      {
        id: 1,
        username: 'admin',
        password: 'admin123',
        fullName: 'Administrator',
        email: 'admin@klikmadura.com',
        phone: '',
        address: '',
        role: 'admin',
        createdAt: new Date().toISOString(),
        isActive: true
      }
    ];
    localStorage.setItem('klikMaduraUsers', JSON.stringify(defaultUsers));
  }
}

function getUsers() {
  return JSON.parse(localStorage.getItem('klikMaduraUsers') || '[]');
}

function saveUsers(users) {
  localStorage.setItem('klikMaduraUsers', JSON.stringify(users));
}

function getCurrentUser() {
  const userStr = localStorage.getItem('klikMaduraCurrentUser');
  return userStr ? JSON.parse(userStr) : null;
}

function setCurrentUser(user) {
  if (user) {
    const { password, ...userWithoutPassword } = user;
    localStorage.setItem('klikMaduraCurrentUser', JSON.stringify(userWithoutPassword));
  } else {
    localStorage.removeItem('klikMaduraCurrentUser');
  }
}

function isLoggedIn() {
  return getCurrentUser() !== null;
}

function isAdmin() {
  const user = getCurrentUser();
  return user && user.role === 'admin';
}

function login(username, password) {
  const users = getUsers();
  const user = users.find(u => u.username === username && u.password === password && u.isActive);
  
  if (user) {
    user.lastLogin = new Date().toISOString();
    saveUsers(users);
    
    setCurrentUser(user);
    
    return {
      success: true,
      user: user,
      message: 'Login berhasil!'
    };
  }
  
  return {
    success: false,
    message: 'Username atau password salah.'
  };
}

function logout() {
  setCurrentUser(null);
  window.location.href = '/';
}

function register(userData) {
  const users = getUsers();
  
  if (users.find(u => u.username === userData.username)) {
    return {
      success: false,
      message: 'Username sudah digunakan.'
    };
  }
  
  if (userData.email && users.find(u => u.email === userData.email)) {
    return {
      success: false,
      message: 'Email sudah terdaftar.'
    };
  }
  
  const newUser = {
    id: users.length > 0 ? Math.max(...users.map(u => u.id)) + 1 : 1,
    username: userData.username,
    password: userData.password,
    fullName: userData.fullName,
    email: userData.email || '',
    phone: userData.phone || '',
    address: userData.address || '',
    role: 'customer',
    createdAt: new Date().toISOString(),
    isActive: true
  };
  
  users.push(newUser);
  saveUsers(users);
  
  return {
    success: true,
    user: newUser,
    message: 'Registrasi berhasil!'
  };
}

function updateProfile(userId, updates) {
  const users = getUsers();
  const userIndex = users.findIndex(u => u.id === userId);
  
  if (userIndex === -1) {
    return { success: false, message: 'User tidak ditemukan.' };
  }
  
  users[userIndex] = { ...users[userIndex], ...updates };
  saveUsers(users);
  
  const currentUser = getCurrentUser();
  if (currentUser && currentUser.id === userId) {
    setCurrentUser(users[userIndex]);
  }
  
  return {
    success: true,
    user: users[userIndex],
    message: 'Profil berhasil diperbarui!'
  };
}

function getUserTransactions(userId) {
  const allTransactions = getTransactions();
  return allTransactions.filter(t => t.userId === userId);
}

function syncUserCart(userId) {
  const localCart = getCart();
  if (localCart.length > 0) {
    console.log('Cart synced for user:', userId);
  }
}

function requireLogin() {
  if (!isLoggedIn()) {
    const currentPath = window.location.pathname;
    window.location.href = `/login?redirect=${encodeURIComponent(currentPath)}`;
    return false;
  }
  return true;
}

function requireAdmin() {
  if (!isAdmin()) {
    alert('Akses ditolak. Halaman ini hanya untuk admin.');
    window.location.href = '/';
    return false;
  }
  return true;
}

initializeUsers();

function updateAuthUI() {
  const user = getCurrentUser();
  const authContainer = document.getElementById('authContainer');
  
  if (!authContainer) return;
  
  if (user) {
    authContainer.innerHTML = `
      <div class="user-menu" id="userMenu">
        <span class="user-name">👤 ${user.fullName || user.username}</span>
        <div class="user-dropdown" id="userDropdown">
          ${user.role === 'admin' ? '<a href="/admin">⚙️ Admin Panel</a>' : ''}
          <a href="/transaksi">📋 Transaksi Saya</a>
          <a href="#" onclick="logout(); return false;">🚪 Logout</a>
        </div>
      </div>
    `;
    
    setupDropdownHandlers();
  } else {
    authContainer.innerHTML = `
      <a href="/login" class="auth-link">Masuk</a>
      <a href="/register" class="auth-link auth-register">Daftar</a>
    `;
  }
}

let dropdownTimeout;
function setupDropdownHandlers() {
  const userMenu = document.getElementById('userMenu');
  const userDropdown = document.getElementById('userDropdown');
  
  if (!userMenu || !userDropdown) return;
  
  userMenu.addEventListener('mouseenter', () => {
    clearTimeout(dropdownTimeout);
    userDropdown.style.display = 'block';
  });
  
  userMenu.addEventListener('mouseleave', () => {
    dropdownTimeout = setTimeout(() => {
      userDropdown.style.display = 'none';
    }, 200);
  });
  
  userDropdown.addEventListener('mouseenter', () => {
    clearTimeout(dropdownTimeout);
  });
  
  userDropdown.addEventListener('mouseleave', () => {
    dropdownTimeout = setTimeout(() => {
      userDropdown.style.display = 'none';
    }, 200);
  });
}

document.addEventListener('DOMContentLoaded', updateAuthUI);
