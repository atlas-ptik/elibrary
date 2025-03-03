-- Path: database/create-tables.sql
-- Tabel untuk admin
CREATE TABLE admin (
    id_admin CHAR(36) PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    no_telepon VARCHAR(15),
    foto VARCHAR(255) NULL DEFAULT 'assets/images/default.jpg',
    status_aktif BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Tabel untuk siswa
CREATE TABLE siswa (
    id_siswa CHAR(36) PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nis VARCHAR(20) UNIQUE NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    no_telepon VARCHAR(15),
    foto VARCHAR(255) NULL DEFAULT 'assets/images/default.jpg',
    kelas ENUM('X', 'XI', 'XII') NOT NULL,
    jurusan ENUM('IPA', 'IPS', 'BAHASA', 'UMUM') NOT NULL,
    status_aktif BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Tabel untuk kategori buku
CREATE TABLE kategori_buku (
    id_kategori CHAR(36) PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Tabel untuk rak buku
CREATE TABLE rak_buku (
    id_rak CHAR(36) PRIMARY KEY,
    nomor_rak VARCHAR(10) NOT NULL,
    lokasi VARCHAR(100) NOT NULL,
    kapasitas INT NOT NULL,
    keterangan TEXT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Tabel untuk data buku fisik
CREATE TABLE buku (
    id_buku CHAR(36) PRIMARY KEY,
    id_kategori CHAR(36) NOT NULL,
    id_rak CHAR(36) NOT NULL,
    judul VARCHAR(255) NOT NULL,
    penulis VARCHAR(100),
    penerbit VARCHAR(100),
    tahun_terbit YEAR,
    isbn VARCHAR(20) NULL DEFAULT NULL,
    jumlah_halaman INT,
    stok INT NOT NULL DEFAULT 0,
    kelas_fokus ENUM('X', 'XI', 'XII', 'UMUM') NOT NULL,
    jurusan_fokus ENUM('IPA', 'IPS', 'BAHASA', 'UMUM') NOT NULL,
    gambar VARCHAR(255) NULL DEFAULT 'assets/images/buku-default.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori_buku(id_kategori),
    FOREIGN KEY (id_rak) REFERENCES rak_buku(id_rak)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Membuat UNIQUE index untuk ISBN (MariaDB/MySQL tidak mendukung WHERE clause pada CREATE INDEX)
ALTER TABLE buku
ADD UNIQUE INDEX idx_isbn_unique (isbn);
-- Tabel untuk data ebook (sudah dihapus kategori ebook)
CREATE TABLE ebook (
    id_ebook CHAR(36) PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    penulis VARCHAR(100) NOT NULL,
    penerbit VARCHAR(100) NOT NULL,
    tahun_terbit YEAR NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    jumlah_halaman INT,
    file_path VARCHAR(255) NOT NULL,
    gambar VARCHAR(255) NULL DEFAULT 'assets/images/buku-default.png',
    kelas_fokus ENUM('X', 'XI', 'XII', 'UMUM') NOT NULL,
    jurusan_fokus ENUM('IPA', 'IPS', 'BAHASA', 'UMUM') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Tabel untuk transaksi peminjaman
CREATE TABLE peminjaman (
    id_peminjaman CHAR(36) PRIMARY KEY,
    id_siswa CHAR(36) NOT NULL,
    id_buku CHAR(36) NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_jatuh_tempo DATE NOT NULL,
    tanggal_kembali DATE,
    status ENUM('dipinjam', 'dikembalikan', 'terlambat') NOT NULL DEFAULT 'dipinjam',
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_siswa) REFERENCES siswa(id_siswa),
    FOREIGN KEY (id_buku) REFERENCES buku(id_buku)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Tabel untuk riwayat baca ebook
CREATE TABLE riwayat_baca_ebook (
    id_riwayat CHAR(36) PRIMARY KEY,
    id_siswa CHAR(36) NOT NULL,
    id_ebook CHAR(36) NOT NULL,
    tanggal_baca TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_siswa) REFERENCES siswa(id_siswa),
    FOREIGN KEY (id_ebook) REFERENCES ebook(id_ebook)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Tabel untuk log aktivitas
CREATE TABLE log_aktivitas (
    id_log CHAR(36) PRIMARY KEY,
    tipe_pengguna ENUM('admin', 'siswa') NOT NULL,
    id_pengguna CHAR(36) NOT NULL,
    aktivitas VARCHAR(255) NOT NULL,
    detail TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- Menghapus catatan log aktivitas yang terkait dengan kategori ebook
DELETE FROM log_aktivitas
WHERE aktivitas LIKE '%kategori e-book%';