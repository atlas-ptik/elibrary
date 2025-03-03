<?php
// Path: detail-buku.php

require_once "globals/layouts/main.php";
require_once "globals/config/database.php";

if (!isset($_GET['id'])) {
    header("Location: cari.php");
    exit;
}

$id_buku = $_GET['id'];

$query = $db->prepare("
    SELECT b.*, kb.nama_kategori, rb.nomor_rak, rb.lokasi,
    (SELECT COUNT(*) FROM peminjaman WHERE id_buku = b.id_buku AND status = 'dipinjam') as sedang_dipinjam
    FROM buku b
    JOIN kategori_buku kb ON b.id_kategori = kb.id_kategori
    JOIN rak_buku rb ON b.id_rak = rb.id_rak
    WHERE b.id_buku = ?
");

$query->execute([$id_buku]);
$buku = $query->fetch();

if (!$buku) {
    header("Location: cari.php");
    exit;
}

echo startLayout("Detail Buku - " . $buku['judul']);
?>

<div class="row g-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="cari.php" class="text-decoration-none">Cari Buku</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $buku['judul'] ?></li>
            </ol>
        </nav>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-12 col-md-4 col-lg-3">
                        <img src="<?= $buku['gambar'] ?>" 
                            alt="<?= $buku['judul'] ?>" 
                            class="img-fluid rounded-3 w-100" 
                            style="aspect-ratio: 3/4; object-fit: cover;">

                        <div class="d-grid gap-2 mt-4">
                            <?php if (isset($_SESSION['admin'])): ?>
                                <a href="admin/buku/detail.php?id=<?= $buku['id_buku'] ?>" 
                                    class="btn btn-primary rounded-pill">
                                    <i class="bi bi-pencil-square me-1"></i>Kelola Buku
                                </a>
                            <?php endif; ?>
                            
                            <?php if (!isset($_SESSION['admin']) && !isset($_SESSION['siswa'])): ?>
                                <a href="siswa/auth/login.php" class="btn btn-primary rounded-pill">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12 col-md-8 col-lg-9">
                        <div class="d-flex gap-2 mb-3 flex-wrap">
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <?= $buku['nama_kategori'] ?>
                            </span>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                Kelas <?= $buku['kelas_fokus'] ?>
                            </span>
                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                <?= $buku['jurusan_fokus'] ?>
                            </span>
                        </div>

                        <h2 class="mb-2"><?= $buku['judul'] ?></h2>
                        <p class="text-muted mb-4">oleh <?= $buku['penulis'] ?></p>

                        <div class="row g-4">
                            <div class="col-12 col-lg-6">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">Informasi Buku</h6>
                                        <div class="d-flex flex-column gap-2 text-muted small">
                                            <?php if ($buku['isbn']): ?>
                                                <div class="d-flex justify-content-between">
                                                    <span>ISBN</span>
                                                    <span><?= $buku['isbn'] ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($buku['penerbit']): ?>
                                                <div class="d-flex justify-content-between">
                                                    <span>Penerbit</span>
                                                    <span><?= $buku['penerbit'] ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($buku['tahun_terbit']): ?>
                                                <div class="d-flex justify-content-between">
                                                    <span>Tahun Terbit</span>
                                                    <span><?= $buku['tahun_terbit'] ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($buku['jumlah_halaman']): ?>
                                                <div class="d-flex justify-content-between">
                                                    <span>Jumlah Halaman</span>
                                                    <span><?= $buku['jumlah_halaman'] ?> halaman</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">Lokasi Buku</h6>
                                        <div class="d-flex flex-column gap-2 text-muted small">
                                            <div class="d-flex justify-content-between">
                                                <span>Nomor Rak</span>
                                                <span><?= $buku['nomor_rak'] ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Lokasi</span>
                                                <span><?= $buku['lokasi'] ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Status</span>
                                                <span class="text-<?= ($buku['stok'] > $buku['sedang_dipinjam']) ? 'success' : 'danger' ?>">
                                                    <?= ($buku['stok'] > $buku['sedang_dipinjam']) ? 'Tersedia' : 'Tidak Tersedia' ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($_SESSION['siswa'])): ?>
                            <div class="alert alert-info mt-4">
                                <i class="bi bi-info-circle me-2"></i>
                                Untuk meminjam buku, silakan kunjungi perpustakaan dan hubungi petugas.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= endLayout(); ?>