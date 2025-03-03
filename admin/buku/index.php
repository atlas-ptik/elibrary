<?php
// Path: admin/buku/index.php

session_start();
require_once "../../globals/config/database.php";
require_once "../middleware/auth.php";
require_once "../layouts/admin-layout.php";

cekAuthAdmin();

echo adminLayout("Manajemen Buku - Elibrary", "buku");

try {
    $filter_kelas = $_GET['kelas'] ?? '';
    $filter_jurusan = $_GET['jurusan'] ?? '';
    $search = $_GET['search'] ?? '';

    $sql = "
        SELECT 
            b.*,
            k.nama_kategori,
            r.nomor_rak,
            r.lokasi,
            (SELECT COUNT(*) FROM peminjaman p WHERE p.id_buku = b.id_buku AND p.status = 'dipinjam') as total_dipinjam
        FROM buku b
        JOIN kategori_buku k ON b.id_kategori = k.id_kategori
        JOIN rak_buku r ON b.id_rak = r.id_rak
        WHERE 1=1
    ";

    $params = [];

    if ($filter_kelas) {
        $sql .= " AND b.kelas_fokus = ?";
        $params[] = $filter_kelas;
    }

    if ($filter_jurusan) {
        $sql .= " AND b.jurusan_fokus = ?";
        $params[] = $filter_jurusan;
    }

    if ($search) {
        $sql .= " AND (b.judul LIKE ? OR b.penulis LIKE ? OR b.isbn LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }

    $sql .= " ORDER BY b.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $buku = $stmt->fetchAll();

    $total_buku = count($buku);
    $total_kategori = $db->query("SELECT COUNT(*) FROM kategori_buku")->fetchColumn();
    $total_dipinjam = $db->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam'")->fetchColumn();
    $total_stok = $db->query("SELECT SUM(stok) FROM buku")->fetchColumn();

    $kategori = $db->query("SELECT * FROM kategori_buku ORDER BY nama_kategori")->fetchAll();
    $rak = $db->query("SELECT * FROM rak_buku ORDER BY nomor_rak")->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

if (isset($_SESSION['success'])) {
    echo "
        <div class='alert alert-success alert-dismissible fade show' role='alert'>
            {$_SESSION['success']}
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>
    ";
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo "
        <div class='alert alert-danger alert-dismissible fade show' role='alert'>
            {$_SESSION['error']}
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>
    ";
    unset($_SESSION['error']);
}
?>

<div class="row g-4">
    <!-- Welcome Card -->
    <div class="col-12">
        <div class="card border-0 bg-primary text-white rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Manajemen Buku</h4>
                        <p class="mb-0">Kelola data buku perpustakaan SMANSATOP</p>
                    </div>
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Buku
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Total Buku</p>
                        <h4 class="mb-0"><?= number_format($total_buku) ?></h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-book fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Total Kategori</p>
                        <h4 class="mb-0"><?= number_format($total_kategori) ?></h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-tags fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Sedang Dipinjam</p>
                        <h4 class="mb-0"><?= number_format($total_dipinjam) ?></h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-journal-check fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Total Stok</p>
                        <h4 class="mb-0"><?= number_format($total_stok) ?></h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-archive fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="GET" class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label small">Pencarian</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control border-0 bg-light" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari judul, penulis, atau ISBN...">
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label small">Filter Kelas</label>
                        <select class="form-select border-0 bg-light" name="kelas">
                            <option value="">Semua Kelas</option>
                            <option value="X" <?= $filter_kelas === 'X' ? 'selected' : '' ?>>Kelas X</option>
                            <option value="XI" <?= $filter_kelas === 'XI' ? 'selected' : '' ?>>Kelas XI</option>
                            <option value="XII" <?= $filter_kelas === 'XII' ? 'selected' : '' ?>>Kelas XII</option>
                            <option value="UMUM" <?= $filter_kelas === 'UMUM' ? 'selected' : '' ?>>Umum</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label small">Filter Jurusan</label>
                        <select class="form-select border-0 bg-light" name="jurusan">
                            <option value="">Semua Jurusan</option>
                            <option value="IPA" <?= $filter_jurusan === 'IPA' ? 'selected' : '' ?>>IPA</option>
                            <option value="IPS" <?= $filter_jurusan === 'IPS' ? 'selected' : '' ?>>IPS</option>
                            <option value="BAHASA" <?= $filter_jurusan === 'BAHASA' ? 'selected' : '' ?>>Bahasa</option>
                            <option value="UMUM" <?= $filter_jurusan === 'UMUM' ? 'selected' : '' ?>>Umum</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block w-100 rounded-pill">
                            <i class="bi bi-funnel me-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Books Table Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">Judul</th>
                                <th class="border-0">Kategori</th>
                                <th class="border-0">Rak</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Kelas</th>
                                <th class="border-0">Jurusan</th>
                                <th class="border-0">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php foreach ($buku as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="../../<?= $item['gambar'] ?>" class="rounded-3" width="48" height="64" style="object-fit: cover;">
                                            <div>
                                                <p class="mb-0 fw-medium"><?= htmlspecialchars($item['judul']) ?></p>
                                                <p class="mb-0 small text-muted">
                                                    <?= htmlspecialchars($item['penulis']) ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($item['nama_kategori']) ?></td>
                                    <td>
                                        <p class="mb-0"><?= htmlspecialchars($item['nomor_rak']) ?></p>
                                        <p class="mb-0 small text-muted">
                                            <?= htmlspecialchars($item['lokasi']) ?>
                                        </p>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-primary">
                                                Stok: <?= $item['stok'] ?>
                                            </span>
                                            <?php if ($item['total_dipinjam'] > 0): ?>
                                                <span class="badge bg-info">
                                                    Dipinjam: <?= $item['total_dipinjam'] ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($item['kelas_fokus'] === 'UMUM'): ?>
                                            <span class="badge bg-secondary">Umum</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">Kelas <?= $item['kelas_fokus'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($item['jurusan_fokus'] === 'UMUM'): ?>
                                            <span class="badge bg-secondary">Umum</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary"><?= $item['jurusan_fokus'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $item['id_buku'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $item['id_buku'] ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($buku)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <img src="../../assets/images/empty.png" alt="Data kosong" width="48" height="48">
                                        <p class="text-muted mb-0 mt-2">Tidak ada data buku</p>
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

<!-- Include Modal Files -->
<?php include "tambah.php"; ?>

<?php foreach ($buku as $item): ?>
    <?php include "edit.php"; ?>
    <?php include "hapus.php"; ?>
<?php endforeach; ?>

<?= endLayout(); ?>