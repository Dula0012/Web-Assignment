CREATE DATABASE IF NOT EXISTS girlly_beauty;
USE girlly_beauty;

CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category_id INT,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    brand VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20),
    customer_address TEXT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(200) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'Unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admin (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@girllybeauty.lk');

INSERT INTO categories (name, description) VALUES
('Skincare', 'Skincare products for healthy and glowing skin'),
('Makeup', 'Makeup essentials for all occasions'),
('Haircare', 'Haircare products for beautiful hair'),
('Fragrance', 'Perfumes and body sprays'),
('Nails', 'Nail polish and nail care products');

INSERT INTO products (name, description, price, category_id, image, stock, brand) VALUES
('Moisturizing Face Cream', 'Hydrating cream for all skin types', 2500.00, 1, 'facecream.jpg', 50, 'Fair & Lovely'),
('Matte Lipstick - Red', 'Long-lasting matte finish lipstick', 1200.00, 2, 'lipstick-red.jpg', 100, 'Maybelline'),
('Shampoo Anti-Dandruff', 'Removes dandruff and nourishes hair', 1500.00, 3, 'shampoo.jpg', 75, 'Sunsilk'),
('Perfume Floral', 'Elegant floral fragrance for women', 3500.00, 4, 'perfume.jpg', 30, 'Enchanteur'),
('Nail Polish - Pink', 'Quick-dry nail polish in pink', 450.00, 5, 'nailpolish-pink.jpg', 120, 'Revlon'),
('Vitamin C Serum', 'Brightening serum with vitamin C', 3200.00, 1, 'serum.jpg', 40, 'Olay'),
('Mascara Volumizing', 'Creates volume and length for lashes', 1800.00, 2, 'mascara.jpg', 60, 'Maybelline'),
('Hair Conditioner', 'Deep conditioning treatment', 1400.00, 3, 'conditioner.jpg', 65, 'Dove'),
('Body Spray Fresh', 'Refreshing body spray for all-day freshness', 1100.00, 4, 'bodyspray.jpg', 80, 'Impulse'),
('Nail Art Kit', 'Complete kit for nail art designs', 2000.00, 5, 'nailkit.jpg', 25, 'Sally Hansen'),
('BB Cream SPF 30', 'Beauty balm with sun protection', 2200.00, 2, 'bbcream.jpg', 45, 'Garnier'),
('Face Wash Foaming', 'Gentle foaming face wash', 850.00, 1, 'facewash.jpg', 90, 'Nivea'),
('Eyebrow Pencil', 'Defines and shapes eyebrows', 650.00, 2, 'eyebrow.jpg', 70, 'Maybelline'),
('Hair Oil Nourishing', 'Natural oils for hair nourishment', 980.00, 3, 'hairoil.jpg', 55, 'Parachute'),
('Eau de Parfum Luxury', 'Premium long-lasting fragrance', 5500.00, 4, 'edp.jpg', 15, 'Zara');
