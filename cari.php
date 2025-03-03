<?php
// Path: cari.php

require_once "globals/layouts/main.php";
require_once "globals/config/database.php";

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$jurusan = isset($_GET['jurusan']) ? $_GET['jurusan'] : '';

$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(b.judul LIKE ? OR b.penulis LIKE ? OR b.isbn LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($kategori)) {
    $where_conditions[] = "b.id_kategori = ?";
    $params[] = $kategori;
}

if (!empty($kelas)) {
    $where_conditions[] = "b.kelas_fokus = ?";
    $params[] = $kelas;
}

if (!empty($jurusan)) {
    $where_conditions[] = "b.jurusan_fokus = ?";
    $params[] = $jurusan;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

$query = $db->prepare("
    SELECT b.*, kb.nama_kategori, rb.nomor_rak, rb.lokasi
    FROM buku b
    JOIN kategori_buku kb ON b.id_kategori = kb.id_kategori
    JOIN rak_buku rb ON b.id_rak = rb.id_rak
    $where_clause
    ORDER BY b.judul ASC
");

$query->execute($params);
$results = $query->fetchAll();

$kategori_query = $db->query("SELECT * FROM kategori_buku ORDER BY nama_kategori ASC");
$kategoris = $kategori_query->fetchAll();

startLayout("Cari Buku - Elibrary");
?>

<div class="row g-4">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h1 class="mb-0">Cari Buku</h1>
                <p class="text-muted mb-0">Temukan buku yang Anda cari di perpustakaan kami</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-primary rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </div>
    </div>

    <div class="col-12">
        <form action="cari.php" method="GET" class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="search" name="q" class="form-control border-0 bg-light" placeholder="Cari judul, penulis, atau ISBN..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="collapse <?= (!empty($kategori) || !empty($kelas) || !empty($jurusan)) ? 'show' : '' ?>" id="filterCollapse">
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <select name="kategori" class="form-select">
                                        <option value="">Semua Kategori</option>
                                        <?php foreach ($kategoris as $kat): ?>
                                            <option value="<?= $kat['id_kategori'] ?>" <?= $kategori == $kat['id_kategori'] ? 'selected' : '' ?>>
                                                <?= $kat['nama_kategori'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <select name="kelas" class="form-select">
                                        <option value="">Semua Kelas</option>
                                        <option value="X" <?= $kelas == 'X' ? 'selected' : '' ?>>Kelas X</option>
                                        <option value="XI" <?= $kelas == 'XI' ? 'selected' : '' ?>>Kelas XI</option>
                                        <option value="XII" <?= $kelas == 'XII' ? 'selected' : '' ?>>Kelas XII</option>
                                        <option value="UMUM" <?= $kelas == 'UMUM' ? 'selected' : '' ?>>Umum</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-4">
                                    <select name="jurusan" class="form-select">
                                        <option value="">Semua Jurusan</option>
                                        <option value="IPA" <?= $jurusan == 'IPA' ? 'selected' : '' ?>>IPA</option>
                                        <option value="IPS" <?= $jurusan == 'IPS' ? 'selected' : '' ?>>IPS</option>
                                        <option value="BAHASA" <?= $jurusan == 'BAHASA' ? 'selected' : '' ?>>Bahasa</option>
                                        <option value="UMUM" <?= $jurusan == 'UMUM' ? 'selected' : '' ?>>Umum</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <a href="cari.php" class="btn text-decoration-none">Reset</a>
                        <button type="submit" class="btn rounded-pill px-4">Terapkan Filter</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <?php if (!empty($search) || !empty($kategori) || !empty($kelas) || !empty($jurusan)): ?>
        <div class="col-12">
            <div class="alert alert-light border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <span>
                        Menampilkan <?= count($results) ?> hasil
                        <?php
                        $filters = [];
                        if (!empty($search)) $filters[] = "pencarian \"" . htmlspecialchars($search) . "\"";
                        if (!empty($kategori)) {
                            $kat_name = array_filter($kategoris, fn($k) => $k['id_kategori'] == $kategori);
                            if (!empty($kat_name)) $filters[] = "kategori " . reset($kat_name)['nama_kategori'];
                        }
                        if (!empty($kelas)) $filters[] = "kelas " . $kelas;
                        if (!empty($jurusan)) $filters[] = "jurusan " . $jurusan;
                        if (!empty($filters)) echo " untuk " . implode(", ", $filters);
                        ?>
                    </span>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-12">
        <div class="row g-4">
            <?php if (empty($results)): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-search display-1 text-muted"></i>
                        <h4 class="mt-3">Tidak Ada Hasil</h4>
                        <p class="text-muted">Coba kata kunci lain atau ubah filter pencarian Anda</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($results as $buku): ?>
                    <div class="col-12 col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="row g-0">
                                <div class="col-4">
                                    <img src="<?= $buku['gambar'] ?>" class="img-fluid rounded-start h-100" alt="<?= $buku['judul'] ?>" style="object-fit: cover;">
                                </div>
                                <div class="col-8">
                                    <div class="card-body">
                                        <div class="d-flex flex-column h-100">
                                            <div class="mb-auto">
                                                <span class="badge bg-primary bg-opacity-10 text-primary mb-2">
                                                    <?= $buku['nama_kategori'] ?>
                                                </span>
                                                <h5 class="card-title mb-1"><?= $buku['judul'] ?></h5>
                                                <p class="text-muted mb-2"><?= $buku['penulis'] ?></p>

                                                <div class="d-flex gap-3 text-muted small mb-3">
                                                    <span><i class="bi bi-bookmark me-1"></i><?= $buku['kelas_fokus'] ?></span>
                                                    <span><i class="bi bi-journals me-1"></i><?= $buku['jurusan_fokus'] ?></span>
                                                </div>

                                                <p class="card-text small text-muted mb-0">
                                                    <i class="bi bi-geo-alt me-1"></i>
                                                    Rak <?= $buku['nomor_rak'] ?> - <?= $buku['lokasi'] ?>
                                                </p>
                                            </div>

                                            <div class="mt-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge <?= $buku['stok'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                                        <?= $buku['stok'] ?> tersedia
                                                    </span>
                                                    <a href="detail-buku.php?id=<?= $buku['id_buku'] ?>" class="btn btn-primary btn-sm rounded-pill px-3">
                                                        Detail
                                                    </a>
                                                </div>
                                            </div>
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

<?= endLayout(); ?>