<?php
// Path: admin/auth/signup.php

require_once "../../globals/config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama_lengkap = filter_input(INPUT_POST, 'nama_lengkap', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $no_telepon = filter_input(INPUT_POST, 'no_telepon', FILTER_SANITIZE_SPECIAL_CHARS);
    
    try {
        $stmt = $db->prepare("
            INSERT INTO admin (
                id_admin, username, password, nama_lengkap, 
                email, no_telepon
            ) VALUES (
                UUID(), ?, ?, ?, ?, ?
            )
        ");
        
        $stmt->execute([
            $username, $password, $nama_lengkap, 
            $email, $no_telepon
        ]);
        
        echo "Admin berhasil didaftarkan";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup Admin</title>
</head>
<body>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="tel" name="no_telepon" placeholder="No Telepon"><br>
        <button type="submit">Daftar</button>
    </form>
</body>
</html>