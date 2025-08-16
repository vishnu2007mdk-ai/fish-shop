-- Fish Shop Database Schema
-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS fish_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fish_shop;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category_id INT,
    image_url VARCHAR(500),
    stock_quantity INT DEFAULT 0,
    in_stock BOOLEAN DEFAULT TRUE,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    zip_code VARCHAR(20),
    country VARCHAR(100) DEFAULT 'USA',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT,
    shipping_city VARCHAR(100),
    shipping_state VARCHAR(100),
    shipping_zip VARCHAR(20),
    shipping_country VARCHAR(100) DEFAULT 'USA',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Newsletter subscriptions table
CREATE TABLE IF NOT EXISTS newsletter_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    subscribed BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Cart table (for guest users)
CREATE TABLE IF NOT EXISTS guest_cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insert sample categories
INSERT INTO categories (name, description) VALUES
('Salmon', 'Fresh salmon varieties including Atlantic, Pacific, and Alaskan'),
('Cod', 'Atlantic and Pacific cod fillets'),
('Tuna', 'Premium tuna varieties including yellowfin and albacore'),
('Bass', 'Sea bass and striped bass'),
('Shellfish', 'Shrimp, crab, lobster, and other shellfish'),
('Trout', 'Freshwater trout varieties'),
('Mahi Mahi', 'Fresh mahi mahi fillets'),
('Snapper', 'Red snapper and other snapper varieties'),
('Halibut', 'Pacific halibut fillets'),
('Swordfish', 'Fresh swordfish steaks'),
('Grouper', 'Fresh grouper fillets');

-- Insert sample products
INSERT INTO products (name, description, price, category_id, stock_quantity, featured) VALUES
('Fresh Salmon', 'Wild-caught Alaskan salmon, perfect for grilling or baking', 24.99, 1, 50, TRUE),
('Atlantic Cod', 'Fresh Atlantic cod fillets, mild and flaky texture', 18.99, 2, 75, TRUE),
('Tuna Steaks', 'Premium yellowfin tuna steaks, perfect for sushi or grilling', 22.99, 3, 40, TRUE),
('Sea Bass', 'European sea bass, delicate flavor and firm texture', 28.99, 4, 30, FALSE),
('Rainbow Trout', 'Fresh rainbow trout, mild and delicate flavor', 16.99, 6, 60, FALSE),
('Mahi Mahi', 'Fresh mahi mahi fillets, firm texture and mild taste', 26.99, 7, 35, FALSE),
('Red Snapper', 'Whole red snapper, perfect for roasting or grilling', 32.99, 8, 0, FALSE),
('Halibut', 'Pacific halibut fillets, thick and meaty texture', 34.99, 9, 25, FALSE),
('Swordfish', 'Fresh swordfish steaks, meaty and flavorful', 29.99, 10, 45, FALSE),
('Grouper', 'Fresh grouper fillets, mild and flaky', 27.99, 11, 40, FALSE);

-- Create indexes for better performance
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_featured ON products(featured);
CREATE INDEX idx_products_in_stock ON products(in_stock);
CREATE INDEX idx_orders_customer ON orders(customer_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_order_items_order ON order_items(order_id);
CREATE INDEX idx_order_items_product ON order_items(product_id);
CREATE INDEX idx_guest_cart_session ON guest_cart(session_id);

