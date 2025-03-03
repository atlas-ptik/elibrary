<?php
// Path: admin/peminjaman/hapus.php
?>
<div class="modal fade" id="modalHapus<?= $item['id_peminjaman'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="functions/delete.php" method="POST">
                <input type="hidden" name="id_peminjaman" value="<?= $item['id_peminjaman'] ?>">
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="bi bi-trash text-danger display-4"></i>
                    </div>

                    <p class="mb-3">
                        Apakah Anda yakin ingin menghapus riwayat peminjaman buku <strong><?= htmlspecialchars($item['judul_buku']) ?></strong>
                        oleh siswa <strong><?= htmlspecialchars($item['nama_siswa']) ?></strong>?
                    </p>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Data yang sudah dihapus tidak dapat dikembalikan.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Hapus Data</button>
                </div>
            </form>
        </div>
    </div>
</div>