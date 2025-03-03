<?php
// Path: admin/kategori/functions/delete.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_kategori = $_POST['id_kategori'];

        // Cek apakah kategori masih digunakan
        $check_stmt = $db->prepare("
            SELECT COUNT(*) FROM buku WHERE id_kategori = ?
        ");
        $check_stmt->execute([$id_kategori]);

        if ($check_stmt->fetchColumn() > 0) {
            throw new Exception("Kategori tidak dapat dihapus karena masih digunakan oleh buku");
        }

        // Ambil nama kategori untuk log
        $get_name = $db->prepare("SELECT nama_kategori FROM kategori_buku WHERE id_kategori = ?");
        $get_name->execute([$id_kategori]);
        $nama_kategori = $get_name->fetchColumn();

        // Hapus kategori
        $stmt = $db->prepare("DELETE FROM kategori_buku WHERE id_kategori = ?");
        $stmt->execute([$id_kategori]);

        // Catat log
        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (
                id_log, tipe_pengguna, id_pengguna, aktivitas, detail
            ) VALUES (
                UUID(), 'admin', ?, 'menghapus kategori buku', ?
            )
        ");
        $log_stmt->execute([$admin_id, "Menghapus kategori: $nama_kategori"]);

        $_SESSION['success'] = "Kategori berhasil dihapus";
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Gagal menghapus kategori: " . $e->getMessage();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
