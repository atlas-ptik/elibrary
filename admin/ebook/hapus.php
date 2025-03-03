<?php
// Path: admin/ebook/hapus.php
?>
<div class="modal fade" id="modalHapus<?= $ebook['id_ebook'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <div class="bg-danger bg-opacity-10 mx-auto" style="width: 80px; height: 80px; border-radius: 40px;">
                        <i class="bi bi-trash text-danger" style="font-size: 40px; line-height: 80px;"></i>
                    </div>
                </div>
                <h5 class="mb-3">Hapus E-Book?</h5>
                <p class="text-muted mb-0">
                    Apakah Anda yakin ingin menghapus e-book <strong><?= htmlspecialchars($ebook['judul']) ?></strong>?
                </p>
                <p class="text-muted">Data yang dihapus tidak dapat dikembalikan.</p>
                <?php if ($ebook['total_dibaca'] > 0): ?>
                    <div class="alert alert-warning d-inline-block mt-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-exclamation-triangle"></i>
                            <small>E-book ini telah dibaca sebanyak <?= number_format($ebook['total_dibaca']) ?> kali</small>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <form action="functions/delete.php" method="POST" class="d-inline">
                    <input type="hidden" name="id_ebook" value="<?= $ebook['id_ebook'] ?>">
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <i class="bi bi-trash me-2"></i>Hapus E-Book
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>