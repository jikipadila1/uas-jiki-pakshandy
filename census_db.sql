-- Database Schema untuk Census Data System

-- Create Database
CREATE DATABASE IF NOT EXISTS census_db;
USE census_db;

-- Table: users
-- Untuk menyimpan data user yang bisa login
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: pelanggaran
-- Untuk menyimpan data pelanggaran (contraflow, overspeed, traffic jam)
CREATE TABLE pelanggaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipe_pelanggaran ENUM('contraflow', 'overspeed', 'traffic_jam') NOT NULL,
    lokasi VARCHAR(255) NOT NULL,
    tanggal DATETIME NOT NULL,
    deskripsi TEXT,
    foto VARCHAR(255),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Table: objek_melintas
-- Untuk menyimpan data objek yang melintas (truk, mobil, motor)
CREATE TABLE objek_melintas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipe_objek ENUM('truk', 'mobil', 'motor') NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    lokasi VARCHAR(255) NOT NULL,
    tanggal DATETIME NOT NULL,
    deskripsi TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default user untuk testing
-- Password: admin123 (hashed dengan password_hash PHP)
INSERT INTO users (username, email, password, full_name) VALUES
('admin', 'admin@census.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Insert sample data pelanggaran
INSERT INTO pelanggaran (tipe_pelanggaran, lokasi, tanggal, deskripsi, created_by) VALUES
('contraflow', 'Jl. Sudirman Km 10', '2025-01-09 08:30:00', 'Kendaraan melawan arus di pagi hari', 1),
('overspeed', 'Jl. Gatot Subroto Km 5', '2025-01-09 09:15:00', 'Kendaraan melebihi batas kecepatan', 1),
('traffic_jam', 'Jl. Thamrin Km 3', '2025-01-09 07:45:00', 'Kemacetan total akibat kelambatan', 1);

-- Insert sample data objek melintas
INSERT INTO objek_melintas (tipe_objek, jumlah, lokasi, tanggal, deskripsi, created_by) VALUES
('motor', 45, 'Jl. Sudirman Km 10', '2025-01-09 08:00:00', 'Dominasi sepeda motor di jam berangkat kerja', 1),
('mobil', 30, 'Jl. Gatot Subroto Km 5', '2025-01-09 09:00:00', 'Mobil pribadi dominan', 1),
('truk', 8, 'Jl. Tol Dalam Kota', '2025-01-09 10:00:00', 'Truk barang di luar jam sibuk', 1);
