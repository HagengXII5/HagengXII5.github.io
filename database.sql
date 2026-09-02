-- ============================================================
-- Klik Madura — Database Schema + Seed Data
-- MySQL 8.0+
-- ============================================================

CREATE DATABASE IF NOT EXISTS klik_madura
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE klik_madura;

-- ============================================================
-- TABLE: users
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
  id            INT             NOT NULL AUTO_INCREMENT,
  username      VARCHAR(50)     NOT NULL,
  password_hash VARCHAR(255)    NOT NULL,
  full_name     VARCHAR(100)    NOT NULL DEFAULT '',
  email         VARCHAR(100)    NOT NULL DEFAULT '',
  phone         VARCHAR(30)     NOT NULL DEFAULT '',
  address       TEXT,
  role          ENUM('admin','customer') NOT NULL DEFAULT 'customer',
  is_active     TINYINT(1)      NOT NULL DEFAULT 1,
  last_login    DATETIME        NULL,
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_username (username),
  UNIQUE KEY uq_email (email(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: categories
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
  id            VARCHAR(100)    NOT NULL,
  name          VARCHAR(100)    NOT NULL,
  icon          VARCHAR(10)     NOT NULL DEFAULT '',
  display_order INT             NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: products
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
  id            VARCHAR(100)    NOT NULL,
  emoji         VARCHAR(10)     NOT NULL DEFAULT '',
  name          VARCHAR(200)    NOT NULL,
  description   TEXT,
  price         INT             NOT NULL DEFAULT 0,
  category_id   VARCHAR(100)    NOT NULL,
  category_icon VARCHAR(10)     NOT NULL DEFAULT '',
  in_stock      TINYINT(1)      NOT NULL DEFAULT 1,
  tags          JSON,
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_category (category_id),
  KEY idx_in_stock (in_stock),
  CONSTRAINT fk_product_category FOREIGN KEY (category_id) REFERENCES categories(id) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: stores
-- ============================================================
CREATE TABLE IF NOT EXISTS stores (
  id            VARCHAR(100)    NOT NULL,
  name          VARCHAR(200)    NOT NULL,
  address       TEXT,
  distance      VARCHAR(20)     NOT NULL DEFAULT '-',
  hours         VARCHAR(50)     NOT NULL DEFAULT '24 Jam',
  phone         VARCHAR(30)     NOT NULL DEFAULT '',
  status        ENUM('open','closed') NOT NULL DEFAULT 'open',
  lat           DECIMAL(10,7)   NOT NULL DEFAULT 0.0000000,
  lng           DECIMAL(10,7)   NOT NULL DEFAULT 0.0000000,
  created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: transactions
-- ============================================================
CREATE TABLE IF NOT EXISTS transactions (
  id              INT             NOT NULL AUTO_INCREMENT,
  order_no        VARCHAR(40)     NOT NULL,
  user_id         INT             NULL,
  user_name       VARCHAR(100)    NOT NULL DEFAULT 'Guest',
  store_name      VARCHAR(200)    NOT NULL DEFAULT '',
  total           INT             NOT NULL DEFAULT 0,
  subtotal        INT             NOT NULL DEFAULT 0,
  delivery_fee    INT             NOT NULL DEFAULT 0,
  service_fee     INT             NOT NULL DEFAULT 0,
  status          ENUM('proses','selesai','batal') NOT NULL DEFAULT 'proses',
  delivery_method ENUM('antar','ambil') NOT NULL DEFAULT 'antar',
  delivery_address TEXT,
  payment_method  VARCHAR(50)     NOT NULL DEFAULT 'cod',
  order_notes     TEXT,
  items_summary   TEXT,
  created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_order_no (order_no),
  KEY idx_user_id (user_id),
  KEY idx_status (status),
  KEY idx_created_at (created_at),
  CONSTRAINT fk_transaction_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: transaction_items
-- ============================================================
CREATE TABLE IF NOT EXISTS transaction_items (
  id              INT             NOT NULL AUTO_INCREMENT,
  transaction_id  INT             NOT NULL,
  product_id      VARCHAR(100)    NULL,
  product_name    VARCHAR(200)    NOT NULL,
  product_emoji   VARCHAR(10)     NOT NULL DEFAULT '',
  price           INT             NOT NULL DEFAULT 0,
  qty             INT             NOT NULL DEFAULT 1,
  line_total      INT             NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_transaction_id (transaction_id),
  KEY idx_product_id (product_id),
  CONSTRAINT fk_txitem_transaction FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
  CONSTRAINT fk_txitem_product     FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABLE: cart_items
-- ============================================================
CREATE TABLE IF NOT EXISTS cart_items (
  id          INT         NOT NULL AUTO_INCREMENT,
  user_id     INT         NOT NULL,
  product_id  VARCHAR(100) NOT NULL,
  qty         INT         NOT NULL DEFAULT 1,
  added_at    DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_user_product (user_id, product_id),
  KEY idx_cart_user (user_id),
  CONSTRAINT fk_cart_user    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
  CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- SEED DATA
-- ============================================================

-- Admin user (password: admin123)
INSERT INTO users (username, password_hash, full_name, email, role, is_active) VALUES
('admin', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@klikmadura.com', 'admin', 1);
-- NOTE: The hash above is a placeholder bcrypt for 'admin123'.
-- Run in PHP to generate real hash: echo password_hash('admin123', PASSWORD_BCRYPT);

-- Categories
INSERT INTO categories (id, name, icon, display_order) VALUES
('kopi-minuman-hangat', 'Kopi & Minuman Hangat', '☕', 1),
('mie-instan',          'Mie Instan',            '🍜', 2),
('minuman',             'Minuman',               '🥤', 3),
('snack-makanan',       'Snack & Makanan',       '🍿', 4),
('rumah-tangga',        'Rumah Tangga',          '🧻', 5),
('pulsa-token',         'Pulsa & Token',         '📶', 6);

-- Products (18 items)
INSERT INTO products (id, emoji, name, description, price, category_id, category_icon, in_stock, tags) VALUES
('kapal-api-special',        '☕', 'Kapal Api Special Mix',    'Kopi + gula, 10 sachet',              13500, 'kopi-minuman-hangat', '☕', 1, '["kapal api","kopi","minuman","hangat","sachet"]'),
('good-day-cappuccino',      '☕', 'Good Day Cappuccino',      'Kopi instan, 10 sachet',              11000, 'kopi-minuman-hangat', '☕', 1, '["good day","cappuccino","kopi","minuman"]'),
('teh-sariwangi',            '🍵', 'Teh Sariwangi Celup',      'Teh hitam, 25 kantong',               9500,  'kopi-minuman-hangat', '☕', 1, '["sariwangi","teh","minuman","hangat","celup"]'),
('indomie-goreng',           '🍜', 'Indomie Mie Goreng',       'Mie instan goreng original',          3500,  'mie-instan',          '🍜', 1, '["indomie","mie","goreng","instan"]'),
('indomie-soto',             '🍲', 'Indomie Soto Mie',         'Mie kuah rasa soto',                  3500,  'mie-instan',          '🍜', 1, '["indomie","mie","soto","kuah","instan"]'),
('pop-mie-rasa-baso',        '🥡', 'Pop Mie Rasa Baso',        'Mie cup rasa baso sapi',              7500,  'mie-instan',          '🍜', 1, '["pop mie","mie","cup","baso","instan"]'),
('aqua-600ml',               '🥤', 'Aqua Botol 600ml',         'Air mineral kemasan',                 4000,  'minuman',             '🥤', 1, '["aqua","air","mineral","minuman"]'),
('teh-pucuk-harum',          '🧃', 'Teh Pucuk Harum',          'Teh hijau kemasan 350ml',             5000,  'minuman',             '🥤', 1, '["teh pucuk","teh","hijau","minuman","dingin"]'),
('ultra-milk-coklat',        '🥛', 'Ultra Milk Coklat',        'Susu UHT coklat 250ml',               6500,  'minuman',             '🥤', 1, '["ultra milk","susu","cokelat","UHT","minuman"]'),
('chitato-rasa-sapi-panggang','🍟','Chitato Sapi Panggang',    'Keripik kentang 68g',                 10000, 'snack-makanan',       '🍿', 1, '["chitato","keripik","kentang","snack"]'),
('roma-kelapa',              '🍪', 'Roma Kelapa',              'Biskuit kelapa 300g',                 11000, 'snack-makanan',       '🍿', 1, '["roma","biskuit","kelapa","snack"]'),
('sari-roti-tawar-kupas',    '🍞', 'Sari Roti Tawar Kupas',    'Roti tawar kemasan',                  13500, 'snack-makanan',       '🍿', 1, '["sari roti","roti","tawar","makanan"]'),
('tessa-jumbo',              '🧻', 'Tessa Jumbo Roll',         'Tissue gulung 250 sheet',             6500,  'rumah-tangga',        '🧻', 1, '["tessa","tissue","gulung","rumah tangga"]'),
('sunlight-jeruk-nipis',     '🧼', 'Sunlight Jeruk Nipis',     'Sabun cuci piring 400ml',             8500,  'rumah-tangga',        '🧻', 1, '["sunlight","sabun","cuci","piring","rumah tangga"]'),
('molto-pelembut-pakaian',   '💧', 'Molto Ultra Sekali Bilas', 'Pelembut pakaian 450ml',              12000, 'rumah-tangga',        '🧻', 1, '["molto","pelembut","pakaian","rumah tangga"]'),
('pulsa-20k',                '📱', 'Pulsa Rp20.000',           'All operator (Telkomsel, Indosat, XL, Tri)', 21000, 'pulsa-token', '📶', 1, '["pulsa","digital","operator","telkomsel"]'),
('token-50k',                '⚡', 'Token Listrik Rp50.000',   'PLN prabayar',                        51000, 'pulsa-token',         '📶', 1, '["token","listrik","PLN","digital"]'),
('paket-data-3gb',           '💳', 'Paket Data 3GB',           'Internet 30 hari, all operator',      24000, 'pulsa-token',         '📶', 1, '["paket","data","internet","digital"]');

-- Stores (4 default)
INSERT INTO stores (id, name, address, distance, hours, phone, status, lat, lng) VALUES
('store-margonda', 'Warung Madura Margonda',       'Jl. Margonda Raya No. 45, Depok',          '0,8 km', '24 Jam', '0812-3456-7801', 'open', -6.3639240, 106.8317770),
('store-tole',     'Warung Madura Tole Iskandar',  'Jl. Tole Iskandar No. 12, Depok',          '1,4 km', '24 Jam', '0812-3456-7802', 'open', -6.3949240, 106.8217770),
('store-kartini',  'Warung Madura Kartini',        'Jl. Kartini No. 8, Depok',                 '2,1 km', '24 Jam', '0812-3456-7803', 'open', -6.3739240, 106.8417770),
('store-juanda',   'Warung Madura Juanda',         'Jl. Ir. H. Juanda No. 30, Depok',          '3,0 km', '24 Jam', '0812-3456-7804', 'open', -6.3839240, 106.8517770);
