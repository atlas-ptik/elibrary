<?php
// Path: siswa/riwayat/hapus.php

require_once "../../globals/config/database.php";
session_start();

// Pastikan user sudah login sebagai siswa
if (!isset($_SESSION['siswa'])) {
    header("Location: ../auth/login.php");
    exit();
}

$siswa_id = $_SESSION['siswa']['id'];

// Validasi input
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?status=error&message=ID riwayat tidak valid");
    exit();
}

$id_riwayat = $_GET['id'];

try {
    $query = $db->prepare("
        DELETE FROM riwayat_baca_ebook 
        WHERE id_riwayat = ? AND id_siswa = ?
    ");

    $query->execute([$id_riwayat, $siswa_id]);

    // Catat log aktivitas
    $log_query = $db->prepare("
        INSERT INTO log_aktivitas (id_log, tipe_pengguna, id_pengguna, aktivitas, detail) 
        VALUES (UUID(), 'siswa', ?, 'Hapus riwayat baca', 'Menghapus riwayat baca e-book')
    ");
    $log_query->execute([$siswa_id]);

    header("Location: index.php?status=success&message=Riwayat berhasil dihapus");
    exit();
} catch (PDOException $e) {
    header("Location: index.php?status=error&message=Gagal menghapus riwayat: " . $e->getMessage());
    exit();
}
