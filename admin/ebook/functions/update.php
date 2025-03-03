<?php
// Path: admin/ebook/functions/update.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_ebook = $_POST['id_ebook'];

        // Ambil data e-book yang ada
        $stmt = $db->prepare("SELECT file_path, gambar FROM ebook WHERE id_ebook = ?");
        $stmt->execute([$id_ebook]);
        $current_ebook = $stmt->fetch();

        if (!$current_ebook) {
            throw new Exception("E-Book tidak ditemukan");
        }

        $file_path = $current_ebook['file_path'];
        $gambar = $current_ebook['gambar'];

        // Proses file e-book baru jika ada
        if (isset($_FILES['file_ebook']) && $_FILES['file_ebook']['error'] === UPLOAD_ERR_OK) {
            $file_ebook = $_FILES['file_ebook'];

            // Validasi tipe file PDF
            $file_type = mime_content_type($file_ebook['tmp_name']);
            if ($file_type !== 'application/pdf') {
                throw new Exception("File harus berformat PDF");
            }

            // Validasi ukuran file (max 10MB)
            if ($file_ebook['size'] > 10 * 1024 * 1024) {
                throw new Exception("Ukuran file maksimal 10MB");
            }

            // Generate nama unik untuk file PDF
            $pdf_extension = pathinfo($file_ebook['name'], PATHINFO_EXTENSION);
            $pdf_filename = uniqid() . '.' . $pdf_extension;
            $pdf_destination = "../../../assets/ebooks/" . $pdf_filename;

            // Pindahkan file PDF baru
            if (!move_uploaded_file($file_ebook['tmp_name'], $pdf_destination)) {
                throw new Exception("Gagal mengunggah file e-book");
            }

            // Hapus file PDF lama
            if (file_exists("../../../" . $current_ebook['file_path'])) {
                unlink("../../../" . $current_ebook['file_path']);
            }

            $file_path = 'assets/ebooks/' . $pdf_filename;
        }

        // Proses gambar baru jika ada
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $gambar_file = $_FILES['gambar'];

            // Validasi tipe gambar
            $allowed_types = ['image/jpeg', 'image/png'];
            $gambar_type = mime_content_type($gambar_file['tmp_name']);
            if (!in_array($gambar_type, $allowed_types)) {
                throw new Exception("Format gambar harus JPG atau PNG");
            }

            // Validasi ukuran gambar (max 2MB)
            if ($gambar_file['size'] > 2 * 1024 * 1024) {
                throw new Exception("Ukuran gambar maksimal 2MB");
            }

            // Generate nama unik untuk gambar
            $img_extension = pathinfo($gambar_file['name'], PATHINFO_EXTENSION);
            $img_filename = uniqid() . '.' . $img_extension;
            $img_destination = "../../../assets/images/ebooks/" . $img_filename;

            // Pindahkan gambar baru
            if (!move_uploaded_file($gambar_file['tmp_name'], $img_destination)) {
                throw new Exception("Gagal mengunggah gambar");
            }

            // Hapus gambar lama jika bukan default
            if (
                $current_ebook['gambar'] !== 'assets/images/buku-default.png' &&
                file_exists("../../../" . $current_ebook['gambar'])
            ) {
                unlink("../../../" . $current_ebook['gambar']);
            }

            $gambar = 'assets/images/ebooks/' . $img_filename;
        }

        // Update database
        $stmt = $db->prepare("
            UPDATE ebook SET 
                judul = ?,
                penulis = ?,
                penerbit = ?,
                tahun_terbit = ?,
                isbn = ?,
                jumlah_halaman = ?,
                file_path = ?,
                gambar = ?,
                kelas_fokus = ?,
                jurusan_fokus = ?
            WHERE id_ebook = ?
        ");

        $stmt->execute([
            trim($_POST['judul']),
            trim($_POST['penulis']),
            trim($_POST['penerbit']),
            $_POST['tahun_terbit'],
            trim($_POST['isbn'] ?? ''),
            $_POST['jumlah_halaman'] ?? null,
            $file_path,
            $gambar,
            $_POST['kelas_fokus'],
            $_POST['jurusan_fokus'],
            $id_ebook
        ]);

        // Catat log aktivitas
        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (id_log, tipe_pengguna, id_pengguna, aktivitas, detail)
            VALUES (UUID(), 'admin', ?, 'mengubah e-book', ?)
        ");
        $log_stmt->execute([$admin_id, "Mengubah e-book: " . $_POST['judul']]);

        $_SESSION['success'] = "E-Book berhasil diperbarui";
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
