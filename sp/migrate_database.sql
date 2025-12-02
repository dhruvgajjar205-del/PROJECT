-- Database Migration Script
-- Run this to update existing database with new customer authentication features

USE shopping_cart;

-- Add username column to users table if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS username VARCHAR(50) UNIQUE;

-- Add customer_id column to orders table if it doesn't exist
ALTER TABLE orders ADD COLUMN IF NOT EXISTS customer_id INT;

-- Add foreign key constraint for customer_id
ALTER TABLE orders ADD CONSTRAINT IF NOT EXISTS fk_orders_customer_id
FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE SET NULL;

-- Update existing admin user to have username if not set
UPDATE users SET username = 'admin' WHERE email = 'admin@shoppingcart.com' AND username IS NULL;

-- Insert default admin user (with username)
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@shoppingcart.com', 'admin')
ON DUPLICATE KEY UPDATE username=username;
