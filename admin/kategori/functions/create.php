<?php
// Path: admin/kategori/functions/create.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nama_kategori = trim($_POST['nama_kategori']);
        $deskripsi = trim($_POST['deskripsi'] ?? '');

        $stmt = $db->prepare("INSERT INTO kategori_buku (id_kategori, nama_kategori, deskripsi) VALUES (UUID(), ?, ?)");
        $stmt->execute([$nama_kategori, $deskripsi]);

        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (id_log, tipe_pengguna, id_pengguna, aktivitas, detail)
            VALUES (UUID(), 'admin', ?, 'menambahkan kategori buku', ?)
        ");
        $log_stmt->execute([$admin_id, "Menambahkan kategori: $nama_kategori"]);

        $_SESSION['success'] = "Kategori berhasil ditambahkan";
        header("Location: ../index.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Gagal menambahkan kategori: " . $e->getMessage();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}
