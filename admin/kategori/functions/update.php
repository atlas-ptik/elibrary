<?php
// Path: admin/kategori/functions/update.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_kategori = $_POST['id_kategori'];
        $nama_kategori = trim($_POST['nama_kategori']);
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        $stmt = $db->prepare("
            UPDATE kategori_buku 
            SET nama_kategori = ?, deskripsi = ? 
            WHERE id_kategori = ?
        ");
        $stmt->execute([$nama_kategori, $deskripsi, $id_kategori]);

        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (
                id_log, tipe_pengguna, id_pengguna, aktivitas, detail
            ) VALUES (
                UUID(), 'admin', ?, 'mengubah kategori buku', ?
            )
        ");
        $log_stmt->execute([$admin_id, "Mengubah kategori: $nama_kategori"]);

        $_SESSION['success'] = "Kategori berhasil diperbarui";
        header("Location: ../index.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Gagal memperbarui kategori: " . $e->getMessage();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
