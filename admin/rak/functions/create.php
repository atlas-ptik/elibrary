<?php
// Path: admin/rak/functions/create.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nomor_rak = trim($_POST['nomor_rak']);
        $lokasi = trim($_POST['lokasi']);
        $kapasitas = (int)$_POST['kapasitas'];
        $keterangan = trim($_POST['keterangan'] ?? '');

        if ($kapasitas < 1) {
            throw new Exception("Kapasitas rak harus lebih dari 0");
        }

        $stmt = $db->prepare("INSERT INTO rak_buku (id_rak, nomor_rak, lokasi, kapasitas, keterangan) VALUES (UUID(), ?, ?, ?, ?)");
        $stmt->execute([$nomor_rak, $lokasi, $kapasitas, $keterangan]);

        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (id_log, tipe_pengguna, id_pengguna, aktivitas, detail)
            VALUES (UUID(), 'admin', ?, 'menambahkan rak buku', ?)
        ");
        $log_stmt->execute([$admin_id, "Menambahkan rak buku: $nomor_rak"]);

        $_SESSION['success'] = "Rak buku berhasil ditambahkan";
        header("Location: ../index.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Gagal menambahkan rak buku: " . $e->getMessage();
        header("Location: ../index.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../index.php");
        exit;
    }
} else {
    header("Location: ../index.php");
    exit;
}