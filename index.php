<?php
// Path: index.php

require_once "globals/layouts/main.php";
require_once "globals/config/database.php";

startLayout();
?>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 bg-primary text-white rounded-4 overflow-hidden">
            <div class="card-body p-4 p-md-5 position-relative">
                <div class="row align-items-center">
                    <div class="col-12 col-lg-7 mb-4 mb-lg-0">
                        <h1 class="display-5 fw-bold mb-4">Akses Perpustakaan Digital</h1>
                        <p class="lead mb-4">Temukan koleksi buku dan e-book untuk mendukung pembelajaran Anda. Akses kapan saja dan di mana saja.</p>
                        <form action="cari.php" method="GET" class="d-flex gap-2 bg-white p-2 rounded-pill">
                            <input type="search" name="q" class="form-control form-control-lg border-0 rounded-pill" placeholder="Cari buku atau e-book...">
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                    <div class="col-12 col-lg-5 text-center">
                        <img src="assets/images/library-hero.jpg" alt="Library Illustration" class="img-fluid rounded-3" style="max-height: 300px;">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-book fs-4 text-primary"></i>
                            </div>
                            <h5 class="card-title mb-0">Koleksi Buku</h5>
                        </div>
                        <p class="text-muted mb-0">Akses berbagai koleksi buku fisik untuk dipinjam</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-journal-text fs-4 text-primary"></i>
                            </div>
                            <h5 class="card-title mb-0">E-Book</h5>
                        </div>
                        <p class="text-muted mb-0">Baca e-book kapan saja dan di mana saja</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-journal-check fs-4 text-primary"></i>
                            </div>
                            <h5 class="card-title mb-0">Peminjaman</h5>
                        </div>
                        <p class="text-muted mb-0">Pinjam dan kembalikan buku dengan mudah</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-person-check fs-4 text-primary"></i>
                            </div>
                            <h5 class="card-title mb-0">Akun Pengguna</h5>
                        </div>
                        <p class="text-muted mb-0">Kelola akun dan aktivitas perpustakaan Anda</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Buku Terbaru</h5>
                            <a href="cari.php" class="btn btn-outline-primary rounded-pill px-4">Lihat Semua</a>
                        </div>
                        <div class="row g-4">
                            <?php
                            $query = $db->query("
                                SELECT b.id_buku, b.judul, b.penulis, b.gambar, kb.nama_kategori 
                                FROM buku b 
                                JOIN kategori_buku kb ON b.id_kategori = kb.id_kategori 
                                ORDER BY b.created_at DESC LIMIT 4
                            ");
                            while ($buku = $query->fetch()) {
                            ?>
                                <div class="col-12 col-md-6">
                                    <div class="d-flex gap-3">
                                        <img src="<?= $buku['gambar'] ?>" alt="<?= $buku['judul'] ?>" class="rounded-3" style="width: 100px; height: 140px; object-fit: cover;">
                                        <div>
                                            <span class="badge bg-primary bg-opacity-10 text-primary mb-2"><?= $buku['nama_kategori'] ?></span>
                                            <h6 class="mb-1"><?= $buku['judul'] ?></h6>
                                            <p class="text-muted small mb-2"><?= $buku['penulis'] ?></p>
                                            <a href="detail-buku.php?id=<?= $buku['id_buku'] ?>" class="btn btn-primary btn-sm rounded-pill px-3">
                                                Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">E-Book Populer</h5>
                            <a href="ebook.php" class="btn btn-outline-primary rounded-pill px-4">Lihat Semua</a>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <?php
                            $query = $db->query("
                                SELECT e.id_ebook, e.judul, e.penulis, e.kelas_fokus, e.jurusan_fokus,
                                COUNT(rbe.id_riwayat) as total_baca
                                FROM ebook e
                                LEFT JOIN riwayat_baca_ebook rbe ON e.id_ebook = rbe.id_ebook
                                GROUP BY e.id_ebook, e.judul, e.penulis, e.kelas_fokus, e.jurusan_fokus
                                ORDER BY total_baca DESC
                                LIMIT 5
                            ");
                            while ($ebook = $query->fetch()) {
                            ?>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                                        <i class="bi bi-file-pdf fs-5 text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 text-truncate"><?= $ebook['judul'] ?></h6>
                                        <p class="text-muted small mb-0"><?= $ebook['penulis'] ?></p>
                                    </div>
                                    <a href="detail-ebook.php?id=<?= $ebook['id_ebook'] ?>" class="btn btn-primary btn-sm rounded-pill px-3">
                                        Baca
                                    </a>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">Kategori</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <?php
                            $query = $db->query("
                                SELECT kb.id_kategori, kb.nama_kategori, COUNT(b.id_buku) as total_buku
                                FROM kategori_buku kb
                                LEFT JOIN buku b ON kb.id_kategori = b.id_kategori
                                GROUP BY kb.id_kategori, kb.nama_kategori
                                ORDER BY total_buku DESC
                                LIMIT 6
                            ");
                            while ($kategori = $query->fetch()) {
                            ?>
                                <a href="cari.php?kategori=<?= $kategori['id_kategori'] ?>"
                                    class="badge bg-primary bg-opacity-10 text-primary text-decoration-none p-2 px-3">
                                    <?= $kategori['nama_kategori'] ?> (<?= $kategori['total_buku'] ?>)
                                </a>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm bg-primary bg-opacity-10 text-center">
            <div class="card-body p-4 p-md-5">
                <h2 class="mb-4">Belum Memiliki Akun?</h2>
                <p class="lead mb-4">Silakan hubungi administrator untuk mendapatkan akun perpustakaan digital</p>
                <a href="siswa/auth/login.php" class="btn btn-primary btn-lg rounded-pill px-5">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                </a>
            </div>
        </div>
    </div>
</div>

<?= endLayout(); ?>