<?php
// Path: admin/auth/logout.php

session_start();
require_once "../../globals/config/database.php";

if (isset($_SESSION['admin_id'])) {
    try {
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (
                id_log, tipe_pengguna, id_pengguna, 
                aktivitas, detail
            ) VALUES (
                UUID(), 'admin', ?, 
                'logout', 'Admin melakukan logout dari sistem'
            )
        ");
        $log_stmt->execute([$_SESSION['admin_id']]);
    } catch (PDOException $e) {
        // Continue with logout even if logging fails
    }
}

session_destroy();
header("Location: login.php");
exit;
