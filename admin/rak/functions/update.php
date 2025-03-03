<?php
// Path: admin/rak/functions/update.php

session_start();
require_once "../../../globals/config/database.php";
require_once "../../middleware/auth.php";

cekAuthAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_rak = $_POST['id_rak'];
        $nomor_rak = trim($_POST['nomor_rak']);
        $lokasi = trim($_POST['lokasi']);
        $kapasitas = (int)$_POST['kapasitas'];
        $keterangan = trim($_POST['keterangan'] ?? '');

        if ($kapasitas < 1) {
            throw new Exception("Kapasitas rak harus lebih dari 0");
        }

        // Cek jumlah buku yang ada di rak
        $check_stmt = $db->prepare("
            SELECT COUNT(*) as total_buku 
            FROM buku 
            WHERE id_rak = ?
        ");
        $check_stmt->execute([$id_rak]);
        $total_buku = $check_stmt->fetch()['total_buku'];

        if ($kapasitas < $total_buku) {
            throw new Exception("Kapasitas tidak boleh kurang dari jumlah buku yang ada ($total_buku buku)");
        }

        $stmt = $db->prepare("
            UPDATE rak_buku 
            SET nomor_rak = ?, lokasi = ?, kapasitas = ?, keterangan = ?
            WHERE id_rak = ?
        ");
        $stmt->execute([$nomor_rak, $lokasi, $kapasitas, $keterangan, $id_rak]);

        $admin_id = $_SESSION['admin_id'];
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (id_log, tipe_pengguna, id_pengguna, aktivitas, detail)
            VALUES (UUID(), 'admin', ?, 'mengubah rak buku', ?)
        ");
        $log_stmt->execute([$admin_id, "Mengubah rak buku: $nomor_rak"]);

        $_SESSION['success'] = "Rak buku berhasil diperbarui";
        header("Location: ../index.php");
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = "Gagal memperbarui rak buku: " . $e->getMessage();
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