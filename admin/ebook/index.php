<?php
// Path: admin/ebook/index.php

session_start();
require_once "../../globals/config/database.php";
require_once "../middleware/auth.php";
require_once "../layouts/admin-layout.php";

cekAuthAdmin();

echo adminLayout("E-Book - Elibrary", "ebook");

try {
    $search = $_GET['search'] ?? '';
    $kelas = $_GET['kelas'] ?? '';
    $jurusan = $_GET['jurusan'] ?? '';

    // Build query
    $sql = "
        SELECT 
            e.*,
            (
                SELECT COUNT(*) 
                FROM riwayat_baca_ebook rbe 
                WHERE rbe.id_ebook = e.id_ebook
            ) as total_dibaca
        FROM ebook e
        WHERE 1=1
    ";
    $params = [];

    if ($search) {
        $sql .= " AND (e.judul LIKE ? OR e.penulis LIKE ? OR e.penerbit LIKE ? OR e.isbn LIKE ?)";
        $search_param = "%$search%";
        array_push($params, $search_param, $search_param, $search_param, $search_param);
    }

    if ($kelas && $kelas !== 'SEMUA') {
        $sql .= " AND e.kelas_fokus = ?";
        array_push($params, $kelas);
    }

    if ($jurusan && $jurusan !== 'SEMUA') {
        $sql .= " AND e.jurusan_fokus = ?";
        array_push($params, $jurusan);
    }

    $sql .= " ORDER BY e.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $ebooks = $stmt->fetchAll();
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
                        <h4 class="mb-1">E-Book</h4>
                        <p class="mb-0">Kelola e-book perpustakaan</p>
                    </div>
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="bi bi-plus-circle me-2"></i>Tambah E-Book
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="GET" class="row g-3">
                    <div class="col-12 col-sm-6 col-xl-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari e-book...">
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-2">
                        <select class="form-select bg-light border-0" name="kelas">
                            <option value="SEMUA">Semua Kelas</option>
                            <option value="X" <?= $kelas === 'X' ? 'selected' : '' ?>>Kelas X</option>
                            <option value="XI" <?= $kelas === 'XI' ? 'selected' : '' ?>>Kelas XI</option>
                            <option value="XII" <?= $kelas === 'XII' ? 'selected' : '' ?>>Kelas XII</option>
                            <option value="UMUM" <?= $kelas === 'UMUM' ? 'selected' : '' ?>>Umum</option>
                        </select>
                    </div>
                    <div class="col-12 col-sm-6 col-xl-2">
                        <select class="form-select bg-light border-0" name="jurusan">
                            <option value="SEMUA">Semua Jurusan</option>
                            <option value="IPA" <?= $jurusan === 'IPA' ? 'selected' : '' ?>>IPA</option>
                            <option value="IPS" <?= $jurusan === 'IPS' ? 'selected' : '' ?>>IPS</option>
                            <option value="BAHASA" <?= $jurusan === 'BAHASA' ? 'selected' : '' ?>>Bahasa</option>
                            <option value="UMUM" <?= $jurusan === 'UMUM' ? 'selected' : '' ?>>Umum</option>
                        </select>
                    </div>
                    <div class="col-12 col-xl-2">
                        <button type="submit" class="btn btn-primary rounded-pill d-block w-100">
                            <i class="bi bi-filter me-2"></i>Terapkan Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- E-Book Grid -->
    <div class="col-12">
        <div class="row g-4">
            <?php if (empty($ebooks)): ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4 text-center">
                            <img src="../../assets/images/empty.png" alt="Data kosong" width="48" height="48">
                            <p class="text-muted mb-0 mt-2">Tidak ada data e-book</p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($ebooks as $ebook): ?>
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-4">
                                <div class="row">
                                    <div class="col-auto">
                                        <img src="../../<?= htmlspecialchars($ebook['gambar']) ?>"
                                            alt="<?= htmlspecialchars($ebook['judul']) ?>"
                                            class="rounded shadow-sm"
                                            width="120" height="160"
                                            style="object-fit: cover;">
                                    </div>
                                    <div class="col">
                                        <h5 class="card-title mb-1">
                                            <?= htmlspecialchars($ebook['judul']) ?>
                                        </h5>
                                        <p class="text-muted small mb-3">
                                            Oleh <?= htmlspecialchars($ebook['penulis']) ?>
                                        </p>
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <span class="badge bg-info rounded-pill">
                                                Kelas <?= $ebook['kelas_fokus'] ?>
                                            </span>
                                            <span class="badge bg-secondary rounded-pill">
                                                <?= $ebook['jurusan_fokus'] ?>
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-3 text-muted small mb-3">
                                            <div>
                                                <i class="bi bi-building me-1"></i>
                                                <?= htmlspecialchars($ebook['penerbit']) ?>
                                            </div>
                                            <div>
                                                <i class="bi bi-calendar me-1"></i>
                                                <?= $ebook['tahun_terbit'] ?>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $ebook['id_ebook'] ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-info rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $ebook['id_ebook'] ?>">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $ebook['id_ebook'] ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Include Modals -->
<?php include "tambah.php"; ?>

<?php foreach ($ebooks as $ebook): ?>
    <?php include "edit.php"; ?>
    <?php include "detail.php"; ?>
    <?php include "hapus.php"; ?>
<?php endforeach; ?>

<?= endLayout(); ?>