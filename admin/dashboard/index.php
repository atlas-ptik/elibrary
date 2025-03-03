<?php
// Path: admin/dashboard/index.php

session_start();
require_once "../../globals/config/database.php";
require_once "../middleware/auth.php";
require_once "../layouts/admin-layout.php";

// Cek autentikasi
cekAuthAdmin();

// Tampilkan layout
echo adminLayout("Dashboard - Elibrary", "dashboard");

try {
    // Get total counts
    $total_buku = $db->query("SELECT COUNT(*) FROM buku")->fetchColumn();
    $total_ebook = $db->query("SELECT COUNT(*) FROM ebook")->fetchColumn();
    $total_siswa = $db->query("SELECT COUNT(*) FROM siswa WHERE status_aktif = TRUE")->fetchColumn();
    $total_peminjaman = $db->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'dipinjam'")->fetchColumn();

    // Get recent activities
    $aktivitas = $db->query("
        SELECT 
            la.aktivitas,
            la.detail,
            la.created_at,
            CASE 
                WHEN la.tipe_pengguna = 'admin' THEN a.nama_lengkap
                WHEN la.tipe_pengguna = 'siswa' THEN s.nama_lengkap
            END as nama_pengguna,
            la.tipe_pengguna
        FROM log_aktivitas la
        LEFT JOIN admin a ON la.id_pengguna = a.id_admin AND la.tipe_pengguna = 'admin'
        LEFT JOIN siswa s ON la.id_pengguna = s.id_siswa AND la.tipe_pengguna = 'siswa'
        ORDER BY la.created_at DESC
        LIMIT 5
    ")->fetchAll();

    // Get recent borrowings
    $peminjaman = $db->query("
        SELECT 
            p.*,
            s.nama_lengkap as nama_siswa,
            b.judul as judul_buku,
            DATEDIFF(p.tanggal_jatuh_tempo, CURRENT_DATE) as sisa_hari
        FROM peminjaman p
        JOIN siswa s ON p.id_siswa = s.id_siswa
        JOIN buku b ON p.id_buku = b.id_buku
        WHERE p.status = 'dipinjam'
        ORDER BY p.tanggal_pinjam DESC
        LIMIT 5
    ")->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<div class="row g-4">
    <!-- Welcome Card -->
    <div class="col-12">
        <div class="card border-0 bg-primary text-white rounded-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Selamat Datang, <?= $_SESSION['admin_nama'] ?>!</h4>
                        <p class="mb-0">Anda login sebagai Admin Elibrary</p>
                    </div>
                    <i class="bi bi-person-circle display-5"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Total Buku</p>
                        <h4 class="mb-0"><?= number_format($total_buku) ?></h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-book fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Total E-Book</p>
                        <h4 class="mb-0"><?= number_format($total_ebook) ?></h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-journal-text fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Siswa Aktif</p>
                        <h4 class="mb-0"><?= number_format($total_siswa) ?></h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-people fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted small mb-1">Peminjaman Aktif</p>
                        <h4 class="mb-0"><?= number_format($total_peminjaman) ?></h4>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                        <i class="bi bi-journal-check fs-4 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Borrowings -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="card-title mb-4">Peminjaman Terbaru</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Siswa</th>
                                <th>Buku</th>
                                <th>Status</th>
                                <th>Sisa Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($peminjaman as $pinjam): ?>
                                <tr>
                                    <td><?= $pinjam['nama_siswa'] ?></td>
                                    <td><?= $pinjam['judul_buku'] ?></td>
                                    <td>
                                        <?php if ($pinjam['sisa_hari'] < 0): ?>
                                            <span class="badge bg-danger">Terlambat</span>
                                        <?php else: ?>
                                            <span class="badge bg-primary">Dipinjam</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($pinjam['sisa_hari'] < 0): ?>
                                            <span class="text-danger">
                                                Terlambat <?= abs($pinjam['sisa_hari']) ?> hari
                                            </span>
                                        <?php else: ?>
                                            <?= $pinjam['sisa_hari'] ?> hari
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($peminjaman)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">
                                        Tidak ada peminjaman aktif
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <a href="../peminjaman/index.php" class="btn btn-primary rounded-pill px-4">
                        Lihat Semua
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="card-title mb-4">Aktivitas Terbaru</h5>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($aktivitas as $log): ?>
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-3">
                                <?php if ($log['tipe_pengguna'] === 'admin'): ?>
                                    <i class="bi bi-person-badge fs-5 text-primary"></i>
                                <?php else: ?>
                                    <i class="bi bi-person fs-5 text-primary"></i>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1">
                                    <span class="fw-medium"><?= $log['nama_pengguna'] ?></span>
                                    <?= $log['aktivitas'] ?>
                                </p>
                                <p class="text-muted small mb-0">
                                    <?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($aktivitas)): ?>
                        <div class="text-center py-3 text-muted">
                            Tidak ada aktivitas terbaru
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= endLayout(); ?>