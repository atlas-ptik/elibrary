<?php
// Path: admin/buku/functions/update.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_buku = $_POST['id_buku'];
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

        $gambar_lama = $db->query("SELECT gambar FROM buku WHERE id_buku = '$id_buku'")->fetchColumn();

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
                if ($gambar_lama !== 'assets/images/buku-default.png') {
                    @unlink("../../../" . $gambar_lama);
                }
            }
        } else {
            $gambar = $gambar_lama;
        }

        $stmt = $db->prepare("
            UPDATE buku SET 
                id_kategori = ?, 
                id_rak = ?, 
                judul = ?, 
                penulis = ?, 
                penerbit = ?, 
                tahun_terbit = ?, 
                isbn = ?, 
                jumlah_halaman = ?, 
                stok = ?, 
                kelas_fokus = ?, 
                jurusan_fokus = ?, 
                gambar = ?
            WHERE id_buku = ?
        ");

        $stmt->execute([
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
            $gambar,
            $id_buku
        ]);

        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (
                id_log, tipe_pengguna, id_pengguna, aktivitas, detail
            ) VALUES (
                UUID(), 'admin', ?, 'mengubah data buku', ?
            )
        ");
        $log_stmt->execute([$admin_id, "Mengubah data buku: $judul"]);

        $_SESSION['success'] = "Data buku berhasil diperbarui";
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Gagal memperbarui data buku: " . $e->getMessage();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
