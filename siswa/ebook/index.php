<?php
// Path: siswa/ebook/index.php

require_once "../layouts/siswa-layout.php";
require_once "../../globals/config/database.php";

$siswa_id = $_SESSION['siswa']['id'];

$search = $_GET['search'] ?? '';
$kelas = $_GET['kelas'] ?? '';
$jurusan = $_GET['jurusan'] ?? '';

$where_clause = "WHERE 1=1";
$params = [];

if ($search) {
    $where_clause .= " AND (
        e.judul LIKE ? OR 
        e.penulis LIKE ? OR 
        e.penerbit LIKE ? OR
        e.isbn LIKE ?
    )";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

if ($kelas && $kelas !== 'SEMUA') {
    $where_clause .= " AND e.kelas_fokus = ?";
    $params[] = $kelas;
}

if ($jurusan && $jurusan !== 'SEMUA') {
    $where_clause .= " AND e.jurusan_fokus = ?";
    $params[] = $jurusan;
}

$query = $db->prepare("
    SELECT e.*,
           (SELECT COUNT(*) FROM riwayat_baca_ebook WHERE id_ebook = e.id_ebook AND id_siswa = ?) as sudah_dibaca
    FROM ebook e
    $where_clause
    ORDER BY e.judul ASC
");

array_unshift($params, $siswa_id);
$query->execute($params);
$ebooks = $query->fetchAll();

echo startLayout("E-Book");
?>

<div class="col-12">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-12">
                    <form action="" method="GET" class="row g-3">
                        <div class="col-12">
                            <div class="input-group">
                                <input type="text"
                                    class="form-control"
                                    placeholder="Cari judul, penulis, atau ISBN..."
                                    name="search"
                                    value="<?= htmlspecialchars($search) ?>">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search me-1"></i>Cari
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <select class="form-select" name="kelas" onchange="this.form.submit()">
                                <option value="SEMUA">Semua Kelas</option>
                                <option value="X" <?= $kelas === 'X' ? 'selected' : '' ?>>Kelas X</option>
                                <option value="XI" <?= $kelas === 'XI' ? 'selected' : '' ?>>Kelas XI</option>
                                <option value="XII" <?= $kelas === 'XII' ? 'selected' : '' ?>>Kelas XII</option>
                                <option value="UMUM" <?= $kelas === 'UMUM' ? 'selected' : '' ?>>Umum</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <select class="form-select" name="jurusan" onchange="this.form.submit()">
                                <option value="SEMUA">Semua Jurusan</option>
                                <option value="IPA" <?= $jurusan === 'IPA' ? 'selected' : '' ?>>IPA</option>
                                <option value="IPS" <?= $jurusan === 'IPS' ? 'selected' : '' ?>>IPS</option>
                                <option value="BAHASA" <?= $jurusan === 'BAHASA' ? 'selected' : '' ?>>Bahasa</option>
                                <option value="UMUM" <?= $jurusan === 'UMUM' ? 'selected' : '' ?>>Umum</option>
                            </select>
                        </div>
                    </form>
                </div>

                <?php if (empty($ebooks)): ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                            <h6 class="mb-2">Tidak Ada E-Book</h6>
                            <p class="text-muted mb-0">E-book yang Anda cari tidak ditemukan.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($ebooks as $ebook): ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <img src="../../<?= $ebook['gambar'] ?>"
                                    alt="<?= $ebook['judul'] ?>"
                                    class="card-img-top"
                                    style="height: 240px; object-fit: cover;">

                                <div class="card-body">
                                    <div class="d-flex gap-2 mb-2 flex-wrap">
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            Kelas <?= $ebook['kelas_fokus'] ?>
                                            <?= $ebook['jurusan_fokus'] !== 'UMUM' ? $ebook['jurusan_fokus'] : '' ?>
                                        </span>
                                    </div>

                                    <h6 class="card-title mb-1"><?= $ebook['judul'] ?></h6>
                                    <p class="text-muted small mb-0"><?= $ebook['penulis'] ?></p>
                                </div>

                                <div class="card-footer bg-white border-0">
                                    <?php if ($ebook['sudah_dibaca']): ?>
                                        <a href="baca.php?id=<?= $ebook['id_ebook'] ?>"
                                            class="btn btn-primary w-100 rounded-pill">
                                            <i class="bi bi-book me-1"></i>Baca Lagi
                                        </a>
                                    <?php else: ?>
                                        <a href="baca.php?id=<?= $ebook['id_ebook'] ?>"
                                            class="btn btn-outline-primary w-100 rounded-pill">
                                            <i class="bi bi-book me-1"></i>Baca E-Book
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= endLayout(); ?>