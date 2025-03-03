<?php
// Path: admin/buku/tambah.php
?>
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Tambah Buku</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="functions/create.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small">Judul Buku <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="judul" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">Penulis</label>
                            <input type="text" class="form-control" name="penulis">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">Penerbit</label>
                            <input type="text" class="form-control" name="penerbit">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">Tahun Terbit</label>
                            <input type="number" class="form-control" name="tahun_terbit" min="1900" max="<?= date('Y') ?>">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">ISBN</label>
                            <input type="text" class="form-control" name="isbn">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">Jumlah Halaman</label>
                            <input type="number" class="form-control" name="jumlah_halaman" min="1">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_kategori" required>
                                <option value="">Pilih Kategori</option>
                                <?php foreach ($kategori as $kat): ?>
                                    <option value="<?= $kat['id_kategori'] ?>">
                                        <?= htmlspecialchars($kat['nama_kategori']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">Rak Buku <span class="text-danger">*</span></label>
                            <select class="form-select" name="id_rak" required>
                                <option value="">Pilih Rak</option>
                                <?php foreach ($rak as $r): ?>
                                    <option value="<?= $r['id_rak'] ?>">
                                        <?= htmlspecialchars($r['nomor_rak']) ?> - <?= htmlspecialchars($r['lokasi']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="stok" min="0" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">Kelas Fokus <span class="text-danger">*</span></label>
                            <select class="form-select" name="kelas_fokus" required>
                                <option value="X">Kelas X</option>
                                <option value="XI">Kelas XI</option>
                                <option value="XII">Kelas XII</option>
                                <option value="UMUM">Umum</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small">Jurusan Fokus <span class="text-danger">*</span></label>
                            <select class="form-select" name="jurusan_fokus" required>
                                <option value="IPA">IPA</option>
                                <option value="IPS">IPS</option>
                                <option value="BAHASA">Bahasa</option>
                                <option value="UMUM">Umum</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Gambar</label>
                            <input type="file" class="form-control" name="gambar" accept="image/*">
                            <div class="form-text">Kosongkan jika ingin menggunakan gambar default</div>
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