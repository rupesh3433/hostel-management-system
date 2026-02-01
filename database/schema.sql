CREATE DATABASE IF NOT EXISTS hostel_management;
USE hostel_management;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Rooms table
CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(20) NOT NULL,
    type ENUM('Single', 'Double', 'Triple', 'Dorm') NOT NULL,
    capacity INT NOT NULL,
    floor INT DEFAULT 1,
    price DECIMAL(10,2) DEFAULT 0.00,
    description TEXT,
    status ENUM('Available', 'Booked', 'Maintenance') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert default admin user (password: admin@12345)
INSERT INTO users (name, email, password, role) VALUES 
('Admin User', 'admin@admin.com', '$2y$10$Tc7yO2mSd8/.kYxWGGQ.2.ja0AUcHsn6nQTzKq.Op7J1l6U6O1eP.', 'admin');

-- Insert sample rooms
INSERT INTO rooms (room_number, type, capacity, floor, price, description, status) VALUES
('101', 'Single', 1, 1, 5000.00, 'Cozy single room with attached bathroom', 'Available'),
('102', 'Double', 2, 1, 8000.00, 'Spacious double room with balcony', 'Available'),
('201', 'Triple', 3, 2, 10000.00, 'Large triple room with study area', 'Booked'),
('202', 'Dorm', 4, 2, 12000.00, 'Modern dorm with bunk beds', 'Available');