<?php
// Path: admin/peminjaman/functions/create.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_peminjaman = uniqid('PJ-');
        $id_siswa = trim($_POST['id_siswa']);
        $id_buku = trim($_POST['id_buku']);
        $tanggal_pinjam = $_POST['tanggal_pinjam'];
        $tanggal_jatuh_tempo = $_POST['tanggal_jatuh_tempo'];
        $keterangan = trim($_POST['keterangan'] ?? '');

        $db->beginTransaction();

        $cek_stok = $db->prepare("SELECT stok FROM buku WHERE id_buku = ?");
        $cek_stok->execute([$id_buku]);
        $stok = $cek_stok->fetchColumn();

        if ($stok < 1) {
            throw new Exception("Stok buku tidak mencukupi");
        }

        $cek_peminjaman = $db->prepare("
            SELECT COUNT(*) FROM peminjaman 
            WHERE id_siswa = ? AND status = 'dipinjam'
        ");
        $cek_peminjaman->execute([$id_siswa]);
        $sedang_pinjam = $cek_peminjaman->fetchColumn();

        if ($sedang_pinjam >= 3) {
            throw new Exception("Siswa sudah meminjam 3 buku dan tidak dapat meminjam lagi");
        }

        $cek_keterlambatan = $db->prepare("
            SELECT COUNT(*) FROM peminjaman 
            WHERE id_siswa = ? AND status = 'terlambat'
        ");
        $cek_keterlambatan->execute([$id_siswa]);
        $ada_keterlambatan = $cek_keterlambatan->fetchColumn();

        if ($ada_keterlambatan > 0) {
            throw new Exception("Siswa memiliki peminjaman yang terlambat dikembalikan");
        }

        $stmt = $db->prepare("
            INSERT INTO peminjaman (
                id_peminjaman, id_siswa, id_buku, tanggal_pinjam, 
                tanggal_jatuh_tempo, keterangan, status
            ) VALUES (
                ?, ?, ?, ?, ?, ?, 'dipinjam'
            )
        ");

        $stmt->execute([
            $id_peminjaman,
            $id_siswa,
            $id_buku,
            $tanggal_pinjam,
            $tanggal_jatuh_tempo,
            $keterangan
        ]);

        $update_stok = $db->prepare("
            UPDATE buku SET stok = stok - 1 WHERE id_buku = ?
        ");
        $update_stok->execute([$id_buku]);

        $get_info = $db->prepare("
            SELECT 
                s.nama_lengkap as nama_siswa,
                b.judul as judul_buku
            FROM peminjaman p
            JOIN siswa s ON p.id_siswa = s.id_siswa
            JOIN buku b ON p.id_buku = b.id_buku
            WHERE p.id_peminjaman = ?
        ");
        $get_info->execute([$id_peminjaman]);
        $info = $get_info->fetch();

        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (
                id_log, tipe_pengguna, id_pengguna, aktivitas, detail
            ) VALUES (
                UUID(), 'admin', ?, 'menambahkan peminjaman buku', ?
            )
        ");
        $log_stmt->execute([
            $admin_id,
            "Menambahkan peminjaman buku {$info['judul_buku']} oleh {$info['nama_siswa']}"
        ]);

        $db->commit();

        $_SESSION['success'] = "Peminjaman buku berhasil ditambahkan";
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        $db->rollBack();
        $_SESSION['error'] = "Gagal menambahkan peminjaman: " . $e->getMessage();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}