<?php
// Path: admin/ebook/detail.php
?>
<div class="modal fade" id="modalDetail<?= $ebook['id_ebook'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Detail E-Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <!-- Informasi E-Book -->
                    <div class="col-12 col-md-4">
                        <img src="../../<?= htmlspecialchars($ebook['gambar']) ?>"
                            alt="<?= htmlspecialchars($ebook['judul']) ?>"
                            class="img-fluid rounded shadow-sm w-100 mb-3"
                            style="object-fit: cover; aspect-ratio: 3/4;">

                        <div class="d-grid gap-2">
                            <a href="../../<?= $ebook['file_path'] ?>" target="_blank" class="btn btn-primary rounded-pill">
                                <i class="bi bi-file-pdf me-2"></i>Buka PDF
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-md-8">
                        <h5 class="border-bottom pb-2 mb-3"><?= htmlspecialchars($ebook['judul']) ?></h5>

                        <div class="row mb-4">
                            <div class="col-12 col-sm-6">
                                <p class="small text-muted mb-1">Penulis</p>
                                <p class="mb-3"><?= htmlspecialchars($ebook['penulis']) ?></p>
                            </div>
                            <div class="col-12 col-sm-6">
                                <p class="small text-muted mb-1">Penerbit</p>
                                <p class="mb-3"><?= htmlspecialchars($ebook['penerbit']) ?></p>
                            </div>
                            <div class="col-12 col-sm-6">
                                <p class="small text-muted mb-1">Tahun Terbit</p>
                                <p class="mb-3"><?= $ebook['tahun_terbit'] ?></p>
                            </div>
                            <div class="col-12 col-sm-6">
                                <p class="small text-muted mb-1">ISBN</p>
                                <p class="mb-3"><?= htmlspecialchars($ebook['isbn']) ?: '-' ?></p>
                            </div>
                            <div class="col-12 col-sm-6">
                                <p class="small text-muted mb-1">Jumlah Halaman</p>
                                <p class="mb-3"><?= $ebook['jumlah_halaman'] ? number_format($ebook['jumlah_halaman']) . ' halaman' : '-' ?></p>
                            </div>
                            <div class="col-12 col-sm-6">
                                <p class="small text-muted mb-1">Kelas</p>
                                <p class="mb-3">Kelas <?= $ebook['kelas_fokus'] ?></p>
                            </div>
                            <div class="col-12 col-sm-6">
                                <p class="small text-muted mb-1">Jurusan</p>
                                <p class="mb-3"><?= $ebook['jurusan_fokus'] ?></p>
                            </div>
                            <div class="col-12">
                                <p class="small text-muted mb-1">Total Dibaca</p>
                                <p class="mb-0">
                                    <span class="badge bg-primary rounded-pill">
                                        <?= number_format($ebook['total_dibaca']) ?> kali
                                    </span>
                                </p>
                            </div>
                        </div>

                        <?php
                        // Ambil 5 riwayat baca terakhir
                        $riwayat_stmt = $db->prepare("
                            SELECT 
                                rbe.tanggal_baca,
                                s.nama_lengkap,
                                s.nis,
                                s.kelas,
                                s.jurusan
                            FROM riwayat_baca_ebook rbe
                            JOIN siswa s ON rbe.id_siswa = s.id_siswa
                            WHERE rbe.id_ebook = ?
                            ORDER BY rbe.tanggal_baca DESC
                            LIMIT 5
                        ");
                        $riwayat_stmt->execute([$ebook['id_ebook']]);
                        $riwayat_baca = $riwayat_stmt->fetchAll();

                        if (!empty($riwayat_baca)):
                        ?>
                            <h6 class="border-bottom pb-2 mb-3">Riwayat Baca Terakhir</h6>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Siswa</th>
                                            <th>Kelas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($riwayat_baca as $riwayat): ?>
                                            <tr>
                                                <td class="small">
                                                    <?= date('d/m/Y H:i', strtotime($riwayat['tanggal_baca'])) ?>
                                                </td>
                                                <td>
                                                    <p class="mb-0"><?= htmlspecialchars($riwayat['nama_lengkap']) ?></p>
                                                    <small class="text-muted"><?= htmlspecialchars($riwayat['nis']) ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary rounded-pill">
                                                        <?= $riwayat['kelas'] ?> <?= $riwayat['jurusan'] ?>
                                                    </span>
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
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>