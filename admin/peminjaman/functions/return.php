<?php
// Path: admin/peminjaman/functions/return.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_peminjaman = $_POST['id_peminjaman'];
        $tanggal_kembali = $_POST['tanggal_kembali'];
        $keterangan = trim($_POST['keterangan'] ?? '');

        $db->beginTransaction();

        $get_peminjaman = $db->prepare("
            SELECT 
                p.*,
                s.nama_lengkap as nama_siswa,
                b.judul as judul_buku,
                DATEDIFF(?, p.tanggal_jatuh_tempo) as keterlambatan
            FROM peminjaman p
            JOIN siswa s ON p.id_siswa = s.id_siswa
            JOIN buku b ON p.id_buku = b.id_buku
            WHERE p.id_peminjaman = ?
        ");
        $get_peminjaman->execute([$tanggal_kembali, $id_peminjaman]);
        $peminjaman = $get_peminjaman->fetch();

        if (!$peminjaman) {
            throw new Exception("Data peminjaman tidak ditemukan");
        }

        if ($peminjaman['status'] === 'dikembalikan') {
            throw new Exception("Buku sudah dikembalikan sebelumnya");
        }

        $status = $peminjaman['keterlambatan'] > 0 ? 'terlambat' : 'dikembalikan';

        $stmt = $db->prepare("
            UPDATE peminjaman SET 
                tanggal_kembali = ?,
                keterangan = ?,
                status = ?
            WHERE id_peminjaman = ?
        ");

        $stmt->execute([
            $tanggal_kembali,
            $keterangan,
            $status,
            $id_peminjaman
        ]);

        $update_stok = $db->prepare("
            UPDATE buku 
            SET stok = stok + 1 
            WHERE id_buku = ?
        ");
        $update_stok->execute([$peminjaman['id_buku']]);

        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (
                id_log, tipe_pengguna, id_pengguna, aktivitas, detail
            ) VALUES (
                UUID(), 'admin', ?, 'memproses pengembalian buku', ?
            )
        ");

        $detail = "Memproses pengembalian buku {$peminjaman['judul_buku']} oleh {$peminjaman['nama_siswa']}";
        if ($status === 'terlambat') {
            $detail .= " (Terlambat {$peminjaman['keterlambatan']} hari)";
        }

        $log_stmt->execute([$admin_id, $detail]);

        $db->commit();

        $_SESSION['success'] = "Pengembalian buku berhasil diproses";
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'] = "Gagal memproses pengembalian: " . $e->getMessage();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
