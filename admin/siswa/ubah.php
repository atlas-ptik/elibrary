<?php
// Path: admin/siswa/ubah.php

session_start();
require_once "../../globals/config/database.php";
require_once "../middleware/auth.php";
require_once "../layouts/admin-layout.php";

// Cek autentikasi
cekAuthAdmin();

// Ambil ID siswa dari parameter
$id_siswa = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

if (!$id_siswa) {
    header("Location: index.php");
    exit;
}

// Ambil data siswa
try {
    $stmt = $db->prepare("
        SELECT * FROM siswa 
        WHERE id_siswa = ? AND status_aktif = TRUE
    ");
    $stmt->execute([$id_siswa]);
    $siswa = $stmt->fetch();

    if (!$siswa) {
        echo "<script>alert('Siswa tidak ditemukan!'); window.location.href='index.php';</script>";
        exit;
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
    $nis = filter_input(INPUT_POST, 'nis', FILTER_SANITIZE_SPECIAL_CHARS);
    $nama_lengkap = filter_input(INPUT_POST, 'nama_lengkap', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $no_telepon = filter_input(INPUT_POST, 'no_telepon', FILTER_SANITIZE_SPECIAL_CHARS);
    $kelas = filter_input(INPUT_POST, 'kelas', FILTER_SANITIZE_SPECIAL_CHARS);
    $jurusan = filter_input(INPUT_POST, 'jurusan', FILTER_SANITIZE_SPECIAL_CHARS);
    $password_baru = $_POST['password_baru'] ?? '';

    try {
        // Cek username, NIS dan email unique kecuali untuk siswa yang sedang diubah
        $check = $db->prepare("
            SELECT id_siswa 
            FROM siswa 
            WHERE (username = ? OR nis = ? OR email = ?) 
            AND id_siswa != ?
        ");
        $check->execute([$username, $nis, $email, $id_siswa]);

        if ($check->rowCount() > 0) {
            $error = "Username, NIS atau email sudah digunakan!";
        } else {
            // Upload foto jika ada
            $foto = $siswa['foto'];
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['image/jpeg', 'image/png'];
                $uploaded = $_FILES['foto'];

                if (!in_array($uploaded['type'], $allowed)) {
                    $error = "Tipe file tidak didukung! Gunakan JPG atau PNG.";
                } else if ($uploaded['size'] > 2 * 1024 * 1024) {
                    $error = "Ukuran file terlalu besar! Maksimal 2MB.";
                } else {
                    $ext = pathinfo($uploaded['name'], PATHINFO_EXTENSION);
                    $filename = 'siswa_' . time() . '.' . $ext;
                    $destination = '../../assets/images/siswa/' . $filename;

                    if (!is_dir('../../assets/images/siswa')) {
                        mkdir('../../assets/images/siswa', 0777, true);
                    }

                    if (move_uploaded_file($uploaded['tmp_name'], $destination)) {
                        // Hapus foto lama jika bukan foto default
                        if ($siswa['foto'] !== 'assets/images/default.jpg') {
                            $old_file = '../../' . $siswa['foto'];
                            if (file_exists($old_file)) {
                                unlink($old_file);
                            }
                        }
                        $foto = 'assets/images/siswa/' . $filename;
                    } else {
                        $error = "Gagal mengupload foto!";
                    }
                }
            }

            if (!isset($error)) {
                // Update data siswa
                if ($password_baru) {
                    // Update dengan password baru
                    $stmt = $db->prepare("
                        UPDATE siswa 
                        SET username = ?,
                            nis = ?,
                            nama_lengkap = ?,
                            email = ?,
                            no_telepon = ?,
                            foto = ?,
                            kelas = ?,
                            jurusan = ?,
                            password = ?
                        WHERE id_siswa = ?
                    ");
                    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
                    $stmt->execute([
                        $username,
                        $nis,
                        $nama_lengkap,
                        $email,
                        $no_telepon,
                        $foto,
                        $kelas,
                        $jurusan,
                        $password_hash,
                        $id_siswa
                    ]);
                } else {
                    // Update tanpa password
                    $stmt = $db->prepare("
                        UPDATE siswa 
                        SET username = ?,
                            nis = ?,
                            nama_lengkap = ?,
                            email = ?,
                            no_telepon = ?,
                            foto = ?,
                            kelas = ?,
                            jurusan = ?
                        WHERE id_siswa = ?
                    ");
                    $stmt->execute([
                        $username,
                        $nis,
                        $nama_lengkap,
                        $email,
                        $no_telepon,
                        $foto,
                        $kelas,
                        $jurusan,
                        $id_siswa
                    ]);
                }

                // Log aktivitas
                $log_stmt = $db->prepare("
                    INSERT INTO log_aktivitas (
                        id_log, tipe_pengguna, id_pengguna, 
                        aktivitas, detail
                    ) VALUES (
                        UUID(), 'admin', ?, 
                        'ubah_siswa', 'Admin mengubah data siswa'
                    )
                ");
                $log_stmt->execute([$_SESSION['admin_id']]);

                echo "<script>alert('Data siswa berhasil diubah!'); window.location.href='index.php';</script>";
                exit;
            }
        }
    } catch (PDOException $e) {
        $error = "Gagal mengubah data siswa! " . $e->getMessage();
    }
}

// Tampilkan layout
echo adminLayout("Ubah Siswa - Elibrary", "siswa");
?>

<div class="row g-4">
    <!-- Page Header -->
    <div class="col-12">
        <div class="card border-0 bg-primary text-white rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Ubah Siswa</h4>
                        <p class="mb-0">Ubah data siswa perpustakaan</p>
                    </div>
                    <a href="index.php" class="btn btn-light rounded-pill px-4">
                        <i class="bi bi-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
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
                                <img src="../../<?= $siswa['foto'] ?>"
                                    alt="<?= $siswa['nama_lengkap'] ?>"
                                    class="rounded-circle mb-3"
                                    width="120" height="120"
                                    style="object-fit: cover;">
                            </div>
                            <div class="d-grid">
                                <label class="btn btn-outline-primary rounded-pill" for="foto">
                                    <i class="bi bi-camera me-2"></i>Ubah Foto
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
                                    <label class="form-label">NIS</label>
                                    <input type="text" name="nis" class="form-control" required
                                        value="<?= $siswa['nis'] ?>"
                                        pattern="[0-9]{5,20}">
                                    <div class="form-text">Nomor Induk Siswa. Gunakan angka saja.</div>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Kelas</label>
                                    <select name="kelas" class="form-select" required>
                                        <option value="" disabled>Pilih Kelas</option>
                                        <option value="X" <?= $siswa['kelas'] === 'X' ? 'selected' : '' ?>>Kelas X</option>
                                        <option value="XI" <?= $siswa['kelas'] === 'XI' ? 'selected' : '' ?>>Kelas XI</option>
                                        <option value="XII" <?= $siswa['kelas'] === 'XII' ? 'selected' : '' ?>>Kelas XII</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Jurusan</label>
                                    <select name="jurusan" class="form-select" required>
                                        <option value="" disabled>Pilih Jurusan</option>
                                        <option value="IPA" <?= $siswa['jurusan'] === 'IPA' ? 'selected' : '' ?>>IPA</option>
                                        <option value="IPS" <?= $siswa['jurusan'] === 'IPS' ? 'selected' : '' ?>>IPS</option>
                                        <option value="BAHASA" <?= $siswa['jurusan'] === 'BAHASA' ? 'selected' : '' ?>>Bahasa</option>
                                        <option value="UMUM" <?= $siswa['jurusan'] === 'UMUM' ? 'selected' : '' ?>>Umum</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" class="form-control" required
                                        value="<?= $siswa['nama_lengkap'] ?>">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" required
                                        value="<?= $siswa['username'] ?>"
                                        pattern="[a-zA-Z0-9_]{5,50}">
                                    <div class="form-text">5-50 karakter. Hanya huruf, angka, dan underscore.</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" name="password_baru" class="form-control"
                                        minlength="6">
                                    <div class="form-text">
                                        Kosongkan jika tidak ingin mengubah password. Minimal 6 karakter.
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">No. Telepon</label>
                                    <input type="tel" name="no_telepon" class="form-control"
                                        value="<?= $siswa['no_telepon'] ?>"
                                        pattern="[0-9]{10,15}">
                                    <div class="form-text">Opsional. Gunakan angka saja.</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required
                                        value="<?= $siswa['email'] ?>">
                                </div>

                                <div class="col-12">
                                    <hr class="my-4">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                                            <i class="bi bi-save me-2"></i>Simpan Perubahan
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
</script>

<?= endLayout(); ?>