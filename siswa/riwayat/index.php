<?php
// Path: siswa/riwayat/index.php

require_once "../layouts/siswa-layout.php";
require_once "../../globals/config/database.php";

$siswa_id = $_SESSION['siswa']['id'];

// Check if there's a delete action
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_riwayat = $_GET['id'];

    try {
        $query = $db->prepare("DELETE FROM riwayat_baca_ebook WHERE id_riwayat = ? AND id_siswa = ?");
        $query->execute([$id_riwayat, $siswa_id]);

        // Redirect to avoid resubmission
        header("Location: index.php?status=success&message=Riwayat berhasil dihapus");
        exit();
    } catch (PDOException $e) {
        header("Location: index.php?status=error&message=Gagal menghapus riwayat");
        exit();
    }
}

// Check if the clear all action is triggered
if (isset($_GET['action']) && $_GET['action'] == 'clear_all') {
    try {
        $query = $db->prepare("DELETE FROM riwayat_baca_ebook WHERE id_siswa = ?");
        $query->execute([$siswa_id]);

        // Redirect to avoid resubmission
        header("Location: index.php?status=success&message=Semua riwayat berhasil dihapus");
        exit();
    } catch (PDOException $e) {
        header("Location: index.php?status=error&message=Gagal menghapus semua riwayat");
        exit();
    }
}

// Modified query to remove the kategori_ebook table join
$query = $db->prepare("
    SELECT rbe.*, e.judul, e.penulis, e.penerbit, e.tahun_terbit, e.gambar,
           e.kelas_fokus, e.jurusan_fokus
    FROM riwayat_baca_ebook rbe
    JOIN ebook e ON rbe.id_ebook = e.id_ebook
    WHERE rbe.id_siswa = ?
    ORDER BY rbe.tanggal_baca DESC
");
$query->execute([$siswa_id]);
$riwayat = $query->fetchAll();

$query = $db->prepare("
    SELECT COUNT(DISTINCT id_ebook) as total_ebook,
           COUNT(*) as total_baca
    FROM riwayat_baca_ebook
    WHERE id_siswa = ?
");
$query->execute([$siswa_id]);
$statistik = $query->fetch();

echo startLayout("Riwayat Baca E-Book");
?>

<!-- Alert untuk notifikasi -->
<?php if (isset($_GET['status'])): ?>
    <div class="alert alert-<?= $_GET['status'] == 'success' ? 'success' : 'danger' ?> alert-dismissible fade show mb-4" role="alert">
        <?= htmlspecialchars($_GET['message']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="col-12">
    <div class="row g-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-book fs-4 text-primary"></i>
                            <h6 class="mb-0">Total E-Book</h6>
                        </div>
                        <span class="badge bg-primary rounded-pill"><?= $statistik['total_ebook'] ?></span>
                    </div>
                    <p class="text-muted small mb-0">Jumlah e-book yang pernah Anda baca</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history fs-4 text-primary"></i>
                            <h6 class="mb-0">Total Aktivitas</h6>
                        </div>
                        <span class="badge bg-primary rounded-pill"><?= $statistik['total_baca'] ?></span>
                    </div>
                    <p class="text-muted small mb-0">Total aktivitas membaca e-book Anda</p>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Riwayat Baca E-Book</h5>
                        <?php if (!empty($riwayat)): ?>
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#clearAllModal">
                                <i class="bi bi-trash me-1"></i>Bersihkan Semua
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($riwayat)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                            <h6 class="mb-2">Belum Ada Riwayat</h6>
                            <p class="text-muted mb-4">Anda belum pernah membaca e-book.</p>
                            <a href="../../ebook.php" class="btn btn-primary rounded-pill">
                                <i class="bi bi-journal-text me-1"></i>Lihat E-Book
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col">E-Book</th>
                                        <th scope="col">Fokus</th>
                                        <th scope="col">Waktu Baca</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($riwayat as $baca): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="../../<?= $baca['gambar'] ?>"
                                                        alt="<?= $baca['judul'] ?>"
                                                        width="48" height="64"
                                                        class="rounded object-fit-cover">
                                                    <div>
                                                        <h6 class="mb-1"><?= $baca['judul'] ?></h6>
                                                        <p class="text-muted small mb-0">
                                                            <?= $baca['penulis'] ?>
                                                            <span class="mx-1">&bull;</span>
                                                            <?= $baca['tahun_terbit'] ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                                        Kelas <?= $baca['kelas_fokus'] ?>
                                                        <?= $baca['jurusan_fokus'] !== 'UMUM' ? $baca['jurusan_fokus'] : '' ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="fw-medium"><?= date('d/m/Y', strtotime($baca['tanggal_baca'])) ?></span>
                                                    <span class="text-muted small"><?= date('H:i', strtotime($baca['tanggal_baca'])) ?> WIB</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="../../detail-ebook.php?id=<?= $baca['id_ebook'] ?>"
                                                        class="btn btn-sm btn-primary rounded-pill">
                                                        <i class="bi bi-book me-1"></i>Baca
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger rounded-pill"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal<?= $baca['id_riwayat'] ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- Modal Hapus Riwayat -->
                                        <div class="modal fade" id="deleteModal<?= $baca['id_riwayat'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Hapus Riwayat</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Apakah Anda yakin ingin menghapus riwayat baca "<?= $baca['judul'] ?>"?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <a href="index.php?action=delete&id=<?= $baca['id_riwayat'] ?>" class="btn btn-danger">Hapus</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus Semua Riwayat -->
<div class="modal fade" id="clearAllModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bersihkan Semua Riwayat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus semua riwayat baca? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="index.php?action=clear_all" class="btn btn-danger">Hapus Semua</a>
            </div>
        </div>
    </div>
</div>

<?= endLayout(); ?>