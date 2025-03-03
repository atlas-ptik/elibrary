<?php
// Path: admin/admin/tambah.php

session_start();
require_once "../../globals/config/database.php";
require_once "../middleware/auth.php";
require_once "../layouts/admin-layout.php";

// Cek autentikasi
cekAuthAdmin();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama_lengkap = filter_input(INPUT_POST, 'nama_lengkap', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $no_telepon = filter_input(INPUT_POST, 'no_telepon', FILTER_SANITIZE_SPECIAL_CHARS);

    try {
        // Cek username dan email unique
        $check = $db->prepare("SELECT id_admin FROM admin WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);

        if ($check->rowCount() > 0) {
            $error = "Username atau email sudah digunakan!";
        } else {
            // Upload foto jika ada
            $foto = 'assets/images/default.jpg';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['image/jpeg', 'image/png'];
                $uploaded = $_FILES['foto'];

                if (!in_array($uploaded['type'], $allowed)) {
                    $error = "Tipe file tidak didukung! Gunakan JPG atau PNG.";
                } else if ($uploaded['size'] > 2 * 1024 * 1024) {
                    $error = "Ukuran file terlalu besar! Maksimal 2MB.";
                } else {
                    $ext = pathinfo($uploaded['name'], PATHINFO_EXTENSION);
                    $filename = 'admin_' . time() . '.' . $ext;
                    $destination = '../../assets/images/admin/' . $filename;

                    if (!is_dir('../../assets/images/admin')) {
                        mkdir('../../assets/images/admin', 0777, true);
                    }

                    if (move_uploaded_file($uploaded['tmp_name'], $destination)) {
                        $foto = 'assets/images/admin/' . $filename;
                    } else {
                        $error = "Gagal mengupload foto!";
                    }
                }
            }

            if (!isset($error)) {
                // Insert data admin baru
                $stmt = $db->prepare("
                    INSERT INTO admin (
                        id_admin, username, password, 
                        nama_lengkap, email, no_telepon, 
                        foto, status_aktif
                    ) VALUES (
                        UUID(), ?, ?, 
                        ?, ?, ?, 
                        ?, TRUE
                    )
                ");

                $stmt->execute([
                    $username,
                    $password,
                    $nama_lengkap,
                    $email,
                    $no_telepon,
                    $foto
                ]);

                // Log aktivitas
                $log_stmt = $db->prepare("
                    INSERT INTO log_aktivitas (
                        id_log, tipe_pengguna, id_pengguna, 
                        aktivitas, detail
                    ) VALUES (
                        UUID(), 'admin', ?, 
                        'tambah_admin', 'Admin menambahkan admin baru'
                    )
                ");
                $log_stmt->execute([$_SESSION['admin_id']]);

                echo "<script>alert('Admin berhasil ditambahkan!'); window.location.href='index.php';</script>";
                exit;
            }
        }
    } catch (PDOException $e) {
        $error = "Gagal menambahkan admin! " . $e->getMessage();
    }
}

// Tampilkan layout
echo adminLayout("Tambah Admin - Elibrary", "admin");
?>

<div class="row g-4">
    <!-- Page Header -->
    <div class="col-12">
        <div class="card border-0 bg-primary text-white rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Tambah Admin</h4>
                        <p class="mb-0">Tambah admin baru perpustakaan</p>
                    </div>
                    <a href="index.php" class="btn btn-light rounded-pill px-4">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Form -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle me-2"></i><?= $error ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="row g-4">
                        <div class="col-12 col-lg-3 text-center">
                            <div class="mb-3">
                                <img src="../../assets/images/default.jpg"
                                    alt="Preview Foto"
                                    class="rounded-circle mb-3"
                                    width="120" height="120"
                                    style="object-fit: cover;">
                            </div>
                            <div class="d-grid">
                                <label class="btn btn-outline-primary rounded-pill" for="foto">
                                    <i class="bi bi-camera me-2"></i>Pilih Foto
                                </label>
                                <input type="file" id="foto" name="foto" class="d-none"
                                    accept="image/jpeg,image/png"
                                    onchange="previewImage(this)">
                            </div>
                            <div class="form-text mt-2">
                                Opsional. Maksimal 2MB. Format: JPG, PNG
                            </div>
                        </div>

                        <div class="col-12 col-lg-9">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" required
                                        value="<?= $_POST['username'] ?? '' ?>"
                                        pattern="[a-zA-Z0-9_]{3,50}">
                                    <div class="form-text">
                                        Gunakan huruf, angka, dan underscore. Minimal 3 karakter.
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required
                                        minlength="6">
                                    <div class="form-text">Minimal 6 karakter.</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" class="form-control" required
                                        value="<?= $_POST['nama_lengkap'] ?? '' ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required
                                        value="<?= $_POST['email'] ?? '' ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="tel" name="no_telepon" class="form-control"
                                        value="<?= $_POST['no_telepon'] ?? '' ?>"
                                        pattern="[0-9]{10,15}">
                                    <div class="form-text">Opsional. Gunakan angka saja.</div>
                                </div>

                                <div class="col-12">
                                    <hr class="my-4">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                                            <i class="bi bi-plus-circle me-2"></i>Tambah Admin
                                        </button>
                                        <a href="index.php" class="btn btn-light rounded-pill px-4">Batal</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                input.closest('.col-12').querySelector('img').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?= endLayout(); ?>