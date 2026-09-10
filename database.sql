CREATE DATABASE IF NOT EXISTS db_project_api;
USE db_project_api;

-- Tabel User
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    no_hp VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'siswa') NOT NULL DEFAULT 'siswa',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Jalankan sekali jika tabel users sudah terlanjur dibuat sebelumnya.
ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('admin', 'siswa') NOT NULL DEFAULT 'siswa' AFTER password;

-- Tabel Log Notifikasi WhatsApp (Nilai Plus)
CREATE TABLE IF NOT EXISTS log_whatsapp (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    no_tujuan VARCHAR(20) NOT NULL,
    pesan TEXT NOT NULL,
    status ENUM('success', 'failed') NOT NULL,
    response TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);