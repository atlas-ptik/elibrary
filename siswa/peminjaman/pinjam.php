<?php
// Path: siswa/peminjaman/pinjam.php

require_once "../layouts/siswa-layout.php";
require_once "../../globals/config/database.php";

if (!isset($_GET['id'])) {
    header("Location: ../../cari.php");
    exit;
}

$siswa_id = $_SESSION['siswa']['id'];
$buku_id = $_GET['id'];

$query = $db->prepare("
    SELECT b.*, kb.nama_kategori, rb.nomor_rak, rb.lokasi
    FROM buku b
    JOIN kategori_buku kb ON b.id_kategori = kb.id_kategori
    JOIN rak_buku rb ON b.id_rak = rb.id_rak
    WHERE b.id_buku = ? AND b.stok > 0
");
$query->execute([$buku_id]);
$buku = $query->fetch();

if (!$buku) {
    header("Location: ../../cari.php");
    exit;
}

$query = $db->prepare("
    SELECT COUNT(*) as jumlah_pinjam
    FROM peminjaman
    WHERE id_siswa = ? AND status = 'dipinjam'
");
$query->execute([$siswa_id]);
$jumlah_pinjam = $query->fetch()['jumlah_pinjam'];

$query = $db->prepare("
    SELECT COUNT(*) as buku_dipinjam
    FROM peminjaman
    WHERE id_siswa = ? AND id_buku = ? AND status = 'dipinjam'
");
$query->execute([$siswa_id, $buku_id]);
$buku_dipinjam = $query->fetch()['buku_dipinjam'];

$batas_pinjam = 3;
$dapat_pinjam = $jumlah_pinjam < $batas_pinjam && $buku_dipinjam == 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $dapat_pinjam) {
    $query = $db->prepare("
        INSERT INTO peminjaman (
            id_peminjaman, id_siswa, id_buku, 
            tanggal_pinjam, tanggal_jatuh_tempo, status
        ) VALUES (
            UUID(), ?, ?, 
            CURRENT_DATE, DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY), 'dipinjam'
        )
    ");
    
    try {
        $db->beginTransaction();
        
        $query->execute([$siswa_id, $buku_id]);
        
        $query = $db->prepare("
            UPDATE buku SET stok = stok - 1
            WHERE id_buku = ?
        ");
        $query->execute([$buku_id]);
        
        $db->commit();
        
        header("Location: index.php");
        exit;
    } catch(Exception $e) {
        $db->rollBack();
        $error = "Terjadi kesalahan saat memproses peminjaman";
    }
}

echo startLayout("Pinjam Buku");
?>

<div class="col-12">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Pinjam Buku</h5>
                <a href="../../detail-buku.php?id=<?= $buku['id_buku'] ?>" class="btn btn-outline-primary rounded-pill">
                    <i class="bi bi-arrow-left me-1"></i>Kembali
                </a>
            </div>

            <div class="row g-4">
                <div class="col-12 col-md-3">
                    <img src="../../<?= $buku['gambar'] ?>"
                        alt="<?= $buku['judul'] ?>"
                        class="img-fluid rounded shadow-sm w-100"
                        style="aspect-ratio: 3/4; object-fit: cover;">
                </div>

                <div class="col-12 col-md-9">
                    <div class="d-flex flex-column h-100">
                        <div class="mb-4">
                            <div class="d-flex gap-2 mb-3">
                                <span class="badge bg-primary"><?= $buku['nama_kategori'] ?></span>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    Stok: <?= $buku['stok'] ?>
                                </span>
                            </div>

                            <h4 class="mb-2"><?= $buku['judul'] ?></h4>
                            <p class="text-muted mb-0"><?= $buku['penulis'] ?></p>
                        </div>

                        <div class="row g-4 mb-4">
                            <div class="col-12 col-lg-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="mb-3">Informasi Buku</h6>
                                        <div class="d-flex flex-column gap-2">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Penerbit</span>
                                                <span class="fw-medium"><?= $buku['penerbit'] ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Tahun Terbit</span>
                                                <span class="fw-medium"><?= $buku['tahun_terbit'] ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">ISBN</span>
                                                <span class="fw-medium"><?= $buku['isbn'] ?? '-' ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Lokasi</span>
                                                <span class="fw-medium">Rak <?= $buku['nomor_rak'] ?> (<?= $buku['lokasi'] ?>)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body">
                                        <h6 class="mb-3">Informasi Peminjaman</h6>
                                        <div class="d-flex flex-column gap-2">
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Buku Dipinjam</span>
                                                <span class="fw-medium"><?= $jumlah_pinjam ?> dari <?= $batas_pinjam ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Lama Peminjaman</span>
                                                <span class="fw-medium">7 hari</span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Tanggal Pinjam</span>
                                                <span class="fw-medium"><?= date('d/m/Y') ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Jatuh Tempo</span>
                                                <span class="fw-medium"><?= date('d/m/Y', strtotime('+7 days')) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger mb-4">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <div class="mt-auto">
                            <?php if ($buku_dipinjam > 0): ?>
                                <div class="alert alert-warning mb-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Anda sedang meminjam buku ini.
                                </div>
                                <a href="index.php" class="btn btn-primary rounded-pill">
                                    <i class="bi bi-journal-check me-1"></i>Lihat Peminjaman
                                </a>
                            <?php elseif ($jumlah_pinjam >= $batas_pinjam): ?>
                                <div class="alert alert-warning mb-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    Anda telah mencapai batas maksimal peminjaman (<?= $batas_pinjam ?> buku).
                                </div>
                                <a href="index.php" class="btn btn-primary rounded-pill">
                                    <i class="bi bi-journal-check me-1"></i>Lihat Peminjaman
                                </a>
                            <?php else: ?>
                                <form action="" method="POST">
                                    <button type="submit" class="btn btn-primary rounded-pill">
                                        <i class="bi bi-journal-plus me-1"></i>Pinjam Buku
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= endLayout(); ?>