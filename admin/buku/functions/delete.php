<?php
// Path: admin/buku/functions/delete.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_buku = $_POST['id_buku'];

        $db->beginTransaction();

        $get_book = $db->prepare("SELECT judul, gambar FROM buku WHERE id_buku = ?");
        $get_book->execute([$id_buku]);
        $book = $get_book->fetch();

        if (!$book) {
            throw new Exception("Buku tidak ditemukan");
        }

        $check_loans = $db->prepare("
            SELECT COUNT(*) FROM peminjaman 
            WHERE id_buku = ? AND status = 'dipinjam'
        ");
        $check_loans->execute([$id_buku]);

        if ($check_loans->fetchColumn() > 0) {
            throw new Exception("Tidak dapat menghapus buku karena masih ada peminjaman aktif");
        }

        $delete_stmt = $db->prepare("DELETE FROM buku WHERE id_buku = ?");
        $delete_stmt->execute([$id_buku]);

        if ($book['gambar'] !== 'assets/images/buku-default.png') {
            @unlink("../../../" . $book['gambar']);
        }

        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (
                id_log, tipe_pengguna, id_pengguna, aktivitas, detail
            ) VALUES (
                UUID(), 'admin', ?, 'menghapus buku', ?
            )
        ");
        $log_stmt->execute([$admin_id, "Menghapus buku: " . $book['judul']]);

        $db->commit();

        $_SESSION['success'] = "Buku berhasil dihapus";
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'] = "Gagal menghapus buku: " . $e->getMessage();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
