-- Schema for my project
CREATE DATABASE IF NOT EXISTS sales_db;
USE sales_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50),
    product_name VARCHAR(255),
    category VARCHAR(100),
    region VARCHAR(100),
    sales DECIMAL(12,2),
    profit DECIMAL(12,2),
    order_date DATE
);
//Just Inpot this in xaamp or any other sql database to create the required database and tables for the project.
//Make sure to change the database connection settings in the project configuration to connect to this database.
// Enjoy!