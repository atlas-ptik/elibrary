<?php
// Path: admin/peminjaman/functions/delete-all.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['konfirmasi']) || $_POST['konfirmasi'] != 1) {
            throw new Exception("Konfirmasi diperlukan untuk menghapus semua data");
        }

        $db->beginTransaction();

        // Get count of records being deleted for logging
        $count_stmt = $db->query("SELECT COUNT(*) FROM peminjaman");
        $count = $count_stmt->fetchColumn();

        if ($count == 0) {
            throw new Exception("Tidak ada data peminjaman untuk dihapus");
        }

        // Update book stock for books that are still being borrowed
        $update_stok = $db->query("
            UPDATE buku b
            JOIN peminjaman p ON b.id_buku = p.id_buku
            SET b.stok = b.stok + 1
            WHERE p.status IN ('dipinjam', 'terlambat')
        ");

        // Delete all loan records
        $stmt = $db->query("DELETE FROM peminjaman");

        // Log the activity
        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (
                id_log, tipe_pengguna, id_pengguna, aktivitas, detail
            ) VALUES (
                UUID(), 'admin', ?, 'menghapus semua peminjaman buku', ?
            )
        ");

        $log_stmt->execute([
            $admin_id,
            "Menghapus semua riwayat peminjaman ({$count} data)"
        ]);

        $db->commit();

        $_SESSION['success'] = "Semua riwayat peminjaman buku berhasil dihapus";
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'] = "Gagal menghapus riwayat peminjaman: " . $e->getMessage();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
