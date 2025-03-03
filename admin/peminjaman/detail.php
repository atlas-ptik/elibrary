<?php
// Path: admin/peminjaman/detail.php
?>
<div class="modal fade" id="modalDetail<?= $item['id_peminjaman'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Detail Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="d-flex gap-3">
                            <?php if ($item['status'] === 'dipinjam'): ?>
                                <span class="badge bg-primary">Sedang Dipinjam</span>
                            <?php elseif ($item['status'] === 'terlambat'): ?>
                                <span class="badge bg-danger">Terlambat</span>
                            <?php else: ?>
                                <span class="badge bg-success">Dikembalikan</span>
                            <?php endif; ?>

                            <?php if ($item['status'] === 'dipinjam' && $item['sisa_hari'] < 0): ?>
                                <span class="badge bg-danger">
                                    Terlambat <?= abs($item['sisa_hari']) ?> hari
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <h6 class="mb-2">Data Siswa</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td style="width: 120px">Nama</td>
                                    <td>: <?= htmlspecialchars($item['nama_siswa']) ?></td>
                                </tr>
                                <tr>
                                    <td>NIS</td>
                                    <td>: <?= htmlspecialchars($item['nis']) ?></td>
                                </tr>
                                <tr>
                                    <td>Kelas</td>
                                    <td>: <?= $item['kelas'] ?> <?= $item['jurusan'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-12">
                        <h6 class="mb-2">Data Buku</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td style="width: 120px">Judul</td>
                                    <td>: <?= htmlspecialchars($item['judul_buku']) ?></td>
                                </tr>
                                <tr>
                                    <td>Penulis</td>
                                    <td>: <?= htmlspecialchars($item['penulis']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="col-12">
                        <h6 class="mb-2">Data Peminjaman</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td style="width: 120px">Tanggal Pinjam</td>
                                    <td>: <?= date('d/m/Y', strtotime($item['tanggal_pinjam'])) ?></td>
                                </tr>
                                <tr>
                                    <td>Jatuh Tempo</td>
                                    <td>: <?= date('d/m/Y', strtotime($item['tanggal_jatuh_tempo'])) ?></td>
                                </tr>
                                <?php if ($item['tanggal_kembali']): ?>
                                    <tr>
                                        <td>Tanggal Kembali</td>
                                        <td>: <?= date('d/m/Y', strtotime($item['tanggal_kembali'])) ?></td>
                                    </tr>
                                    <?php if ($item['keterlambatan'] > 0): ?>
                                        <tr>
                                            <td>Keterlambatan</td>
                                            <td>: <?= $item['keterlambatan'] ?> hari</td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($item['keterangan']): ?>
                                    <tr>
                                        <td>Keterangan</td>
                                        <td>: <?= nl2br(htmlspecialchars($item['keterangan'])) ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>