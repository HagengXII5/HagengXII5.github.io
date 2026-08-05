/**
 * Authentication Module (localStorage version)
 * For GitHub Pages - simulates backend authentication
 * 
 * For production with MySQL backend, replace with API calls
 */

// Initialize users in localStorage (first time setup)
function initializeUsers() {
  const users = getUsers();
  if (users.length === 0) {
    // Add default admin
    const defaultUsers = [
      {
        id: 1,
        username: 'admin',
        password: 'admin123', // In production, this should be hashed
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

// Get all users
function getUsers() {
  return JSON.parse(localStorage.getItem('klikMaduraUsers') || '[]');
}

// Save users
function saveUsers(users) {
  localStorage.setItem('klikMaduraUsers', JSON.stringify(users));
}

// Get current logged-in user
function getCurrentUser() {
  const userStr = localStorage.getItem('klikMaduraCurrentUser');
  return userStr ? JSON.parse(userStr) : null;
}

// Set current user
function setCurrentUser(user) {
  if (user) {
    // Don't store password in current user session
    const { password, ...userWithoutPassword } = user;
    localStorage.setItem('klikMaduraCurrentUser', JSON.stringify(userWithoutPassword));
  } else {
    localStorage.removeItem('klikMaduraCurrentUser');
  }
}

// Check if user is logged in
function isLoggedIn() {
  return getCurrentUser() !== null;
}

// Check if user is admin
function isAdmin() {
  const user = getCurrentUser();
  return user && user.role === 'admin';
}

// Login function
function login(username, password) {
  const users = getUsers();
  const user = users.find(u => u.username === username && u.password === password && u.isActive);
  
  if (user) {
    // Update last login
    user.lastLogin = new Date().toISOString();
    saveUsers(users);
    
    // Set current user
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

// Logout function
function logout() {
  setCurrentUser(null);
  window.location.href = '/';
}

// Register new user
function register(userData) {
  const users = getUsers();
  
  // Check if username already exists
  if (users.find(u => u.username === userData.username)) {
    return {
      success: false,
      message: 'Username sudah digunakan.'
    };
  }
  
  // Check if email already exists
  if (userData.email && users.find(u => u.email === userData.email)) {
    return {
      success: false,
      message: 'Email sudah terdaftar.'
    };
  }
  
  // Create new user
  const newUser = {
    id: users.length > 0 ? Math.max(...users.map(u => u.id)) + 1 : 1,
    username: userData.username,
    password: userData.password, // In production, hash this
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

// Update user profile
function updateProfile(userId, updates) {
  const users = getUsers();
  const userIndex = users.findIndex(u => u.id === userId);
  
  if (userIndex === -1) {
    return { success: false, message: 'User tidak ditemukan.' };
  }
  
  // Update user data
  users[userIndex] = { ...users[userIndex], ...updates };
  saveUsers(users);
  
  // Update current user if it's the same user
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

// Get user transactions
function getUserTransactions(userId) {
  const allTransactions = getTransactions();
  return allTransactions.filter(t => t.userId === userId);
}

// Sync cart with user (when login)
function syncUserCart(userId) {
  const localCart = getCart();
  if (localCart.length > 0) {
    // In a real backend, you would merge local cart with server cart
    // For now, we just keep the local cart
    console.log('Cart synced for user:', userId);
  }
}

// Require login - redirect to login page if not logged in
function requireLogin() {
  if (!isLoggedIn()) {
    const currentPath = window.location.pathname;
    window.location.href = `/login?redirect=${encodeURIComponent(currentPath)}`;
    return false;
  }
  return true;
}

// Require admin - redirect if not admin
function requireAdmin() {
  if (!isAdmin()) {
    alert('Akses ditolak. Halaman ini hanya untuk admin.');
    window.location.href = '/';
    return false;
  }
  return true;
}

// Initialize on load
initializeUsers();

// Update header to show login/logout
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
    
    // Add hover handlers with delay
    setupDropdownHandlers();
  } else {
    authContainer.innerHTML = `
      <a href="/login" class="auth-link">Masuk</a>
      <a href="/register" class="auth-link auth-register">Daftar</a>
    `;
  }
}

// Setup dropdown hover handlers with delay
let dropdownTimeout;
function setupDropdownHandlers() {
  const userMenu = document.getElementById('userMenu');
  const userDropdown = document.getElementById('userDropdown');
  
  if (!userMenu || !userDropdown) return;
  
  // Show dropdown on hover
  userMenu.addEventListener('mouseenter', () => {
    clearTimeout(dropdownTimeout);
    userDropdown.style.display = 'block';
  });
  
  // Hide dropdown with delay when mouse leaves
  userMenu.addEventListener('mouseleave', () => {
    dropdownTimeout = setTimeout(() => {
      userDropdown.style.display = 'none';
    }, 200); // 200ms delay before hiding
  });
  
  // Keep dropdown open when hovering over it
  userDropdown.addEventListener('mouseenter', () => {
    clearTimeout(dropdownTimeout);
  });
  
  // Hide when mouse leaves dropdown
  userDropdown.addEventListener('mouseleave', () => {
    dropdownTimeout = setTimeout(() => {
      userDropdown.style.display = 'none';
    }, 200);
  });
}

// Auto-update auth UI on page load
document.addEventListener('DOMContentLoaded', updateAuthUI);
