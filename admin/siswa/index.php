<?php
// Path: admin/siswa/index.php

session_start();
require_once "../../globals/config/database.php";
require_once "../middleware/auth.php";
require_once "../layouts/admin-layout.php";

// Cek autentikasi
cekAuthAdmin();

// Handle hapus siswa
if (isset($_GET['hapus'])) {
    $id = filter_input(INPUT_GET, 'hapus', FILTER_SANITIZE_SPECIAL_CHARS);
    
    try {
        $stmt = $db->prepare("UPDATE siswa SET status_aktif = FALSE WHERE id_siswa = ?");
        $stmt->execute([$id]);
        
        // Log aktivitas
        $log_stmt = $db->prepare("
            INSERT INTO log_aktivitas (
                id_log, tipe_pengguna, id_pengguna, 
                aktivitas, detail
            ) VALUES (
                UUID(), 'admin', ?, 
                'hapus_siswa', 'Admin menghapus data siswa'
            )
        ");
        $log_stmt->execute([$_SESSION['admin_id']]);
        
        echo "<script>alert('Siswa berhasil dihapus!'); window.location.href='index.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Gagal menghapus siswa!');</script>";
    }
}

// Ambil filter dari query string
$kelas = filter_input(INPUT_GET, 'kelas', FILTER_SANITIZE_SPECIAL_CHARS);
$jurusan = filter_input(INPUT_GET, 'jurusan', FILTER_SANITIZE_SPECIAL_CHARS);
$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);

// Buat query dasar
$query = "
    SELECT id_siswa, username, nis, nama_lengkap, 
           email, no_telepon, foto, kelas, jurusan, 
           created_at 
    FROM siswa 
    WHERE status_aktif = TRUE
";

// Tambahkan filter jika ada
$params = [];
if ($kelas) {
    $query .= " AND kelas = ?";
    $params[] = $kelas;
}
if ($jurusan) {
    $query .= " AND jurusan = ?";
    $params[] = $jurusan;
}
if ($search) {
    $query .= " AND (nama_lengkap LIKE ? OR nis LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY kelas ASC, nama_lengkap ASC";

// Ambil data siswa
try {
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $siswa_list = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Tampilkan layout
echo adminLayout("Manajemen Siswa - Elibrary", "siswa");
?>

<div class="row g-4">
    <!-- Page Header -->
    <div class="col-12">
        <div class="card border-0 bg-primary text-white rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Manajemen Siswa</h4>
                        <p class="mb-0">Kelola data siswa perpustakaan</p>
                    </div>
                    <a href="tambah.php" class="btn btn-light rounded-pill px-4">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Siswa
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="GET" class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Pencarian</label>
                        <input type="search" name="search" class="form-control" 
                               placeholder="Cari nama, NIS, atau email..."
                               value="<?= $search ?? '' ?>">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Kelas</label>
                        <select name="kelas" class="form-select">
                            <option value="">Semua Kelas</option>
                            <option value="X" <?= $kelas === 'X' ? 'selected' : '' ?>>Kelas X</option>
                            <option value="XI" <?= $kelas === 'XI' ? 'selected' : '' ?>>Kelas XI</option>
                            <option value="XII" <?= $kelas === 'XII' ? 'selected' : '' ?>>Kelas XII</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Jurusan</label>
                        <select name="jurusan" class="form-select">
                            <option value="">Semua Jurusan</option>
                            <option value="IPA" <?= $jurusan === 'IPA' ? 'selected' : '' ?>>IPA</option>
                            <option value="IPS" <?= $jurusan === 'IPS' ? 'selected' : '' ?>>IPS</option>
                            <option value="BAHASA" <?= $jurusan === 'BAHASA' ? 'selected' : '' ?>>Bahasa</option>
                            <option value="UMUM" <?= $jurusan === 'UMUM' ? 'selected' : '' ?>>Umum</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label d-none d-md-block">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 flex-grow-1">
                                <i class="bi bi-search me-2"></i>Filter
                            </button>
                            <?php if ($search || $kelas || $jurusan): ?>
                                <a href="index.php" class="btn btn-light rounded-pill px-3">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Student List -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="50" class="text-center">No</th>
                                <th width="80">Foto</th>
                                <th>NIS</th>
                                <th>Nama Lengkap</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>Email</th>
                                <th>No. Telepon</th>
                                <th width="100" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($siswa_list as $index => $siswa): ?>
                                <tr>
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td>
                                        <img src="../../<?= $siswa['foto'] ?>" 
                                             alt="<?= $siswa['nama_lengkap'] ?>" 
                                             class="rounded-circle"
                                             width="40" height="40"
                                             style="object-fit: cover;">
                                    </td>
                                    <td class="fw-medium"><?= $siswa['nis'] ?></td>
                                    <td><?= $siswa['nama_lengkap'] ?></td>
                                    <td>Kelas <?= $siswa['kelas'] ?></td>
                                    <td><?= $siswa['jurusan'] ?></td>
                                    <td><?= $siswa['email'] ?></td>
                                    <td><?= $siswa['no_telepon'] ?? '-' ?></td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="ubah.php?id=<?= $siswa['id_siswa'] ?>" 
                                               class="btn btn-sm btn-primary rounded-pill px-3">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="index.php?hapus=<?= $siswa['id_siswa'] ?>" 
                                               class="btn btn-sm btn-danger rounded-pill px-3"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus siswa ini?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($siswa_list)): ?>
                                <tr>
                                    <td colspan="9" class="text-center py-3 text-muted">
                                        <?php if ($search || $kelas || $jurusan): ?>
                                            Tidak ada siswa yang sesuai dengan filter
                                        <?php else: ?>
                                            Tidak ada data siswa
                                        <?php endif; ?>
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