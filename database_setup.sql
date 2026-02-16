-- Create Database
CREATE DATABASE IF NOT EXISTS toko_kelontong;
USE toko_kelontong;

-- Table: Products (Updated with stock)
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255) DEFAULT 'default.jpg',
    category VARCHAR(100),
    is_popular BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: Info Toko
CREATE TABLE IF NOT EXISTS info_toko (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_name VARCHAR(100) UNIQUE NOT NULL,
    content TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: Orders
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Table: Order Items
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert Dummy Data for Products (If not exists)
-- Note: Re-inserting with stock values if table is empty or just for reference
INSERT IGNORE INTO products (id, name, description, price, stock, image, category, is_popular) VALUES
(1, 'Beras Premium 5kg', 'Beras putih berkualitas tinggi, pulen dan wangi.', 65000, 50, 'beras.jpg', 'Sembako', 1),
(2, 'Minyak Goreng 2L', 'Minyak goreng kelapa sawit jernih.', 28000, 100, 'minyak.jpg', 'Sembako', 1),
(3, 'Gula Pasir 1kg', 'Gula pasir putih manis alami.', 14500, 200, 'gula.jpg', 'Sembako', 1),
(4, 'Telur Ayam 1kg', 'Telur ayam negeri segar.', 26000, 30, 'telur.jpg', 'Sembako', 0),
(5, 'Terigu Segitiga Biru 1kg', 'Tepung terigu serbaguna.', 12000, 80, 'terigu.jpg', 'Sembako', 0),
(6, 'Indomie Goreng (Dus)', 'Mie instan goreng paling populer.', 110000, 25, 'indomie.jpg', 'Makanan Instan', 1);

-- Insert Dummy Users
DELETE FROM users WHERE username IN ('admin', 'user');
INSERT INTO users (username, password, full_name, role) VALUES
('admin', '$2y$10$y3YTV62aamY8McsUlUSgYO0C.f9V0hniwa3lqLxZIp0fhkeSFFLdu', 'Administrator', 'admin'),
('user', '$2y$10$AYqHqLcnmMnH8h7/6Feq5.4bEcfLOZWwKj07mAKyWHp0B2VAmISWO', 'Pelanggan Setia', 'user');
