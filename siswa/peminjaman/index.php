<?php
// Path: siswa/peminjaman/index.php

require_once "../layouts/siswa-layout.php";
require_once "../../globals/config/database.php";

$siswa_id = $_SESSION['siswa']['id'];

// Handle delete individual loan
if (isset($_POST['delete_single']) && isset($_POST['id_peminjaman'])) {
    try {
        $delete_query = $db->prepare("
            DELETE FROM peminjaman 
            WHERE id_peminjaman = ? AND id_siswa = ? AND status = 'dikembalikan'
        ");
        $delete_query->execute([$_POST['id_peminjaman'], $siswa_id]);

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Riwayat peminjaman berhasil dihapus.'
        ];

        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Gagal menghapus riwayat peminjaman: ' . $e->getMessage()
        ];
    }
}

// Handle delete all completed loans
if (isset($_POST['delete_all_completed'])) {
    try {
        $delete_query = $db->prepare("
            DELETE FROM peminjaman 
            WHERE id_siswa = ? AND status = 'dikembalikan'
        ");
        $delete_query->execute([$siswa_id]);

        $_SESSION['message'] = [
            'type' => 'success',
            'text' => 'Semua riwayat peminjaman yang sudah dikembalikan berhasil dihapus.'
        ];

        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['message'] = [
            'type' => 'danger',
            'text' => 'Gagal menghapus riwayat peminjaman: ' . $e->getMessage()
        ];
    }
}

$query = $db->prepare("
    SELECT p.*, b.judul, b.gambar, b.penulis,
           DATEDIFF(p.tanggal_jatuh_tempo, CURRENT_DATE) as sisa_hari
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.id_siswa = ?
    ORDER BY 
        CASE p.status
            WHEN 'dipinjam' THEN 1
            WHEN 'terlambat' THEN 2
            WHEN 'dikembalikan' THEN 3
        END,
        p.tanggal_jatuh_tempo ASC
");
$query->execute([$siswa_id]);
$peminjaman = $query->fetchAll();

echo startLayout("Peminjaman Buku");
?>

<div class="col-12">
    <?php
    // Display session messages
    if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message']['type'] ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['message']['text'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php
        // Clear the message after displaying
        unset($_SESSION['message']);
    endif;
    ?>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Daftar Peminjaman Buku</h5>

                <?php
                // Only show delete buttons if there are completed loans
                $completed_loans = array_filter($peminjaman, function ($pinjam) {
                    return $pinjam['status'] === 'dikembalikan';
                });

                if (!empty($completed_loans)): ?>
                    <div class="d-flex gap-2">
                        <form method="POST" onsubmit="return confirm('Anda yakin ingin menghapus semua riwayat peminjaman yang sudah dikembalikan?');">
                            <button type="submit" name="delete_all_completed" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash me-1"></i>Hapus Semua Riwayat
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (empty($peminjaman)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted mb-3"></i>
                    <h6 class="mb-2">Belum Ada Peminjaman</h6>
                    <p class="text-muted mb-0">Anda belum pernah meminjam buku dari perpustakaan.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col">Buku</th>
                                <th scope="col">Tanggal Pinjam</th>
                                <th scope="col">Jatuh Tempo</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($peminjaman as $pinjam): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="../../<?= $pinjam['gambar'] ?>"
                                                alt="<?= $pinjam['judul'] ?>"
                                                width="48" height="64"
                                                class="rounded object-fit-cover">
                                            <div>
                                                <h6 class="mb-1"><?= $pinjam['judul'] ?></h6>
                                                <p class="text-muted small mb-0"><?= $pinjam['penulis'] ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($pinjam['tanggal_pinjam'])) ?></td>
                                    <td><?= date('d/m/Y', strtotime($pinjam['tanggal_jatuh_tempo'])) ?></td>
                                    <td>
                                        <?php if ($pinjam['status'] === 'dikembalikan'): ?>
                                            <span class="badge bg-success">Dikembalikan</span>
                                        <?php elseif ($pinjam['sisa_hari'] < 0): ?>
                                            <span class="badge bg-danger">Terlambat <?= abs($pinjam['sisa_hari']) ?> hari</span>
                                        <?php elseif ($pinjam['sisa_hari'] <= 3): ?>
                                            <span class="badge bg-warning">Sisa <?= $pinjam['sisa_hari'] ?> hari</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Sisa <?= $pinjam['sisa_hari'] ?> hari</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="detail.php?id=<?= $pinjam['id_peminjaman'] ?>"
                                                class="btn btn-sm btn-primary rounded-pill">
                                                <i class="bi bi-info-circle me-1"></i>Detail
                                            </a>
                                            <a href="../../detail-buku.php?id=<?= $pinjam['id_buku'] ?>"
                                                class="btn btn-sm btn-outline-primary rounded-pill">
                                                <i class="bi bi-book me-1"></i>Lihat Buku
                                            </a>
                                            <?php if ($pinjam['status'] === 'dikembalikan'): ?>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus riwayat peminjaman ini?');">
                                                    <input type="hidden" name="id_peminjaman" value="<?= $pinjam['id_peminjaman'] ?>">
                                                    <button type="submit" name="delete_single" class="btn btn-sm btn-outline-danger rounded-pill">
                                                        <i class="bi bi-trash me-1"></i>Hapus
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= endLayout(); ?>