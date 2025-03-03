<?php
// Path: admin/peminjaman/kembali.php
?>
<div class="modal fade" id="modalKembali<?= $item['id_peminjaman'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Konfirmasi Pengembalian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="functions/return.php" method="POST">
                <input type="hidden" name="id_peminjaman" value="<?= $item['id_peminjaman'] ?>">
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-journal-check text-success display-4"></i>
                    </div>

                    <p class="mb-3">
                        Konfirmasi pengembalian buku <strong><?= htmlspecialchars($item['judul_buku']) ?></strong> 
                        oleh siswa <strong><?= htmlspecialchars($item['nama_siswa']) ?></strong>.
                    </p>

                    <?php if ($item['sisa_hari'] < 0): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle me-2"></i>
                            Terlambat <?= abs($item['sisa_hari']) ?> hari dari tanggal jatuh tempo.
                        </div>
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small">Tanggal Pengembalian <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_kembali" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="3" placeholder="Tambahkan catatan jika diperlukan"><?= htmlspecialchars($item['keterangan'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Konfirmasi Pengembalian</button>
                </div>
            </form>
        </div>
    </div>
</div>