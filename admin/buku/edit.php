<?php
// Path: admin/buku/edit.php
?>
<div class="modal fade" id="modalEdit<?= $item['id_buku'] ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Edit Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="functions/update.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_buku" value="<?= $item['id_buku'] ?>">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small">Judul Buku <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="judul" value="<?= htmlspecialchars($item['judul']) ?>" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">Penulis</label>
                            <input type="text" class="form-control" name="penulis" value="<?= htmlspecialchars($item['penulis']) ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">Penerbit</label>
                            <input type="text" class="form-control" name="penerbit" value="<?= htmlspecialchars($item['penerbit']) ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">Tahun Terbit</label>
                            <input type="number" class="form-control" name="tahun_terbit" min="1900" max="<?= date('Y') ?>" value="<?= $item['tahun_terbit'] ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">ISBN</label>
                            <input type="text" class="form-control" name="isbn" value="<?= htmlspecialchars($item['isbn']) ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">Jumlah Halaman</label>
                            <input type="number" class="form-control" name="jumlah_halaman" min="1" value="<?= $item['jumlah_halaman'] ?>">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_kategori" required>
                                <?php foreach ($kategori as $kat): ?>
                                    <option value="<?= $kat['id_kategori'] ?>" <?= $item['id_kategori'] === $kat['id_kategori'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($kat['nama_kategori']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">Rak Buku <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_rak" required>
                                <?php foreach ($rak as $r): ?>
                                    <option value="<?= $r['id_rak'] ?>" <?= $item['id_rak'] === $r['id_rak'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['nomor_rak']) ?> - <?= htmlspecialchars($r['lokasi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="stok" min="0" value="<?= $item['stok'] ?>" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">Kelas Fokus <span class="text-danger">*</span></label>
                            <select class="form-select" name="kelas_fokus" required>
                                <option value="X" <?= $item['kelas_fokus'] === 'X' ? 'selected' : '' ?>>Kelas X</option>
                                <option value="XI" <?= $item['kelas_fokus'] === 'XI' ? 'selected' : '' ?>>Kelas XI</option>
                                <option value="XII" <?= $item['kelas_fokus'] === 'XII' ? 'selected' : '' ?>>Kelas XII</option>
                                <option value="UMUM" <?= $item['kelas_fokus'] === 'UMUM' ? 'selected' : '' ?>>Umum</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">Jurusan Fokus <span class="text-danger">*</span></label>
                            <select class="form-select" name="jurusan_fokus" required>
                                <option value="IPA" <?= $item['jurusan_fokus'] === 'IPA' ? 'selected' : '' ?>>IPA</option>
                                <option value="IPS" <?= $item['jurusan_fokus'] === 'IPS' ? 'selected' : '' ?>>IPS</option>
                                <option value="BAHASA" <?= $item['jurusan_fokus'] === 'BAHASA' ? 'selected' : '' ?>>Bahasa</option>
                                <option value="UMUM" <?= $item['jurusan_fokus'] === 'UMUM' ? 'selected' : '' ?>>Umum</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Gambar</label>
                            <input type="file" class="form-control" name="gambar" accept="image/*">
                            <div class="form-text">Kosongkan jika tidak ingin mengubah gambar</div>
                        </div>
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