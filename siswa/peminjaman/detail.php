<?php
// Path: siswa/peminjaman/detail.php

require_once "../layouts/siswa-layout.php";
require_once "../../globals/config/database.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$siswa_id = $_SESSION['siswa']['id'];
$peminjaman_id = $_GET['id'];

$query = $db->prepare("
    SELECT p.*, b.judul, b.gambar, b.penulis, b.penerbit, b.tahun_terbit,
           b.isbn, kb.nama_kategori, rb.nomor_rak, rb.lokasi,
           DATEDIFF(p.tanggal_jatuh_tempo, CURRENT_DATE) as sisa_hari
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id_buku
    JOIN kategori_buku kb ON b.id_kategori = kb.id_kategori
    JOIN rak_buku rb ON b.id_rak = rb.id_rak
    WHERE p.id_peminjaman = ? AND p.id_siswa = ?
");
$query->execute([$peminjaman_id, $siswa_id]);
$peminjaman = $query->fetch();

if (!$peminjaman) {
    header("Location: index.php");
    exit;
}

echo startLayout("Detail Peminjaman");
?>

<div class="col-12">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Detail Peminjaman Buku</h5>
                <a href="index.php" class="btn btn-outline-primary rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-3">
                    <img src="../../<?= $peminjaman['gambar'] ?>"
                        alt="<?= $peminjaman['judul'] ?>"
                        class="img-fluid rounded shadow-sm w-100"
                        style="aspect-ratio: 3/4; object-fit: cover;">
                </div>

                <div class="col-12 col-md-9">
                    <div class="d-flex flex-column h-100">
                        <div class="mb-4">
                            <div class="d-flex gap-2 mb-3">
                                <span class="badge bg-primary"><?= $peminjaman['nama_kategori'] ?></span>
                                <?php if ($peminjaman['status'] === 'dikembalikan'): ?>
                                    <span class="badge bg-success">Dikembalikan</span>
                                <?php elseif ($peminjaman['sisa_hari'] < 0): ?>
                                    <span class="badge bg-danger">Terlambat <?= abs($peminjaman['sisa_hari']) ?> hari</span>
                                <?php elseif ($peminjaman['sisa_hari'] <= 3): ?>
                                    <span class="badge bg-warning">Sisa <?= $peminjaman['sisa_hari'] ?> hari</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Sisa <?= $peminjaman['sisa_hari'] ?> hari</span>
                                <?php endif; ?>
                            </div>

                            <h4 class="mb-2"><?= $peminjaman['judul'] ?></h4>
                            <p class="text-muted mb-0"><?= $peminjaman['penulis'] ?></p>
                        </div>

                        <div class="row g-4">
                            <div class="col-12 col-lg-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="mb-3">Informasi Buku</h6>
                                        <div class="d-flex flex-column gap-2">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Penerbit</span>
                                                <span class="fw-medium"><?= $peminjaman['penerbit'] ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Tahun Terbit</span>
                                                <span class="fw-medium"><?= $peminjaman['tahun_terbit'] ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">ISBN</span>
                                                <span class="fw-medium"><?= $peminjaman['isbn'] ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Lokasi</span>
                                                <span class="fw-medium">Rak <?= $peminjaman['nomor_rak'] ?> (<?= $peminjaman['lokasi'] ?>)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="mb-3">Detail Peminjaman</h6>
                                        <div class="d-flex flex-column gap-2">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Tanggal Pinjam</span>
                                                <span class="fw-medium"><?= date('d/m/Y', strtotime($peminjaman['tanggal_pinjam'])) ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Jatuh Tempo</span>
                                                <span class="fw-medium"><?= date('d/m/Y', strtotime($peminjaman['tanggal_jatuh_tempo'])) ?></span>
                                            </div>
                                            <?php if ($peminjaman['tanggal_kembali']): ?>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Tanggal Kembali</span>
                                                    <span class="fw-medium"><?= date('d/m/Y', strtotime($peminjaman['tanggal_kembali'])) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Status</span>
                                                <span class="fw-medium">
                                                    <?php if ($peminjaman['status'] === 'dikembalikan'): ?>
                                                        <span class="text-success">Dikembalikan</span>
                                                    <?php elseif ($peminjaman['sisa_hari'] < 0): ?>
                                                        <span class="text-danger">Terlambat <?= abs($peminjaman['sisa_hari']) ?> hari</span>
                                                    <?php else: ?>
                                                        <span class="text-success">Masih dipinjam</span>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                            <?php if ($peminjaman['keterangan']): ?>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Keterangan</span>
                                                    <span class="fw-medium"><?= $peminjaman['keterangan'] ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="../../detail-buku.php?id=<?= $peminjaman['id_buku'] ?>" 
                                class="btn btn-primary rounded-pill">
                                <i class="bi bi-book me-1"></i>Lihat Detail Buku
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= endLayout(); ?>