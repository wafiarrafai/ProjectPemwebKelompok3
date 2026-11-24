
CREATE DATABASE IF NOT EXISTS sehatin_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sehatin_db;

-- 1. Tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL
);

-- 2. Tabel article_categories
CREATE TABLE IF NOT EXISTS article_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- 3. Tabel articles
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    author_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    excerpt VARCHAR(300) NULL,
    content TEXT NOT NULL,
    status ENUM('draft', 'published')
        NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,

    CONSTRAINT fk_articles_category
        FOREIGN KEY (category_id)
        REFERENCES article_categories(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_articles_author
        FOREIGN KEY (author_id)
        REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

-- 4. Tabel facilities
CREATE TABLE IF NOT EXISTS facilities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    type VARCHAR(80) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(30) NULL,
    description TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL
);

-- 5. Tabel complaint_categories
CREATE TABLE IF NOT EXISTS complaint_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- 6. Tabel complaints
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('Menunggu', 'Diproses', 'Selesai')
        NOT NULL DEFAULT 'Menunggu',
    admin_note TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL,

    CONSTRAINT fk_complaints_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,

    CONSTRAINT fk_complaints_category
        FOREIGN KEY (category_id)
        REFERENCES complaint_categories(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

--Untuk isi kategori keluhan
INSERT IGNORE INTO complaint_categories (name)
VALUES
('Kesehatan umum'),
('Keluhan fisik'),
('Kesehatan mental'),
('Lainnya');

--Untuk isi kategori artikel
INSERT IGNORE INTO article_categories (name)
VALUES
    ('Kesehatan Umum'),
    ('Pola Hidup Sehat'),
    ('Kesehatan Mental'),
    ('Kesehatan Keluarga'),
    ('Nutrisi dan Gizi'),
    ('Lainnya');

/*
Membuat akun admin. Ini buat contoh aja. Kalo mau ubah sendiri juga bisa. Untuk password pake hash.
Password : admin1234
*/
INSERT INTO users (
    name,
    email,
    password,
    role,
    created_at,
    updated_at
) VALUES (
    'Admin SEHATIN',
    'admin@gmail.com',
    '$2y$10$9bVwRA8Jl8rqEzdpSOpgeeHXe6ZnHWvrQM4xudRqk3R1fwCuPNh0m',
    'admin',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    name = 'Admin SEHATIN',
    password = '$2y$10$9bVwRA8Jl8rqEzdpSOpgeeHXe6ZnHWvrQM4xudRqk3R1fwCuPNh0m',
    role = 'admin',
    updated_at = NOW();

/*
 * Akun demo untuk pengguna.
 * Email: user@gmail.com
 * Password: user1234
 */
INSERT INTO users (name, email, password, role, created_at, updated_at)
VALUES (
    'Pengguna Demo',
    'user@gmail.com',
    '$2y$10$G1gUdn4SgwOmfaZWpIcAmOqYG.k1McDmqaoKU96S2zOG1DMiSJGs6',
    'user',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    name = 'Pengguna Demo',
    password = '$2y$10$G1gUdn4SgwOmfaZWpIcAmOqYG.k1McDmqaoKU96S2zOG1DMiSJGs6',
    role = 'user',
    updated_at = NOW();

-- Data fasilitas contoh. Nama yang sama tidak dimasukkan lagi saat file diimpor ulang.
INSERT INTO facilities (name, type, address, phone, description)
SELECT 'Puskesmas Sejahtera', 'Puskesmas', 'Jl. Kesehatan No. 12, Purwokerto', NULL,
       'Data contoh direktori SEHATIN.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM facilities WHERE name = 'Puskesmas Sejahtera');

INSERT INTO facilities (name, type, address, phone, description)
SELECT 'Klinik Bersama', 'Klinik', 'Jl. Melati No. 8, Purwokerto', NULL,
       'Data contoh direktori SEHATIN.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM facilities WHERE name = 'Klinik Bersama');

INSERT INTO facilities (name, type, address, phone, description)
SELECT 'RSU Contoh Sehat', 'Rumah sakit', 'Area Purwokerto', NULL,
       'Data contoh direktori SEHATIN.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM facilities WHERE name = 'RSU Contoh Sehat');

INSERT INTO facilities (name, type, address, phone, description)
SELECT 'Puskesmas Sehat Mandiri', 'Puskesmas', 'Jl. Merdeka, Purwokerto', NULL,
       'Data contoh direktori SEHATIN.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM facilities WHERE name = 'Puskesmas Sehat Mandiri');

INSERT INTO facilities (name, type, address, phone, description)
SELECT 'Klinik Medika', 'Klinik', 'Jl. Jenderal Sudirman, Purwokerto', NULL,
       'Data contoh direktori SEHATIN.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM facilities WHERE name = 'Klinik Medika');

INSERT INTO facilities (name, type, address, phone, description)
SELECT 'Rumah Sakit Harapan', 'Rumah sakit', 'Purwokerto', NULL,
       'Data contoh direktori SEHATIN.'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM facilities WHERE name = 'Rumah Sakit Harapan');

-- Artikel contoh untuk daftar kelola artikel admin.
INSERT INTO articles (category_id, author_id, title, excerpt, content, status, created_at, updated_at)
SELECT category.id, admin.id,
       'Mempersiapkan informasi sebelum berkunjung ke fasilitas kesehatan',
       'Catat informasi yang ingin ditanyakan sebelum menghubungi fasilitas kesehatan.',
       'Artikel ini merupakan data contoh untuk praktikum. Siapkan pertanyaan dan informasi umum yang relevan sebelum menghubungi fasilitas kesehatan. Hubungi fasilitas secara langsung untuk memastikan layanan dan jam operasional.',
       'published', NOW(), NOW()
FROM article_categories AS category
JOIN users AS admin ON admin.email = 'admin@gmail.com'
WHERE category.name = 'Kesehatan Umum'
  AND NOT EXISTS (
      SELECT 1 FROM articles
      WHERE title = 'Mempersiapkan informasi sebelum berkunjung ke fasilitas kesehatan'
  );

INSERT INTO articles (category_id, author_id, title, excerpt, content, status, created_at, updated_at)
SELECT category.id, admin.id,
       'Membangun kebiasaan istirahat yang teratur',
       'Contoh bacaan umum tentang rutinitas istirahat sehari-hari.',
       'Artikel ini merupakan data contoh untuk praktikum. Rutinitas yang konsisten dapat membantu seseorang mengatur waktu istirahat. Sesuaikan kebiasaan dengan kebutuhan pribadi dan mintalah saran tenaga kesehatan bila diperlukan.',
       'published', NOW(), NOW()
FROM article_categories AS category
JOIN users AS admin ON admin.email = 'admin@gmail.com'
WHERE category.name = 'Pola Hidup Sehat'
  AND NOT EXISTS (
      SELECT 1 FROM articles
      WHERE title = 'Membangun kebiasaan istirahat yang teratur'
  );

INSERT INTO articles (category_id, author_id, title, excerpt, content, status, created_at, updated_at)
SELECT category.id, admin.id,
       'Menyimpan informasi kesehatan keluarga',
       'Cara sederhana menyiapkan informasi keluarga sebelum mencari layanan.',
       'Artikel ini merupakan data contoh untuk praktikum. Simpan informasi dasar yang diperlukan secara aman agar mudah ditemukan saat dibutuhkan. Hindari membagikan informasi pribadi kepada pihak yang tidak berkepentingan.',
       'published', NOW(), NOW()
FROM article_categories AS category
JOIN users AS admin ON admin.email = 'admin@gmail.com'
WHERE category.name = 'Kesehatan Keluarga'
  AND NOT EXISTS (
      SELECT 1 FROM articles
      WHERE title = 'Menyimpan informasi kesehatan keluarga'
  );

-- Keluhan di bawah hanya data demo untuk mengisi dashboard dan riwayat akun user.
INSERT INTO complaints (user_id, category_id, title, description, status, admin_note, created_at, updated_at)
SELECT demo.id, category.id,
       'Contoh keluhan menunggu',
       'Data demo untuk melihat tampilan status Menunggu pada riwayat keluhan.',
       'Menunggu', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), NULL
FROM users AS demo
JOIN complaint_categories AS category ON category.name = 'Kesehatan umum'
WHERE demo.email = 'user@gmail.com'
  AND NOT EXISTS (
      SELECT 1 FROM complaints
      WHERE user_id = demo.id AND title = 'Contoh keluhan menunggu'
  );

INSERT INTO complaints (user_id, category_id, title, description, status, admin_note, created_at, updated_at)
SELECT demo.id, category.id,
       'Contoh keluhan diproses',
       'Data demo untuk melihat contoh tindak lanjut administratif.',
       'Diproses', 'Data contoh: laporan sedang ditinjau oleh admin.',
       DATE_SUB(NOW(), INTERVAL 3 DAY), NOW()
FROM users AS demo
JOIN complaint_categories AS category ON category.name = 'Lainnya'
WHERE demo.email = 'user@gmail.com'
  AND NOT EXISTS (
      SELECT 1 FROM complaints
      WHERE user_id = demo.id AND title = 'Contoh keluhan diproses'
  );

INSERT INTO complaints (user_id, category_id, title, description, status, admin_note, created_at, updated_at)
SELECT demo.id, category.id,
       'Contoh keluhan selesai',
       'Data demo untuk melihat contoh keluhan yang sudah selesai dicatat.',
       'Selesai', 'Data contoh: tindak lanjut administratif sudah selesai.',
       DATE_SUB(NOW(), INTERVAL 5 DAY), NOW()
FROM users AS demo
JOIN complaint_categories AS category ON category.name = 'Keluhan fisik'
WHERE demo.email = 'user@gmail.com'
  AND NOT EXISTS (
      SELECT 1 FROM complaints
      WHERE user_id = demo.id AND title = 'Contoh keluhan selesai'
  );
