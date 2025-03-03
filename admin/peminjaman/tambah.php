<?php
// Path: admin/peminjaman/tambah.php
?>
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Tambah Peminjaman</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="functions/create.php" method="POST">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small">Siswa <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_siswa" required>
                                <option value="">Pilih Siswa</option>
                                <?php foreach ($siswa as $s): ?>
                                    <option value="<?= $s['id_siswa'] ?>">
                                        <?= htmlspecialchars($s['nis']) ?> - 
                                        <?= htmlspecialchars($s['nama_lengkap']) ?> 
                                        (Kelas <?= $s['kelas'] ?> <?= $s['jurusan'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Buku <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_buku" required>
                                <option value="">Pilih Buku</option>
                                <?php foreach ($buku as $b): ?>
                                    <option value="<?= $b['id_buku'] ?>">
                                        <?= htmlspecialchars($b['judul']) ?> 
                                        (Stok: <?= $b['stok'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Tanggal Pinjam <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_pinjam" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="tanggal_jatuh_tempo" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Keterangan</label>
                            <textarea class="form-control" name="keterangan" rows="3" placeholder="Tambahkan catatan jika diperlukan"></textarea>
                        </div>
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