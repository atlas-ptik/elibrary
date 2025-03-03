<?php
// Path: siswa/riwayat/hapus-semua.php

require_once "../../globals/config/database.php";
session_start();

// Pastikan user sudah login sebagai siswa
if (!isset($_SESSION['siswa'])) {
    header("Location: ../auth/login.php");
    exit();
}

$siswa_id = $_SESSION['siswa']['id'];

try {
    $query = $db->prepare("DELETE FROM riwayat_baca_ebook WHERE id_siswa = ?");
    $query->execute([$siswa_id]);

    // Catat log aktivitas
    $log_query = $db->prepare("
        INSERT INTO log_aktivitas (id_log, tipe_pengguna, id_pengguna, aktivitas, detail) 
        VALUES (UUID(), 'siswa', ?, 'Hapus semua riwayat baca', 'Menghapus semua riwayat baca e-book')
    ");
    $log_query->execute([$siswa_id]);

    header("Location: index.php?status=success&message=Semua riwayat berhasil dihapus");
    exit();
} catch (PDOException $e) {
    header("Location: index.php?status=error&message=Gagal menghapus semua riwayat: " . $e->getMessage());
    exit();
}
