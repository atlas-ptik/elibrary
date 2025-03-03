<?php
// Path: admin/middleware/auth.php

function cekAuthAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../auth/login.php");
        exit;
    }

    try {
        global $db;
        $stmt = $db->prepare("
            SELECT status_aktif 
            FROM admin 
            WHERE id_admin = ?
        ");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();

        if (!$admin || !$admin['status_aktif']) {
            session_destroy();
            header("Location: ../auth/login.php");
            exit;
        }
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        header("Location: ../auth/login.php");
        exit;
    }
}
?>