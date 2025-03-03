<?php
// Path: detail-ebook.php

require_once "globals/layouts/main.php";
require_once "globals/config/database.php";

if (!isset($_GET['id'])) {
    header("Location: ebook.php");
    exit;
}

$id_ebook = $_GET['id'];

$query = $db->prepare("
    SELECT e.*,
    COUNT(DISTINCT rbe.id_siswa) as total_pembaca,
    COUNT(DISTINCT CASE WHEN rbe.tanggal_baca >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN rbe.id_siswa END) as pembaca_bulan_ini
    FROM ebook e
    LEFT JOIN riwayat_baca_ebook rbe ON e.id_ebook = rbe.id_ebook
    WHERE e.id_ebook = ?
    GROUP BY e.id_ebook, e.judul, e.penulis, e.penerbit, e.tahun_terbit, 
             e.isbn, e.jumlah_halaman, e.file_path, e.gambar, e.kelas_fokus, 
             e.jurusan_fokus, e.created_at, e.updated_at
");

$query->execute([$id_ebook]);
$ebook = $query->fetch();

if (!$ebook) {
    header("Location: ebook.php");
    exit;
}

startLayout("Detail E-Book - " . $ebook['judul']);
?>

<div class="row g-4">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="ebook.php" class="text-decoration-none">E-Book</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $ebook['judul'] ?></li>
            </ol>
        </nav>
    </div>

    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-12 col-md-4 col-lg-3">
                        <img src="<?= $ebook['gambar'] ?>" alt="<?= $ebook['judul'] ?>" class="img-fluid rounded-3 w-100" style="object-fit: cover;">

                        <div class="d-grid gap-2 mt-4">
                            <?php if (isset($_SESSION['siswa'])): ?>
                                <?php
                                // Cek riwayat baca ebook ini
                                $query = $db->prepare("
                                    SELECT MAX(tanggal_baca) as terakhir_baca
                                    FROM riwayat_baca_ebook 
                                    WHERE id_siswa = ? AND id_ebook = ?
                                ");
                                $query->execute([$_SESSION['siswa']['id'], $ebook['id_ebook']]);
                                $riwayat = $query->fetch();
                                ?>

                                <a href="siswa/ebook/baca.php?id=<?= $ebook['id_ebook'] ?>" class="btn btn-primary rounded-pill">
                                    <i class="bi bi-book me-1"></i>
                                    <?= $riwayat ? 'Lanjutkan Membaca' : 'Baca Sekarang' ?>
                                </a>

                                <?php if ($riwayat): ?>
                                    <div class="text-center text-muted small">
                                        Terakhir dibaca: <?= $riwayat && $riwayat['terakhir_baca'] ? date('d/m/Y H:i', strtotime($riwayat['terakhir_baca'])) : '-' ?>
                                    </div>
                                <?php endif; ?>

                            <?php elseif (isset($_SESSION['admin'])): ?>
                                <a href="admin/ebook/detail.php?id=<?= $ebook['id_ebook'] ?>" class="btn btn-primary rounded-pill">
                                    <i class="bi bi-pencil-square me-1"></i>Kelola E-Book
                                </a>
                            <?php else: ?>
                                <a href="siswa/auth/login.php" class="btn btn-primary rounded-pill">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Login untuk Membaca
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12 col-md-8 col-lg-9">
                        <div class="d-flex gap-2 mb-3 flex-wrap">
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

                        <h2 class="mb-2"><?= $ebook['judul'] ?></h2>
                        <p class="text-muted mb-4">oleh <?= $ebook['penulis'] ?></p>

                        <div class="row g-4">
                            <div class="col-12 col-lg-6">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">Informasi E-Book</h6>
                                        <div class="d-flex flex-column gap-2 text-muted small">
                                            <?php if ($ebook['isbn']): ?>
                                                <div class="d-flex justify-content-between">
                                                    <span>ISBN</span>
                                                    <span><?= $ebook['isbn'] ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($ebook['penerbit']): ?>
                                                <div class="d-flex justify-content-between">
                                                    <span>Penerbit</span>
                                                    <span><?= $ebook['penerbit'] ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($ebook['tahun_terbit']): ?>
                                                <div class="d-flex justify-content-between">
                                                    <span>Tahun Terbit</span>
                                                    <span><?= $ebook['tahun_terbit'] ?></span>
                                                </div>
                                            <?php endif; ?>

                                            <?php if ($ebook['jumlah_halaman']): ?>
                                                <div class="d-flex justify-content-between">
                                                    <span>Jumlah Halaman</span>
                                                    <span><?= $ebook['jumlah_halaman'] ?> halaman</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="card h-100 bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">Statistik Pembaca</h6>
                                        <div class="d-flex flex-column gap-2 text-muted small">
                                            <div class="d-flex justify-content-between">
                                                <span>Total Pembaca</span>
                                                <span><?= $ebook['total_pembaca'] ?> siswa</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Pembaca Bulan Ini</span>
                                                <span><?= $ebook['pembaca_bulan_ini'] ?> siswa</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Terakhir Diperbarui</span>
                                                <span><?= $ebook['updated_at'] ? date('d/m/Y', strtotime($ebook['updated_at'])) : '-' ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <div class="col-12">
                                    <div class="alert alert-light border-0">
                                        <div class="d-flex gap-3 align-items-center">
                                            <i class="bi bi-info-circle fs-4 text-primary"></i>
                                            <div>
                                                <h6 class="mb-1">Login untuk Membaca</h6>
                                                <p class="mb-0 text-muted">Silakan login menggunakan akun siswa Anda untuk mulai membaca e-book ini.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= endLayout(); ?>