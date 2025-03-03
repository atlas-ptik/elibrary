<?php
// Path: admin/rak/edit.php
?>
<div class="modal fade" id="modalEdit<?= $item['id_rak'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Edit Rak Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="functions/update.php" method="POST">
                <input type="hidden" name="id_rak" value="<?= $item['id_rak'] ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small">Nomor Rak <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nomor_rak" value="<?= htmlspecialchars($item['nomor_rak']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="lokasi" value="<?= htmlspecialchars($item['lokasi']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Kapasitas <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="kapasitas" value="<?= $item['kapasitas'] ?>" min="<?= $item['total_buku'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"><?= htmlspecialchars($item['keterangan']) ?></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>