<?php
// Path: admin/kategori/edit.php
?>
<div class="modal fade" id="modalEdit<?= $item['id_kategori'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Edit Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="functions/update.php" method="POST">
                <input type="hidden" name="id_kategori" value="<?= $item['id_kategori'] ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kategori" value="<?= htmlspecialchars($item['nama_kategori']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3"><?= htmlspecialchars($item['deskripsi']) ?></textarea>
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