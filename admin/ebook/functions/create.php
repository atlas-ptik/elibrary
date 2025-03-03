<?php
// Path: admin/ebook/functions/create.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validasi file e-book
        if (!isset($_FILES['file_ebook']) || $_FILES['file_ebook']['error'] === UPLOAD_ERR_NO_FILE) {
            throw new Exception("File e-book harus diunggah");
        }

        $file_ebook = $_FILES['file_ebook'];
        if ($file_ebook['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error saat mengunggah file");
        }

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

        // Proses gambar jika diunggah
        $gambar_path = 'assets/images/buku-default.png'; // Default
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $gambar = $_FILES['gambar'];

            // Validasi tipe gambar
            $allowed_types = ['image/jpeg', 'image/png'];
            $gambar_type = mime_content_type($gambar['tmp_name']);
            if (!in_array($gambar_type, $allowed_types)) {
                throw new Exception("Format gambar harus JPG atau PNG");
            }

            // Validasi ukuran gambar (max 2MB)
            if ($gambar['size'] > 2 * 1024 * 1024) {
                throw new Exception("Ukuran gambar maksimal 2MB");
            }

            // Generate nama unik untuk gambar
            $img_extension = pathinfo($gambar['name'], PATHINFO_EXTENSION);
            $img_filename = uniqid() . '.' . $img_extension;
            $img_destination = "../../../assets/images/ebooks/" . $img_filename;
            $gambar_path = 'assets/images/ebooks/' . $img_filename;

            // Pindahkan gambar
            if (!move_uploaded_file($gambar['tmp_name'], $img_destination)) {
                throw new Exception("Gagal mengunggah gambar");
            }
        }

        // Pindahkan file PDF
        if (!move_uploaded_file($file_ebook['tmp_name'], $pdf_destination)) {
            // Hapus gambar jika sudah terupload
            if ($gambar_path !== 'assets/images/buku-default.png') {
                unlink("../../../" . $gambar_path);
            }
            throw new Exception("Gagal mengunggah file e-book");
        }

        // Simpan data ke database
        $stmt = $db->prepare("
            INSERT INTO ebook (
                id_ebook, judul, penulis, penerbit, 
                tahun_terbit, isbn, jumlah_halaman, file_path, gambar,
                kelas_fokus, jurusan_fokus
            ) VALUES (
                UUID(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
        ");

        $stmt->execute([
            trim($_POST['judul']),
            trim($_POST['penulis']),
            trim($_POST['penerbit']),
            $_POST['tahun_terbit'],
            trim($_POST['isbn'] ?? ''),
            $_POST['jumlah_halaman'] ?? null,
            'assets/ebooks/' . $pdf_filename,
            $gambar_path,
            $_POST['kelas_fokus'],
            $_POST['jurusan_fokus']
        ]);

        // Catat log aktivitas
        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (id_log, tipe_pengguna, id_pengguna, aktivitas, detail)
            VALUES (UUID(), 'admin', ?, 'menambahkan e-book', ?)
        ");
        $log_stmt->execute([$admin_id, "Menambahkan e-book: " . $_POST['judul']]);

        $_SESSION['success'] = "E-Book berhasil ditambahkan";
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
