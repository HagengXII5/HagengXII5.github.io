-- Klik Madura Database Schema
-- MySQL Database for User Authentication and Transaction Management

-- Create database
CREATE DATABASE IF NOT EXISTS klik_madura;
USE klik_madura;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- Transactions table
CREATE TABLE IF NOT EXISTS transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_no VARCHAR(50) UNIQUE NOT NULL,
    items_text TEXT NOT NULL,
    items_json JSON NOT NULL,
    store_name VARCHAR(100) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    delivery_method ENUM('antar', 'ambil') NOT NULL,
    delivery_address TEXT,
    payment_method VARCHAR(50),
    status ENUM('proses', 'selesai', 'batal') DEFAULT 'proses',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Cart items table (optional, for persistent cart across devices)
CREATE TABLE IF NOT EXISTS cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id VARCHAR(50) NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    product_emoji VARCHAR(10),
    price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Insert default admin user
-- Password: admin123 (hashed with bcrypt)
INSERT INTO users (username, password, full_name, email, role, is_active) 
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin123
    'Administrator',
    'admin@klikmadura.com',
    'admin',
    TRUE
) ON DUPLICATE KEY UPDATE username=username;

-- Insert sample customer for testing
INSERT INTO users (username, password, full_name, email, phone, role) 
VALUES (
    'customer1',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin123
    'Customer Demo',
    'customer@example.com',
    '081234567890',
    'customer'
) ON DUPLICATE KEY UPDATE username=username;

-- Indexes for performance
CREATE INDEX idx_user_username ON users(username);
CREATE INDEX idx_user_role ON users(role);
CREATE INDEX idx_transaction_user ON transactions(user_id);
CREATE INDEX idx_transaction_status ON transactions(status);
CREATE INDEX idx_transaction_created ON transactions(created_at);
CREATE INDEX idx_cart_user ON cart_items(user_id);

-- View for transaction summary
CREATE OR REPLACE VIEW transaction_summary AS
SELECT 
    t.id,
    t.order_no,
    t.user_id,
    u.username,
    u.full_name,
    t.items_text,
    t.store_name,
    t.total_amount,
    t.delivery_method,
    t.status,
    t.created_at
FROM transactions t
JOIN users u ON t.user_id = u.id
ORDER BY t.created_at DESC;

-- View for user statistics
CREATE OR REPLACE VIEW user_stats AS
SELECT 
    u.id,
    u.username,
    u.full_name,
    COUNT(t.id) as total_orders,
    SUM(CASE WHEN t.status = 'selesai' THEN 1 ELSE 0 END) as completed_orders,
    SUM(CASE WHEN t.status = 'selesai' THEN t.total_amount ELSE 0 END) as total_spent
FROM users u
LEFT JOIN transactions t ON u.id = t.user_id
WHERE u.role = 'customer'
GROUP BY u.id;
