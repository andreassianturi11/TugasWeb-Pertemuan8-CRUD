CREATE DATABASE IF NOT EXISTS inventaris_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE inventaris_db;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS log_aktivitas;
DROP TABLE IF EXISTS produk;
DROP TABLE IF EXISTS supplier;
DROP TABLE IF EXISTS kategori;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE supplier (
    id_supplier INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    kontak VARCHAR(50) DEFAULT NULL,
    alamat VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE produk (
    id_produk INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(150) NOT NULL,
    id_kategori INT NOT NULL,
    id_supplier INT NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    harga DECIMAL(12,2) NOT NULL DEFAULT 0,
    satuan VARCHAR(30) NOT NULL DEFAULT 'pcs',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_produk_kategori FOREIGN KEY (id_kategori)
        REFERENCES kategori(id_kategori)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_produk_supplier FOREIGN KEY (id_supplier)
        REFERENCES supplier(id_supplier)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE log_aktivitas (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    aksi VARCHAR(20) NOT NULL,
    nama_produk VARCHAR(150) NOT NULL,
    keterangan VARCHAR(255) DEFAULT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Alat Tulis Kantor', 'Perlengkapan tulis dan kebutuhan kantor'),
('Elektronik', 'Perangkat dan aksesoris elektronik'),
('Furniture', 'Perabotan kantor dan rumah tangga'),
('Bahan Baku', 'Bahan mentah untuk produksi'),
('Kebersihan', 'Peralatan dan bahan kebersihan');

INSERT INTO supplier (nama_supplier, kontak, alamat) VALUES
('CV Sumber Makmur', '0812-3456-7890', 'Jl. Merdeka No. 10, Medan'),
('PT Global Elektronik', '0813-9988-7766', 'Jl. Gatot Subroto No. 45, Medan'),
('UD Jaya Furniture', '0811-2233-4455', 'Jl. Sisingamangaraja No. 88, Medan'),
('CV Bahan Baku Sejahtera', '0857-1122-3344', 'Jl. Setia Budi No. 21, Medan'),
('PT Bersih Sentosa', '0821-5566-7788', 'Jl. Krakatau No. 5, Medan');

INSERT INTO produk (nama_produk, id_kategori, id_supplier, stok, harga, satuan) VALUES
('Pulpen Standard AE7', 1, 1, 150, 2500.00, 'pcs'),
('Kertas HVS A4 80gr', 1, 1, 80, 45000.00, 'rim'),
('Keyboard Mechanical K1', 2, 2, 25, 350000.00, 'unit'),
('Kursi Kantor Ergonomis', 3, 3, 12, 850000.00, 'unit'),
('Cairan Pembersih Lantai', 5, 5, 40, 18000.00, 'botol');
