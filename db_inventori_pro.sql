-- =====================================================
-- DATABASE: db_inventori_pro
-- Sistem Inventori Raja Dekor
-- =====================================================

CREATE DATABASE IF NOT EXISTS db_inventori_pro;
USE db_inventori_pro;

-- =====================================================
-- TABEL: kategori
-- =====================================================
CREATE TABLE IF NOT EXISTS kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL: barang
-- =====================================================
CREATE TABLE IF NOT EXISTS barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(255) NOT NULL,
    harga INT NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0,
    tanggal_masuk DATE NOT NULL,
    gambar VARCHAR(255) DEFAULT 'default.jpg',
    created_by VARCHAR(100) DEFAULT NULL,
    id_kategori INT DEFAULT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL: users
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    level ENUM('admin', 'staff') DEFAULT 'staff',
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL: log_aktivitas
-- =====================================================
CREATE TABLE IF NOT EXISTS log_aktivitas (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    aksi VARCHAR(100) NOT NULL,
    detail TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- DATA AWAL: User Admin Default
-- Password: admin123 (MD5)
-- =====================================================
INSERT INTO users (username, password, nama_lengkap, email, level) VALUES
('admin', MD5('admin123'), 'Administrator', 'admin@rajadekor.com', 'admin');

-- =====================================================
-- DATA AWAL: Kategori Contoh
-- =====================================================
INSERT INTO kategori (nama_kategori) VALUES
('Cermin'),
('Bingkai Foto'),
('Hiasan Dinding'),
('Lampu Hias'),
('Wallpaper');
