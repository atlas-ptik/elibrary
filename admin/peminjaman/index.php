<?php
// Path: admin/peminjaman/index.php

session_start();
require_once "../../globals/config/database.php";
require_once "../middleware/auth.php";
require_once "../layouts/admin-layout.php";

cekAuthAdmin();

echo adminLayout("Manajemen Peminjaman - Elibrary", "peminjaman");

try {
    $filter_status = $_GET['status'] ?? '';
    $search = $_GET['search'] ?? '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;

    $count_sql = "
        SELECT COUNT(*) 
        FROM peminjaman p
        JOIN siswa s ON p.id_siswa = s.id_siswa
        JOIN buku b ON p.id_buku = b.id_buku
        WHERE 1=1
    ";

    $sql = "
        SELECT 
            p.*,
            s.nama_lengkap as nama_siswa,
            s.nis,
            s.kelas,
            s.jurusan,
            b.judul as judul_buku,
            b.penulis,
            DATEDIFF(p.tanggal_jatuh_tempo, CURRENT_DATE) as sisa_hari,
            DATEDIFF(p.tanggal_kembali, p.tanggal_jatuh_tempo) as keterlambatan
        FROM peminjaman p
        JOIN siswa s ON p.id_siswa = s.id_siswa
        JOIN buku b ON p.id_buku = b.id_buku
        WHERE 1=1
    ";

    $params = [];

    if ($filter_status) {
        $sql .= " AND p.status = ?";
        $params[] = $filter_status;
    }

    if ($search) {
        $sql .= " AND (s.nama_lengkap LIKE ? OR s.nis LIKE ? OR b.judul LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }

    $sql .= " ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
    $count_sql .= $filter_status ? " AND p.status = ?" : "";
    $count_sql .= $search ? " AND (s.nama_lengkap LIKE ? OR s.nis LIKE ? OR b.judul LIKE ?)" : "";

    // Get total records for pagination
    $count_stmt = $db->prepare($count_sql);
    $count_params = $params;
    $count_stmt->execute($count_params);
    $total_records = $count_stmt->fetchColumn();
    $total_pages = ceil($total_records / $per_page);

    // Add pagination parameters
    $params[] = $per_page;
    $params[] = $offset;

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $peminjaman = $stmt->fetchAll();

    $total_dipinjam = $db->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam'")->fetchColumn();
    $total_terlambat = $db->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'terlambat'")->fetchColumn();
    $total_dikembalikan = $db->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dikembalikan'")->fetchColumn();

    $siswa = $db->query("SELECT id_siswa, nis, nama_lengkap, kelas, jurusan FROM siswa WHERE status_aktif = TRUE ORDER BY nama_lengkap")->fetchAll();
    $buku = $db->query("SELECT id_buku, judul, stok FROM buku WHERE stok > 0 ORDER BY judul")->fetchAll();
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
                        <h4 class="mb-1">Manajemen Peminjaman</h4>
                        <p class="mb-0">Kelola transaksi peminjaman buku perpustakaan SMANSATOP</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalHapusSemua">
                            <i class="bi bi-trash me-2"></i>Hapus Semua
                        </button>
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Peminjaman
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-12 col-md-6 col-xl-4">
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

    <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Terlambat</p>
                        <h4 class="mb-0"><?= number_format($total_terlambat) ?></h4>
                    </div>
                    <div class="bg-danger bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-exclamation-circle fs-4 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Sudah Dikembalikan</p>
                        <h4 class="mb-0"><?= number_format($total_dikembalikan) ?></h4>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-check-circle fs-4 text-success"></i>
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
                    <div class="col-12 col-md-6">
                        <label class="form-label small">Pencarian</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control border-0 bg-light" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama siswa, NIS, atau judul buku...">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small">Filter Status</label>
                        <select class="form-select border-0 bg-light" name="status">
                            <option value="">Semua Status</option>
                            <option value="dipinjam" <?= $filter_status === 'dipinjam' ? 'selected' : '' ?>>Sedang Dipinjam</option>
                            <option value="terlambat" <?= $filter_status === 'terlambat' ? 'selected' : '' ?>>Terlambat</option>
                            <option value="dikembalikan" <?= $filter_status === 'dikembalikan' ? 'selected' : '' ?>>Sudah Dikembalikan</option>
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

    <!-- Loans Table Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0" style="width: 50px">No</th>
                                <th class="border-0">Siswa</th>
                                <th class="border-0">Buku</th>
                                <th class="border-0">Tanggal Pinjam</th>
                                <th class="border-0">Jatuh Tempo</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php foreach ($peminjaman as $index => $item): ?>
                                <tr>
                                    <td class="text-center"><?= $offset + $index + 1 ?></td>
                                    <td>
                                        <p class="mb-0 fw-medium"><?= htmlspecialchars($item['nama_siswa']) ?></p>
                                        <p class="mb-0 small text-muted">
                                            <?= htmlspecialchars($item['nis']) ?> •
                                            Kelas <?= $item['kelas'] ?> <?= $item['jurusan'] ?>
                                        </p>
                                    </td>
                                    <td>
                                        <p class="mb-0"><?= htmlspecialchars($item['judul_buku']) ?></p>
                                        <p class="mb-0 small text-muted">
                                            <?= htmlspecialchars($item['penulis']) ?>
                                        </p>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($item['tanggal_pinjam'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($item['tanggal_jatuh_tempo'])) ?></td>
                                    <td>
                                        <?php if ($item['status'] === 'dipinjam'): ?>
                                            <?php if ($item['sisa_hari'] < 0): ?>
                                                <span class="badge bg-danger">
                                                    Terlambat <?= abs($item['sisa_hari']) ?> hari
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">
                                                    Sisa <?= $item['sisa_hari'] ?> hari
                                                </span>
                                            <?php endif; ?>
                                        <?php elseif ($item['status'] === 'terlambat'): ?>
                                            <span class="badge bg-danger">
                                                Terlambat <?= $item['keterlambatan'] ?> hari
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Dikembalikan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <?php if ($item['status'] === 'dipinjam' || $item['status'] === 'terlambat'): ?>
                                                <button type="button" class="btn btn-sm btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalKembali<?= $item['id_peminjaman'] ?>">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            <?php endif; ?>
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $item['id_peminjaman'] ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $item['id_peminjaman'] ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($peminjaman)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <img src="../../assets/images/empty.png" alt="Data kosong" width="48" height="48">
                                        <p class="text-muted mb-0 mt-2">Tidak ada data peminjaman</p>
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
<?php include_once "tambah.php"; ?>
<?php include_once "hapus-semua.php"; ?>

<?php foreach ($peminjaman as $item): ?>
    <?php include "kembali.php"; ?>
    <?php include "detail.php"; ?>
    <?php include "hapus.php"; ?>
<?php endforeach; ?>

<?= endLayout(); ?>