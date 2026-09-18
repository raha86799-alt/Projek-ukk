CREATE DATABASE IF NOT EXISTS db_toko_atk;
USE db_toko_atk;

CREATE TABLE users (
 id_user INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) NOT NULL UNIQUE,
 password VARCHAR(255) NOT NULL,
 nama VARCHAR(100) NOT NULL,
 role VARCHAR(20) NOT NULL
);

CREATE TABLE kategori (
 id_kategori INT AUTO_INCREMENT PRIMARY KEY,
 nama_kategori VARCHAR(100) NOT NULL
);

CREATE TABLE produk (
 id_produk INT AUTO_INCREMENT PRIMARY KEY,
 id_kategori INT NOT NULL,
 kode_produk VARCHAR(20) NOT NULL UNIQUE,
 nama_produk VARCHAR(100) NOT NULL,
 harga DECIMAL(12,2) NOT NULL DEFAULT 0,
 stok INT NOT NULL DEFAULT 0,
 FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
);

CREATE TABLE pelanggan (
 id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
 nama VARCHAR(100) NOT NULL,
 alamat TEXT,
 no_hp VARCHAR(15)
);

CREATE TABLE penjualan (
 id_penjualan INT AUTO_INCREMENT PRIMARY KEY,
 id_user INT NOT NULL,
 id_pelanggan INT NULL,
 tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 total DECIMAL(12,2) NOT NULL DEFAULT 0,
 FOREIGN KEY (id_user) REFERENCES users(id_user),
 FOREIGN KEY (id_pelanggan) REFERENCES pelanggan(id_pelanggan) ON DELETE SET NULL
);

CREATE TABLE detail_penjualan (
 id_detail INT AUTO_INCREMENT PRIMARY KEY,
 id_penjualan INT NOT NULL,
 id_produk INT NOT NULL,
 jumlah INT NOT NULL,
 harga DECIMAL(12,2) NOT NULL,
 subtotal DECIMAL(12,2) NOT NULL,
 FOREIGN KEY (id_penjualan) REFERENCES penjualan(id_penjualan) ON DELETE CASCADE,
 FOREIGN KEY (id_produk) REFERENCES produk(id_produk)
);

INSERT INTO users (username,password,nama,role)
VALUES ('admin',SHA2('admin123',256),'Administrator','Admin');

INSERT INTO kategori (nama_kategori) VALUES
('Alat Tulis'),('Buku'),('Perlengkapan Kantor');
