<?php
// Path: admin/buku/hapus.php
?>
<div class="modal fade" id="modalHapus<?= $item['id_buku'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-exclamation-circle text-warning display-4"></i>
                </div>
                <p class="mb-0 text-center">Apakah Anda yakin ingin menghapus buku <strong><?= htmlspecialchars($item['judul']) ?></strong>? Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <form action="functions/delete.php" method="POST" class="d-inline">
                    <input type="hidden" name="id_buku" value="<?= $item['id_buku'] ?>">
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>