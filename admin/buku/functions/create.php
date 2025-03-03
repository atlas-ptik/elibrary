<?php
// Path: admin/buku/functions/create.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_buku = uniqid('BK-');
        $judul = trim($_POST['judul']);
        $penulis = trim($_POST['penulis'] ?? '');
        $penerbit = trim($_POST['penerbit'] ?? '');
        $tahun_terbit = $_POST['tahun_terbit'] ?? null;
        $isbn = trim($_POST['isbn'] ?? '');
        $jumlah_halaman = $_POST['jumlah_halaman'] ?? null;
        $id_kategori = $_POST['id_kategori'];
        $id_rak = $_POST['id_rak'];
        $stok = (int)$_POST['stok'];
        $kelas_fokus = $_POST['kelas_fokus'];
        $jurusan_fokus = $_POST['jurusan_fokus'];

        $gambar = 'assets/images/buku-default.png';

        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['gambar'];
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 2 * 1024 * 1024;

            if (!in_array($file['type'], $allowed_types)) {
                throw new Exception("Tipe file tidak valid. Harap upload gambar (JPG, PNG, GIF)");
            }

            if ($file['size'] > $max_size) {
                throw new Exception("Ukuran file terlalu besar. Maksimal 2MB");
            }

            $upload_dir = "../../../assets/images/buku/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $id_buku . '.' . $extension;
            $destination = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $gambar = 'assets/images/buku/' . $filename;
            }
        }

        $stmt = $db->prepare("
            INSERT INTO buku (
                id_buku, id_kategori, id_rak, judul, penulis, penerbit, 
                tahun_terbit, isbn, jumlah_halaman, stok, kelas_fokus, 
                jurusan_fokus, gambar
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ");

        $stmt->execute([
            $id_buku,
            $id_kategori,
            $id_rak,
            $judul,
            $penulis,
            $penerbit,
            $tahun_terbit,
            $isbn,
            $jumlah_halaman,
            $stok,
            $kelas_fokus,
            $jurusan_fokus,
            $gambar
        ]);

        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (
                id_log, tipe_pengguna, id_pengguna, aktivitas, detail
            ) VALUES (
                UUID(), 'admin', ?, 'menambahkan buku baru', ?
            )
        ");
        $log_stmt->execute([$admin_id, "Menambahkan buku: $judul"]);

        $_SESSION['success'] = "Buku berhasil ditambahkan";
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Gagal menambahkan buku: " . $e->getMessage();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
