<?php
// Path: admin/ebook/functions/delete.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_ebook = $_POST['id_ebook'];

        // Ambil informasi file sebelum menghapus
        $stmt = $db->prepare("
            SELECT judul, file_path, gambar 
            FROM ebook 
            WHERE id_ebook = ?
        ");
        $stmt->execute([$id_ebook]);
        $ebook = $stmt->fetch();

        if (!$ebook) {
            throw new Exception("E-Book tidak ditemukan");
        }

        // Mulai transaksi
        $db->beginTransaction();

        // Hapus riwayat baca
        $stmt = $db->prepare("DELETE FROM riwayat_baca_ebook WHERE id_ebook = ?");
        $stmt->execute([$id_ebook]);

        // Hapus data e-book
        $stmt = $db->prepare("DELETE FROM ebook WHERE id_ebook = ?");
        $stmt->execute([$id_ebook]);

        // Catat log aktivitas
        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (id_log, tipe_pengguna, id_pengguna, aktivitas, detail)
            VALUES (UUID(), 'admin', ?, 'menghapus e-book', ?)
        ");
        $log_stmt->execute([$admin_id, "Menghapus e-book: " . $ebook['judul']]);

        // Commit transaksi
        $db->commit();

        // Hapus file PDF
        if (file_exists("../../../" . $ebook['file_path'])) {
            unlink("../../../" . $ebook['file_path']);
        }

        // Hapus gambar jika bukan default
        if (
            $ebook['gambar'] !== 'assets/images/buku-default.png' &&
            file_exists("../../../" . $ebook['gambar'])
        ) {
            unlink("../../../" . $ebook['gambar']);
        }

        $_SESSION['success'] = "E-Book berhasil dihapus";
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        // Rollback transaksi jika terjadi error
        if ($db->inTransaction()) {
            $db->rollBack();
        }

        $_SESSION['error'] = "Gagal menghapus e-book: " . $e->getMessage();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
