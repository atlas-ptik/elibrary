<?php
// Path: ebook.php

require_once "globals/layouts/main.php";
require_once "globals/config/database.php";

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$jurusan = isset($_GET['jurusan']) ? $_GET['jurusan'] : '';

$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(e.judul LIKE ? OR e.penulis LIKE ? OR e.isbn LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($kelas)) {
    $where_conditions[] = "e.kelas_fokus = ?";
    $params[] = $kelas;
}

if (!empty($jurusan)) {
    $where_conditions[] = "e.jurusan_fokus = ?";
    $params[] = $jurusan;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Fixed query with proper line breaks
$query = $db->prepare("
    SELECT 
        e.*,
        COUNT(DISTINCT rbe.id_siswa) as total_pembaca
    FROM ebook e
    LEFT JOIN riwayat_baca_ebook rbe ON e.id_ebook = rbe.id_ebook
    $where_clause
    GROUP BY 
        e.id_ebook,
        e.judul,
        e.penulis,
        e.penerbit,
        e.tahun_terbit,
        e.isbn,
        e.jumlah_halaman,
        e.file_path,
        e.gambar,
        e.kelas_fokus,
        e.jurusan_fokus,
        e.created_at,
        e.updated_at
    ORDER BY e.judul ASC
");

$query->execute($params);
$results = $query->fetchAll();

startLayout("E-Book - Elibrary");
?>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 bg-primary bg-opacity-10 rounded-4">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-8 mb-4 mb-lg-0">
                        <h1 class="display-6 fw-bold mb-3">Perpustakaan Digital</h1>
                        <p class="lead mb-4">Akses ribuan e-book untuk mendukung pembelajaran Anda kapan saja dan di mana saja</p>
                        <form action="ebook.php" method="GET" class="d-flex gap-2 bg-white p-2 rounded-pill shadow-sm">
                            <input type="search" name="q" class="form-control form-control-lg border-0 rounded-pill"
                                placeholder="Cari e-book..." value="<?= htmlspecialchars($search) ?>">
                            <button type="submit" class="btn btn-primary rounded-pill px-4 d-flex align-items-center gap-2">
                                <i class="bi bi-search"></i>
                                <span class="d-none d-md-inline">Cari</span>
                            </button>
                        </form>
                    </div>
                    <div class="col-12 col-lg-4 text-center">
                        <img src="assets/images/ebook.png" alt="E-Book Illustration" class="img-fluid" style="max-height: 200px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-3">
        <div class="sticky-top" style="top: 80px;">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="mb-3">Filter</h5>
                    <form action="ebook.php" method="GET">
                        <?php if (!empty($search)): ?>
                            <input type="hidden" name="q" value="<?= htmlspecialchars($search) ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas" class="form-select">
                                <option value="">Semua Kelas</option>
                                <option value="X" <?= $kelas == 'X' ? 'selected' : '' ?>>Kelas X</option>
                                <option value="XI" <?= $kelas == 'XI' ? 'selected' : '' ?>>Kelas XI</option>
                                <option value="XII" <?= $kelas == 'XII' ? 'selected' : '' ?>>Kelas XII</option>
                                <option value="UMUM" <?= $kelas == 'UMUM' ? 'selected' : '' ?>>Umum</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Jurusan</label>
                            <select name="jurusan" class="form-select">
                                <option value="">Semua Jurusan</option>
                                <option value="IPA" <?= $jurusan == 'IPA' ? 'selected' : '' ?>>IPA</option>
                                <option value="IPS" <?= $jurusan == 'IPS' ? 'selected' : '' ?>>IPS</option>
                                <option value="BAHASA" <?= $jurusan == 'BAHASA' ? 'selected' : '' ?>>Bahasa</option>
                                <option value="UMUM" <?= $jurusan == 'UMUM' ? 'selected' : '' ?>>Umum</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                            <a href="ebook.php" class="btn btn-link text-decoration-none">Reset Filter</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="d-none d-lg-block card border-0 shadow-sm bg-primary bg-opacity-10">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Panduan Membaca</h6>
                    <div class="d-flex flex-column gap-2 text-muted small">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-1-circle"></i>
                            <span>Login ke akun siswa Anda</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-2-circle"></i>
                            <span>Pilih e-book yang ingin dibaca</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-3-circle"></i>
                            <span>Klik tombol "Baca" untuk mulai membaca</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-4-circle"></i>
                            <span>E-book akan terbuka di tab baru</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-9">
        <?php if (!empty($search) || !empty($kelas) || !empty($jurusan)): ?>
            <div class="alert alert-light border-0 shadow-sm mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <span>
                        Menampilkan <?= count($results) ?> hasil
                        <?php
                        $filters = [];
                        if (!empty($search)) $filters[] = "pencarian \"" . htmlspecialchars($search) . "\"";
                        if (!empty($kelas)) $filters[] = "kelas " . $kelas;
                        if (!empty($jurusan)) $filters[] = "jurusan " . $jurusan;
                        if (!empty($filters)) echo " untuk " . implode(", ", $filters);
                        ?>
                    </span>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary btn-sm rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-sort-alpha-down me-1"></i>Urutkan
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['sort' => 'title_asc'])) ?>">Judul (A-Z)</a></li>
                            <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['sort' => 'title_desc'])) ?>">Judul (Z-A)</a></li>
                            <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['sort' => 'newest'])) ?>">Terbaru</a></li>
                            <li><a class="dropdown-item" href="?<?= http_build_query(array_merge($_GET, ['sort' => 'popular'])) ?>">Terpopuler</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (empty($results)): ?>
            <div class="text-center py-5">
                <i class="bi bi-search display-1 text-muted"></i>
                <h4 class="mt-3">Tidak Ada Hasil</h4>
                <p class="text-muted">Coba kata kunci lain atau ubah filter pencarian Anda</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($results as $ebook): ?>
                    <div class="col-12 col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="d-flex flex-column flex-sm-row h-100">
                                <div class="book-cover" style="flex: 0 0 120px;">
                                    <img src="<?= $ebook['gambar'] ?>"
                                        class="h-100 w-100"
                                        alt="<?= $ebook['judul'] ?>"
                                        style="object-fit: cover;">
                                </div>
                                <div class="card-body p-3">
                                    <div class="d-flex flex-column h-100">
                                        <div class="mb-auto">
                                            <div class="d-flex gap-2 mb-2 flex-wrap">
                                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                                    Kelas <?= $ebook['kelas_fokus'] ?>
                                                </span>
                                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                                    <?= $ebook['jurusan_fokus'] ?>
                                                </span>
                                                <span class="badge bg-success bg-opacity-10 text-success">
                                                    <i class="bi bi-people-fill me-1"></i><?= $ebook['total_pembaca'] ?> pembaca
                                                </span>
                                            </div>

                                            <h5 class="card-title mb-1"><?= $ebook['judul'] ?></h5>
                                            <p class="text-muted small mb-2">
                                                oleh <?= $ebook['penulis'] ?> • <?= $ebook['tahun_terbit'] ?>
                                            </p>

                                            <div class="d-flex gap-3 text-muted small mb-3">
                                                <span><i class="bi bi-bookmark me-1"></i><?= $ebook['kelas_fokus'] ?></span>
                                                <span><i class="bi bi-journals me-1"></i><?= $ebook['jurusan_fokus'] ?></span>
                                                <span><i class="bi bi-file-text me-1"></i><?= $ebook['jumlah_halaman'] ?> hal</span>
                                            </div>
                                        </div>

                                        <div class="mt-3 d-flex gap-2">
                                            <a href="detail-ebook.php?id=<?= $ebook['id_ebook'] ?>"
                                                class="btn btn-outline-primary btn-sm rounded-pill flex-grow-1">
                                                <i class="bi bi-info-circle me-1"></i>Detail
                                            </a>
                                            <a href="baca-ebook.php?id=<?= $ebook['id_ebook'] ?>"
                                                class="btn btn-primary btn-sm rounded-pill flex-grow-1">
                                                <i class="bi bi-book me-1"></i>Baca
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= endLayout(); ?>