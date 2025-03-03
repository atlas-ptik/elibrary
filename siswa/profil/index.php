<?php
// Path: siswa/profil/index.php

require_once "../layouts/siswa-layout.php";
require_once "../../globals/config/database.php";

$siswa_id = $_SESSION['siswa']['id'];

// Ambil data lengkap siswa
$query = $db->prepare("
    SELECT s.*,
        (SELECT COUNT(*) FROM peminjaman WHERE id_siswa = s.id_siswa AND status = 'dipinjam') as total_dipinjam,
        (SELECT COUNT(*) FROM peminjaman WHERE id_siswa = s.id_siswa AND status = 'dikembalikan') as total_dikembalikan,
        (SELECT COUNT(*) FROM peminjaman WHERE id_siswa = s.id_siswa AND status = 'terlambat') as total_terlambat,
        (SELECT COUNT(DISTINCT id_ebook) FROM riwayat_baca_ebook WHERE id_siswa = s.id_siswa) as total_ebook_dibaca
    FROM siswa s
    WHERE s.id_siswa = ?
");

$query->execute([$siswa_id]);
$siswa = $query->fetch();

echo startLayout("Profil Saya");
?>

<div class="col-12">
    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">
                    <img src="../../<?= $siswa['foto'] ?>"
                        alt="Foto Profil"
                        class="rounded-circle mb-3"
                        width="120" height="120"
                        style="object-fit: cover;">

                    <h5 class="mb-1"><?= $siswa['nama_lengkap'] ?></h5>
                    <p class="text-muted mb-3">NIS: <?= $siswa['nis'] ?></p>

                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Status</span>
                            <span class="badge bg-<?= $siswa['status_aktif'] ? 'success' : 'danger' ?>">
                                <?= $siswa['status_aktif'] ? 'Aktif' : 'Tidak Aktif' ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Kelas</span>
                            <span class="fw-medium"><?= $siswa['kelas'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Jurusan</span>
                            <span class="fw-medium"><?= $siswa['jurusan'] ?></span>
                        </div>
                        <div class="d-flex justify-content-between text-muted small">
                            <span>Bergabung</span>
                            <span class="fw-medium"><?= date('d/m/Y', strtotime($siswa['created_at'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="mb-3">Statistik Aktivitas</h6>

                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 bg-primary bg-opacity-10 rounded">
                                <i class="bi bi-journal-check text-primary"></i>
                            </div>
                            <div>
                                <p class="mb-0"><?= $siswa['total_dipinjam'] ?> Buku</p>
                                <p class="text-muted small mb-0">Sedang Dipinjam</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 bg-success bg-opacity-10 rounded">
                                <i class="bi bi-check-circle text-success"></i>
                            </div>
                            <div>
                                <p class="mb-0"><?= $siswa['total_dikembalikan'] ?> Buku</p>
                                <p class="text-muted small mb-0">Sudah Dikembalikan</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 bg-warning bg-opacity-10 rounded">
                                <i class="bi bi-exclamation-circle text-warning"></i>
                            </div>
                            <div>
                                <p class="mb-0"><?= $siswa['total_terlambat'] ?> Buku</p>
                                <p class="text-muted small mb-0">Terlambat Dikembalikan</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <div class="p-2 bg-info bg-opacity-10 rounded">
                                <i class="bi bi-journal-text text-info"></i>
                            </div>
                            <div>
                                <p class="mb-0"><?= $siswa['total_ebook_dibaca'] ?> E-Book</p>
                                <p class="text-muted small mb-0">Telah Dibaca</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h6 class="mb-4">Informasi Pribadi</h6>

                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Nama Lengkap</label>
                                <p class="mb-0"><?= $siswa['nama_lengkap'] ?></p>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">NIS</label>
                                <p class="mb-0"><?= $siswa['nis'] ?></p>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Username</label>
                                <p class="mb-0"><?= $siswa['username'] ?></p>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Email</label>
                                <p class="mb-0"><?= $siswa['email'] ?></p>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Nomor Telepon</label>
                                <p class="mb-0"><?= $siswa['no_telepon'] ?: '-' ?></p>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="mb-3">
                                <label class="form-label text-muted small">Terakhir Diperbarui</label>
                                <p class="mb-0"><?= date('d/m/Y H:i', strtotime($siswa['updated_at'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="mb-4">Ubah Password</h6>

                    <?php
                    if (isset($_POST['ubah_password'])) {
                        $password_lama = $_POST['password_lama'];
                        $password_baru = $_POST['password_baru'];
                        $konfirmasi_password = $_POST['konfirmasi_password'];

                        $error = false;
                        $pesan = '';

                        // Validasi password lama
                        $query = $db->prepare("SELECT password FROM siswa WHERE id_siswa = ?");
                        $query->execute([$siswa_id]);
                        $data = $query->fetch();

                        if (!password_verify($password_lama, $data['password'])) {
                            $error = true;
                            $pesan = 'Password lama tidak sesuai!';
                        }
                        // Validasi password baru dan konfirmasi
                        else if ($password_baru !== $konfirmasi_password) {
                            $error = true;
                            $pesan = 'Konfirmasi password baru tidak sesuai!';
                        }
                        // Validasi panjang password minimal 6 karakter
                        else if (strlen($password_baru) < 6) {
                            $error = true;
                            $pesan = 'Password baru minimal 6 karakter!';
                        }
                        // Update password
                        else {
                            $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
                            $query = $db->prepare("UPDATE siswa SET password = ? WHERE id_siswa = ?");
                            $query->execute([$password_hash, $siswa_id]);
                            $pesan = 'Password berhasil diubah!';
                        }

                        // Tampilkan pesan
                        echo '<div class="alert alert-' . ($error ? 'danger' : 'success') . ' alert-dismissible fade show mb-4" role="alert">
                                ' . $pesan . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                              </div>';
                    }
                    ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label">Password Lama</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password_lama" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Password lama wajib diisi</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="password_baru"
                                        required minlength="6">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Password baru minimal 6 karakter</div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" name="konfirmasi_password"
                                        required minlength="6">
                                    <button class="btn btn-outline-secondary toggle-password" type="button">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Konfirmasi password baru minimal 6 karakter</div>
                            </div>

                            <div class="col-12">
                                <button type="submit" name="ubah_password" class="btn btn-primary rounded-pill">
                                    <i class="bi bi-key me-1"></i>Ubah Password
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="alert alert-light mb-0">
                        <div class="d-flex gap-3 align-items-center">
                            <i class="bi bi-info-circle fs-4 text-primary"></i>
                            <div>
                                <h6 class="mb-1">Perlu mengubah data lainnya?</h6>
                                <p class="mb-0 text-muted">Silakan hubungi admin perpustakaan untuk mengubah data profil selain password.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= endLayout(); ?>