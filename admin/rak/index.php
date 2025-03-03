<?php
// Path: admin/rak/index.php

session_start();
require_once "../../globals/config/database.php";
require_once "../middleware/auth.php";
require_once "../layouts/admin-layout.php";

cekAuthAdmin();

echo adminLayout("Rak Buku - Elibrary", "rak");

try {
    $search = $_GET['search'] ?? '';

    $sql = "
        SELECT 
            r.*,
            (SELECT COUNT(*) FROM buku b WHERE b.id_rak = r.id_rak) as total_buku
        FROM rak_buku r
        WHERE 1=1
    ";

    if ($search) {
        $sql .= " AND (r.nomor_rak LIKE ? OR r.lokasi LIKE ? OR r.keterangan LIKE ?)";
        $search_param = "%$search%";
        $params = [$search_param, $search_param, $search_param];

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
    } else {
        $stmt = $db->query($sql);
    }

    $rak = $stmt->fetchAll();
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
    <!-- Header -->
    <div class="col-12">
        <div class="card border-0 bg-primary text-white rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Rak Buku</h4>
                        <p class="mb-0">Kelola rak buku perpustakaan</p>
                    </div>
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Rak
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="GET" class="row g-3">
                    <div class="col-12 col-md-10">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari rak buku...">
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <button type="submit" class="btn btn-primary d-block w-100 rounded-pill">
                            <i class="bi bi-search me-2"></i>Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rak List -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0">Nomor Rak</th>
                                <th class="border-0">Lokasi</th>
                                <th class="border-0">Kapasitas</th>
                                <th class="border-0">Total Buku</th>
                                <th class="border-0">Keterangan</th>
                                <th class="border-0" width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            <?php foreach ($rak as $item): ?>
                                <tr>
                                    <td class="fw-medium"><?= htmlspecialchars($item['nomor_rak']) ?></td>
                                    <td><?= htmlspecialchars($item['lokasi']) ?></td>
                                    <td>
                                        <?php
                                        $persentase = ($item['total_buku'] / $item['kapasitas']) * 100;
                                        $warnaBar = $persentase >= 90 ? 'danger' : ($persentase >= 70 ? 'warning' : 'primary');
                                        ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px;">
                                                <div class="progress-bar bg-<?= $warnaBar ?>"
                                                    role="progressbar"
                                                    style="width: <?= $persentase ?>%"
                                                    aria-valuenow="<?= $persentase ?>"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <small class="text-muted">
                                                <?= number_format($item['total_buku']) ?>/<?= number_format($item['kapasitas']) ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary rounded-pill">
                                            <?= number_format($item['total_buku']) ?> buku
                                        </span>
                                    </td>
                                    <td class="text-muted"><?= htmlspecialchars($item['keterangan'] ?: '-') ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $item['id_rak'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <?php if ($item['total_buku'] == 0): ?>
                                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $item['id_rak'] ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($rak)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <img src="../../assets/images/empty.png" alt="Data kosong" width="48" height="48">
                                        <p class="text-muted mb-0 mt-2">Tidak ada data rak buku</p>
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

<!-- Include Modals -->
<?php include "tambah.php"; ?>

<?php foreach ($rak as $item): ?>
    <?php include "edit.php"; ?>
    <?php if ($item['total_buku'] == 0): ?>
        <?php include "hapus.php"; ?>
    <?php endif; ?>
<?php endforeach; ?>

<?= endLayout(); ?>