<?php
// Path: admin/admin/index.php

session_start();
require_once "../../globals/config/database.php";
require_once "../middleware/auth.php";
require_once "../layouts/admin-layout.php";

// Cek autentikasi
cekAuthAdmin();

// Handle hapus admin
if (isset($_GET['hapus'])) {
    $id = filter_input(INPUT_GET, 'hapus', FILTER_SANITIZE_SPECIAL_CHARS);

    try {
        if ($id === $_SESSION['admin_id']) {
            echo "<script>alert('Tidak dapat menghapus akun yang sedang digunakan!');</script>";
        } else {
            $stmt = $db->prepare("UPDATE admin SET status_aktif = FALSE WHERE id_admin = ?");
            $stmt->execute([$id]);

            $log_stmt = $db->prepare("
                INSERT INTO log_aktivitas (
                    id_log, tipe_pengguna, id_pengguna, 
                    aktivitas, detail
                ) VALUES (
                    UUID(), 'admin', ?, 
                    'hapus_admin', 'Admin menghapus data admin lain'
                )
            ");
            $log_stmt->execute([$_SESSION['admin_id']]);

            echo "<script>alert('Admin berhasil dihapus!'); window.location.href='index.php';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Gagal menghapus admin!');</script>";
    }
}

// Ambil data admin
try {
    $query = $db->query("
        SELECT id_admin, username, nama_lengkap, email, 
               no_telepon, foto, status_aktif, created_at 
        FROM admin 
        WHERE status_aktif = TRUE 
        ORDER BY created_at DESC
    ");
    $admin_list = $query->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Tampilkan layout
echo adminLayout("Manajemen Admin - Elibrary", "admin");
?>

<div class="row g-4">
    <!-- Page Header -->
    <div class="col-12">
        <div class="card border-0 bg-primary text-white rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Manajemen Admin</h4>
                        <p class="mb-0">Kelola akun admin perpustakaan</p>
                    </div>
                    <div>
                        <a href="tambah.php" class="btn btn-light rounded-pill px-4">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin List -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th width="80">Foto</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>No. Telepon</th>
                                <th>Tanggal Dibuat</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admin_list as $index => $admin): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td>
                                        <img src="../../<?= $admin['foto'] ?>"
                                            alt="<?= $admin['nama_lengkap'] ?>"
                                            class="rounded-circle"
                                            width="40" height="40"
                                            style="object-fit: cover;">
                                    </td>
                                    <td class="fw-medium"><?= $admin['nama_lengkap'] ?></td>
                                    <td><?= $admin['username'] ?></td>
                                    <td><?= $admin['email'] ?></td>
                                    <td><?= $admin['no_telepon'] ?? '-' ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($admin['created_at'])) ?></td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="ubah.php?id=<?= $admin['id_admin'] ?>"
                                                class="btn btn-sm btn-primary rounded-pill px-3">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <?php if ($admin['id_admin'] !== $_SESSION['admin_id']): ?>
                                                <a href="index.php?hapus=<?= $admin['id_admin'] ?>"
                                                    class="btn btn-sm btn-danger rounded-pill px-3"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus admin ini?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($admin_list)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-3 text-muted">
                                        Tidak ada data admin
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= endLayout(); ?>