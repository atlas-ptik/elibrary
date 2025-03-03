<?php
// Path: siswa/dashboard/index.php

require_once "../layouts/siswa-layout.php";
require_once "../../globals/config/database.php";

$siswa_id = $_SESSION['siswa']['id'];

// Ambil data peminjaman aktif
$query = $db->prepare("
    SELECT p.*, b.judul, b.gambar,
           DATEDIFF(p.tanggal_jatuh_tempo, CURRENT_DATE) as sisa_hari
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.id_siswa = ? AND p.status = 'dipinjam'
    ORDER BY p.tanggal_jatuh_tempo ASC
");
$query->execute([$siswa_id]);
$peminjaman_aktif = $query->fetchAll();

// Ambil riwayat baca e-book terakhir dengan DISTINCT pada id_ebook
// untuk menghindari duplikasi ebook yang sama
$query = $db->prepare("
    SELECT DISTINCT e.id_ebook, e.judul, e.gambar, e.kelas_fokus, e.jurusan_fokus,
           (SELECT MAX(tanggal_baca) FROM riwayat_baca_ebook 
            WHERE id_ebook = e.id_ebook AND id_siswa = ?) as tanggal_baca
    FROM ebook e
    JOIN riwayat_baca_ebook rbe ON e.id_ebook = rbe.id_ebook
    WHERE rbe.id_siswa = ?
    ORDER BY tanggal_baca DESC
    LIMIT 5
");
$query->execute([$siswa_id, $siswa_id]);
$riwayat_ebook = $query->fetchAll();

// Ambil total statistik
$query = $db->prepare("
    SELECT 
        (SELECT COUNT(*) FROM peminjaman WHERE id_siswa = ? AND status = 'dipinjam') as total_dipinjam,
        (SELECT COUNT(*) FROM peminjaman WHERE id_siswa = ? AND status = 'dikembalikan') as total_dikembalikan,
        (SELECT COUNT(DISTINCT id_ebook) FROM riwayat_baca_ebook WHERE id_siswa = ?) as total_ebook_dibaca
");
$query->execute([$siswa_id, $siswa_id, $siswa_id]);
$statistik = $query->fetch();

echo startLayout();
?>

<div class="col-12">
    <div class="row g-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-journal-check fs-4 text-primary"></i>
                            <h6 class="mb-0">Sedang Dipinjam</h6>
                        </div>
                        <span class="badge bg-primary rounded-pill"><?= $statistik['total_dipinjam'] ?></span>
                    </div>
                    <p class="text-muted small mb-0">Total buku yang sedang Anda pinjam</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-journal-text fs-4 text-primary"></i>
                            <h6 class="mb-0">E-Book Dibaca</h6>
                        </div>
                        <span class="badge bg-primary rounded-pill"><?= $statistik['total_ebook_dibaca'] ?></span>
                    </div>
                    <p class="text-muted small mb-0">Total e-book yang pernah Anda baca</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history fs-4 text-primary"></i>
                            <h6 class="mb-0">Riwayat Pengembalian</h6>
                        </div>
                        <span class="badge bg-primary rounded-pill"><?= $statistik['total_dikembalikan'] ?></span>
                    </div>
                    <p class="text-muted small mb-0">Total buku yang sudah Anda kembalikan</p>
                </div>
            </div>
        </div>

        <?php if ($peminjaman_aktif): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="mb-0">Buku yang Sedang Dipinjam</h6>
                            <a href="../peminjaman/index.php" class="btn btn-sm btn-primary rounded-pill">
                                Lihat Semua
                            </a>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">Buku</th>
                                        <th scope="col">Tanggal Pinjam</th>
                                        <th scope="col">Jatuh Tempo</th>
                                        <th scope="col">Status</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($peminjaman_aktif as $pinjam): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="../../<?= $pinjam['gambar'] ?>"
                                                        alt="<?= $pinjam['judul'] ?>"
                                                        width="48" height="64"
                                                        class="rounded object-fit-cover">
                                                    <div>
                                                        <h6 class="mb-1"><?= $pinjam['judul'] ?></h6>
                                                        <p class="text-muted small mb-0">
                                                            Dipinjam pada <?= date('d/m/Y', strtotime($pinjam['tanggal_pinjam'])) ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($pinjam['tanggal_pinjam'])) ?></td>
                                            <td><?= date('d/m/Y', strtotime($pinjam['tanggal_jatuh_tempo'])) ?></td>
                                            <td>
                                                <?php if ($pinjam['sisa_hari'] < 0): ?>
                                                    <span class="badge bg-danger">Terlambat <?= abs($pinjam['sisa_hari']) ?> hari</span>
                                                <?php elseif ($pinjam['sisa_hari'] <= 3): ?>
                                                    <span class="badge bg-warning">Sisa <?= $pinjam['sisa_hari'] ?> hari</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Sisa <?= $pinjam['sisa_hari'] ?> hari</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="../../detail-buku.php?id=<?= $pinjam['id_buku'] ?>"
                                                    class="btn btn-sm btn-primary rounded-pill">
                                                    Detail Buku
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($riwayat_ebook): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="mb-0">E-Book yang Terakhir Dibaca</h6>
                            <a href="../riwayat/index.php" class="btn btn-sm btn-primary rounded-pill">
                                Lihat Semua
                            </a>
                        </div>

                        <div class="row g-4">
                            <?php foreach ($riwayat_ebook as $ebook): ?>
                                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <img src="../../<?= $ebook['gambar'] ?>"
                                            class="card-img-top"
                                            alt="<?= $ebook['judul'] ?>"
                                            style="height: 240px; object-fit: cover;">
                                        <div class="card-body">
                                            <div class="d-flex gap-2 mb-2 flex-wrap">
                                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                                    Kelas <?= $ebook['kelas_fokus'] ?>
                                                </span>
                                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                                    <?= $ebook['jurusan_fokus'] ?>
                                                </span>
                                            </div>
                                            <h6 class="card-title mb-1"><?= $ebook['judul'] ?></h6>
                                            <p class="text-muted small mb-0">
                                                Terakhir dibaca <?= date('d/m/Y H:i', strtotime($ebook['tanggal_baca'])) ?>
                                            </p>
                                        </div>
                                        <div class="card-footer bg-white border-0">
                                            <a href="../../detail-ebook.php?id=<?= $ebook['id_ebook'] ?>"
                                                class="btn btn-primary w-100 rounded-pill">
                                                Baca Lagi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= endLayout(); ?>