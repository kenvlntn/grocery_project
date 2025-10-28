CREATE DATABASE grocery_db;
USE grocery_db;

-- CATEGORIES TABLE
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT
);

INSERT INTO categories (category_name, description) VALUES
('Dairy', 'Milk-based products and derivatives'),
('Fruits', 'Fresh fruits and seasonal produce'),
('Pantry', 'Dry goods and cooking essentials'),
('Beverages', 'Soft drinks, juices, and bottled water'),
('Bakery', 'Bread, pastries, and baked goods');

-- SUPPLIERS TABLE
CREATE TABLE suppliers (
    supplier_id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20),
    address VARCHAR(150)
);

INSERT INTO suppliers (supplier_name, contact_number, address) VALUES
('Alpine Suppliers', '09171234567', 'Makati City'),
('SweetGold Co.', '09221234567', 'Quezon City'),
('FarmFresh Ltd.', '09351234567', 'Tagaytay'),
('Golden Harvest', '09481234567', 'Laguna'),
('Tropical Suppliers', '09561234567', 'Davao City');

-- GROCERY ITEMS TABLE
CREATE TABLE grocery_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    category_id INT,
    supplier_id INT,
    price DECIMAL(10,2),
    unit VARCHAR(20),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
);

INSERT INTO grocery_items (item_name, category_id, supplier_id, price, unit)
VALUES 
('Fresh Milk', 1, 1, 85.50, '1L'),
('Brown Sugar', 3, 2, 60.00, '1kg'),
('Eggs (Dozen)', 3, 3, 90.00, '12pcs'),
('Canola Oil 1L', 3, 4, 120.00, '1L'),
('Bananas', 2, 5, 45.00, '1kg'),
('Butter', 1, 1, 110.00, '250g'),
('Tomato Sauce 250g', 3, 2, 35.75, '250g');

-- INVENTORY TABLE
CREATE TABLE inventory (
    inventory_id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT,
    quantity INT DEFAULT 0,
    last_restocked DATE,
    FOREIGN KEY (item_id) REFERENCES grocery_items(item_id)
);

INSERT INTO inventory (item_id, quantity, last_restocked) VALUES
(1, 50, '2025-10-10'),
(2, 120, '2025-10-12'),
(3, 80, '2025-10-14'),
(4, 40, '2025-10-15'),
(5, 100, '2025-10-18'),
(6, 35, '2025-10-19'),
(7, 60, '2025-10-20');

-- EMPLOYEES TABLE
CREATE TABLE employees (
    employee_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    position VARCHAR(50),
    hire_date DATE,
    contact_number VARCHAR(20)
);

INSERT INTO employees (first_name, last_name, position, hire_date, contact_number) VALUES
('Ken', 'Valentin', 'Cashier', '2024-06-10', '09191234567'),
('Sophia', 'Cruz', 'Inventory Clerk', '2024-08-15', '09203456789'),
('Miko', 'Dela Rosa', 'Sales Associate', '2025-01-05', '09302345678');

-- PROMOTIONS TABLE
CREATE TABLE promotions (
    promo_id INT AUTO_INCREMENT PRIMARY KEY,
    promo_name VARCHAR(100),
    discount_percentage DECIMAL(5,2),
    start_date DATE,
    end_date DATE
);

INSERT INTO promotions (promo_name, discount_percentage, start_date, end_date) VALUES
('Holiday Discount', 10.00, '2025-12-01', '2025-12-31'),
('Weekend Sale', 5.00, '2025-10-25', '2025-10-27'),
('Buy 1 Take 1 - Dairy', 50.00, '2025-11-01', '2025-11-10');

-- ORDERS TABLE
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    customer_name VARCHAR(100),
    total_amount DECIMAL(10,2),
    promo_id INT NULL,
    FOREIGN KEY (employee_id) REFERENCES employees(employee_id),
    FOREIGN KEY (promo_id) REFERENCES promotions(promo_id)
);

INSERT INTO orders (employee_id, customer_name, total_amount, promo_id)
VALUES
(1, 'John Doe', 250.00, 2),
(2, 'Jane Smith', 180.00, NULL),
(3, 'Carlos Dizon', 360.00, 1);

-- ORDER DETAILS TABLE
CREATE TABLE order_details (
    order_detail_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    item_id INT,
    quantity INT,
    price DECIMAL(10,2),
    FOREIGN KEY (order_id) REFERENCES orders(order_id),
    FOREIGN KEY (item_id) REFERENCES grocery_items(item_id)
);

INSERT INTO order_details (order_id, item_id, quantity, price) VALUES
(1, 1, 2, 85.50),
(1, 2, 1, 60.00),
(2, 5, 3, 45.00),
(3, 3, 2, 90.00),
(3, 4, 1, 120.00);

-- shows total items sold per category with supplier and promo details
SELECT c.category_name, s.supplier_name, SUM(od.quantity) AS total_sold, 
       SUM(od.quantity * od.price) AS total_revenue,
       p.promo_name
FROM order_details od
JOIN grocery_items gi ON od.item_id = gi.item_id
JOIN categories c ON gi.category_id = c.category_id
JOIN suppliers s ON gi.supplier_id = s.supplier_id
LEFT JOIN orders o ON od.order_id = o.order_id
LEFT JOIN promotions p ON o.promo_id = p.promo_id
GROUP BY c.category_name, s.supplier_name, p.promo_name;

