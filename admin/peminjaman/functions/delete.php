<?php
// Path: admin/peminjaman/functions/delete.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_peminjaman = $_POST['id_peminjaman'];

        $db->beginTransaction();

        // Get information about the loan before deleting it
        $get_info = $db->prepare("
            SELECT 
                p.*,
                s.nama_lengkap as nama_siswa,
                b.judul as judul_buku,
                b.id_buku
            FROM peminjaman p
            JOIN siswa s ON p.id_siswa = s.id_siswa
            JOIN buku b ON p.id_buku = b.id_buku
            WHERE p.id_peminjaman = ?
        ");
        $get_info->execute([$id_peminjaman]);
        $info = $get_info->fetch();

        if (!$info) {
            throw new Exception("Data peminjaman tidak ditemukan");
        }

        // If the book is still being borrowed, restore the stock
        if ($info['status'] === 'dipinjam' || $info['status'] === 'terlambat') {
            $update_stok = $db->prepare("
                UPDATE buku 
                SET stok = stok + 1 
                WHERE id_buku = ?
            ");
            $update_stok->execute([$info['id_buku']]);
        }

        // Delete the loan record
        $stmt = $db->prepare("DELETE FROM peminjaman WHERE id_peminjaman = ?");
        $stmt->execute([$id_peminjaman]);

        // Log the activity
        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (
                id_log, tipe_pengguna, id_pengguna, aktivitas, detail
            ) VALUES (
                UUID(), 'admin', ?, 'menghapus peminjaman buku', ?
            )
        ");

        $log_stmt->execute([
            $admin_id,
            "Menghapus riwayat peminjaman buku {$info['judul_buku']} oleh {$info['nama_siswa']}"
        ]);

        $db->commit();

        $_SESSION['success'] = "Riwayat peminjaman buku berhasil dihapus";
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
