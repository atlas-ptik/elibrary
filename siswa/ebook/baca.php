<?php
// Path: siswa/ebook/baca.php

require_once "../layouts/siswa-layout.php";
require_once "../../globals/config/database.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$siswa_id = $_SESSION['siswa']['id'];
$ebook_id = $_GET['id'];

$query = $db->prepare("SELECT * FROM ebook WHERE id_ebook = ?");
$query->execute([$ebook_id]);
$ebook = $query->fetch();

if (!$ebook) {
    header("Location: index.php");
    exit;
}

try {
    $db->beginTransaction();

    $query = $db->prepare("
        INSERT INTO riwayat_baca_ebook (id_riwayat, id_siswa, id_ebook)
        VALUES (UUID(), ?, ?)
    ");
    $query->execute([$siswa_id, $ebook_id]);

    $query = $db->prepare("
        INSERT INTO log_aktivitas (id_log, tipe_pengguna, id_pengguna, aktivitas, detail)
        VALUES (UUID(), 'siswa', ?, 'baca_ebook', ?)
    ");
    $query->execute([$siswa_id, "Membaca e-book: " . $ebook['judul']]);

    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
}

echo startLayout("Baca E-Book - " . $ebook['judul']);
?>

<div class="col-12">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <div class="d-flex gap-2 mb-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary">
                            Kelas <?= $ebook['kelas_fokus'] ?>
                            <?= $ebook['jurusan_fokus'] !== 'UMUM' ? $ebook['jurusan_fokus'] : '' ?>
                        </span>
                    </div>
                    <h5 class="mb-0"><?= $ebook['judul'] ?></h5>
                </div>
                <div class="d-flex gap-2">
                    <a href="index.php" class="btn btn-outline-primary rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                    <button onclick="openPDFInNewTab()" class="btn btn-primary rounded-pill">
                        <i class="bi bi-box-arrow-up-right me-1"></i>Buka di Tab Baru
                    </button>
                </div>
            </div>

            <hr>

            <div class="text-center">
                <embed id="pdf-viewer" src="../../<?= $ebook['file_path'] ?>"
                    type="application/pdf"
                    width="100%"
                    height="800px"
                    class="border rounded">
            </div>
        </div>
    </div>
</div>

<script>
    function openPDFInNewTab() {
        const pdfPath = document.getElementById('pdf-viewer').src;
        window.open(pdfPath, '_blank');
    }
</script>

<?= endLayout(); ?>