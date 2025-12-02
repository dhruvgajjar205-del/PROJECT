-- Shopping Cart Database Schema
-- Run this SQL script to create the database structure

-- First, create the database (run this separately if needed)
-- CREATE DATABASE IF NOT EXISTS shopping_cart;

-- Use the database
USE shopping_cart;

-- Users table for admin authentication
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image VARCHAR(500),
    stock_quantity INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    receipt VARCHAR(100) UNIQUE NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_address TEXT NOT NULL,
    customer_city VARCHAR(100) NOT NULL,
    customer_pincode VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    order_status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    discount_code VARCHAR(50),
    discount_amount DECIMAL(10,2) DEFAULT 0,
    razorpay_order_id VARCHAR(100),
    razorpay_payment_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Cart sessions table (for persistent cart)
CREATE TABLE IF NOT EXISTS cart_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    session_id VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_session_product (session_id, product_id)
);

-- Insert default admin user
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@shoppingcart.com', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- Insert sample products
INSERT INTO products (name, price, description, image, stock_quantity) VALUES
('Apple iPhone 14 Pro', 60000.00, 'Latest iOS smartphone with advanced features', 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=200&h=150&fit=crop', 10),
('Sony Wireless Headphones', 15000.00, 'Noise-cancelling wireless headphones', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=200&h=150&fit=crop', 25),
('Canon DSLR Camera', 50000.00, 'Professional digital camera for photography', 'https://images.unsplash.com/photo-1606983340126-99ab4feaa64a?w=200&h=150&fit=crop', 5),
('HP Laser Printer', 12000.00, 'High-speed color laser printer', 'https://images.unsplash.com/photo-1587825140708-dfaf72ae4b04?w=200&h=150&fit=crop', 15),
('JBL Home Speaker', 25000.00, 'Smart home speaker system with voice control', 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=200&h=150&fit=crop', 8),
('Microsoft Surface Laptop', 80000.00, 'Premium 2-in-1 laptop for productivity', 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=200&h=150&fit=crop', 12),
('Dell Laptop', 45000.00, 'High-performance laptop for work and gaming', 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=200&h=150&fit=crop', 20),
('Samsung Smartphone', 25000.00, 'Latest Android smartphone with advanced features', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=200&h=150&fit=crop', 30),
('LG LED TV', 35000.00, '55-inch 4K LED TV with smart features', 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=200&h=150&fit=crop', 7),
('Whirlpool Refrigerator', 28000.00, 'Double door refrigerator with frost-free technology', 'https://images.unsplash.com/photo-1584568694244-14e61205dd8b?w=200&h=150&fit=crop', 6),
('LG Washing Machine', 22000.00, 'Fully automatic front-load washing machine', 'https://images.unsplash.com/photo-1626806787426-5910811b6325?w=200&h=150&fit=crop', 9)
ON DUPLICATE KEY UPDATE name=name;

-- Create indexes for better performance
CREATE INDEX idx_orders_receipt ON orders(receipt);
CREATE INDEX idx_orders_status ON orders(order_status);
CREATE INDEX idx_orders_created_at ON orders(created_at);
CREATE INDEX idx_products_active ON products(is_active);
CREATE INDEX idx_cart_sessions_session ON cart_sessions(session_id);
