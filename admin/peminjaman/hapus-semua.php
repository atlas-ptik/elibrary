<?php
// Path: admin/peminjaman/hapus-semua.php
?>
<div class="modal fade" id="modalHapusSemua" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Konfirmasi Hapus Semua</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="functions/delete-all.php" method="POST">
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-trash text-danger display-4"></i>
                    </div>

                    <p class="mb-3">
                        Apakah Anda yakin ingin menghapus <strong>semua riwayat peminjaman</strong>?
                    </p>

                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Peringatan!</strong> Semua data peminjaman akan dihapus secara permanen dan tidak dapat dikembalikan.
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="konfirmasiHapus" name="konfirmasi" value="1" required>
                        <label class="form-check-label" for="konfirmasiHapus">
                            Saya mengerti dan bertanggung jawab atas tindakan ini
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Hapus Semua Data</button>
                </div>
            </form>
        </div>
    </div>
</div>