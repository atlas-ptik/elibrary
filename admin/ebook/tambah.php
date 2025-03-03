<?php
// Path: admin/ebook/tambah.php
?>
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Tambah E-Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="functions/create.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="bg-light rounded-4 p-3 mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                <i class="bi bi-journal-plus fs-4 text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">E-Book Baru</h6>
                                <p class="text-muted small mb-0">Tambahkan e-book baru ke perpustakaan</p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-12 col-md-8">
                            <div class="mb-3">
                                <label class="form-label small">Judul E-Book <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="judul" required>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label small">Penulis <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="penulis" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label small">Penerbit <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="penerbit" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label small">Tahun Terbit <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="tahun_terbit" min="1900" max="<?= date('Y') ?>" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label small">ISBN</label>
                                        <input type="text" class="form-control" name="isbn">
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label small">Jumlah Halaman</label>
                                        <input type="number" class="form-control" name="jumlah_halaman" min="1">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="mb-3">
                                        <label class="form-label small">Kelas <span class="text-danger">*</span></label>
                                        <select class="form-select" name="kelas_fokus" required>
                                            <option value="">Pilih Kelas</option>
                                            <option value="X">Kelas X</option>
                                            <option value="XI">Kelas XI</option>
                                            <option value="XII">Kelas XII</option>
                                            <option value="UMUM">Umum</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label small">Jurusan <span class="text-danger">*</span></label>
                                        <select class="form-select" name="jurusan_fokus" required>
                                            <option value="">Pilih Jurusan</option>
                                            <option value="IPA">IPA</option>
                                            <option value="IPS">IPS</option>
                                            <option value="BAHASA">Bahasa</option>
                                            <option value="UMUM">Umum</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4">
                            <div class="mb-3">
                                <label class="form-label small">File E-Book (PDF) <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" name="file_ebook" accept=".pdf" required>
                                <div class="form-text">Format: PDF. Maksimal 10MB</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Gambar Cover</label>
                                <input type="file" class="form-control" name="gambar" accept="image/*">
                                <div class="form-text">Format: JPG, PNG. Maksimal 2MB</div>
                            </div>
                            <div class="bg-light rounded p-3 mt-4">
                                <div class="d-flex align-items-center gap-2 text-primary mb-2">
                                    <i class="bi bi-info-circle"></i>
                                    <small class="fw-medium">Informasi</small>
                                </div>
                                <ul class="small text-muted mb-0" style="padding-left: 1rem;">
                                    <li>File e-book harus dalam format PDF</li>
                                    <li>Ukuran file maksimal 10MB</li>
                                    <li>Gambar cover opsional</li>
                                    <li>Format gambar: JPG, PNG</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-plus-circle me-2"></i>Tambah E-Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>