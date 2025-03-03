<?php
// Path: admin/rak/functions/delete.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_rak = $_POST['id_rak'];

        // Cek apakah rak masih digunakan
        $check_stmt = $db->prepare("
            SELECT COUNT(*) FROM buku WHERE id_rak = ?
        ");
        $check_stmt->execute([$id_rak]);

        if ($check_stmt->fetchColumn() > 0) {
            throw new Exception("Rak tidak dapat dihapus karena masih digunakan oleh buku");
        }

        // Ambil nomor rak untuk log
        $get_nomor = $db->prepare("SELECT nomor_rak FROM rak_buku WHERE id_rak = ?");
        $get_nomor->execute([$id_rak]);
        $nomor_rak = $get_nomor->fetchColumn();

        // Hapus rak
        $stmt = $db->prepare("DELETE FROM rak_buku WHERE id_rak = ?");
        $stmt->execute([$id_rak]);

        // Catat log
        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (id_log, tipe_pengguna, id_pengguna, aktivitas, detail)
            VALUES (UUID(), 'admin', ?, 'menghapus rak buku', ?)
        ");
        $log_stmt->execute([$admin_id, "Menghapus rak buku: $nomor_rak"]);

        $_SESSION['success'] = "Rak buku berhasil dihapus";
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = "Gagal menghapus rak buku: " . $e->getMessage();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
