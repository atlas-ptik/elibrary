<?php
// Path: admin/rak/tambah.php
?>
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Tambah Rak Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="functions/create.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small">Nomor Rak <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nomor_rak" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="lokasi" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Kapasitas <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="kapasitas" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Keterangan</label>
                        <textarea class="form-control" name="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>