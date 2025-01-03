-- Create database
CREATE DATABASE IF NOT EXISTS gas_station_db;
USE gas_station_db;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'manager', 'staff') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Customers table
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(20),
    loyalty_points INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Fuel types table
CREATE TABLE fuel_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    price_per_liter DECIMAL(10, 2) NOT NULL,
    current_stock DECIMAL(10, 2) NOT NULL,
    min_stock_level DECIMAL(10, 2) NOT NULL
);

-- Sales table
CREATE TABLE sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT,
    fuel_type_id INT,
    liters DECIMAL(10, 2) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('cash', 'card', 'mobile') NOT NULL,
    sale_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (fuel_type_id) REFERENCES fuel_types(id)
);

-- Inventory logs table
CREATE TABLE inventory_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fuel_type_id INT,
    action ENUM('restock', 'sale', 'adjustment') NOT NULL,
    quantity DECIMAL(10, 2) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT,
    FOREIGN KEY (fuel_type_id) REFERENCES fuel_types(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Insert sample data
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$somehashedpassword', 'admin@station.com', 'admin');

INSERT INTO fuel_types (name, price_per_liter, current_stock, min_stock_level) VALUES
('Regular', 2.50, 1000.00, 200.00),
('Premium', 3.00, 800.00, 150.00),
('Diesel', 2.75, 1200.00, 250.00);
