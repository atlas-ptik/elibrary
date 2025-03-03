<?php
// Path: baca-ebook.php

session_start();
require_once "globals/config/database.php";

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: siswa/auth/login.php");
    exit;
}

// Cek parameter id
if (!isset($_GET['id'])) {
    header("Location: ebook.php");
    exit;
}

$id_ebook = $_GET['id'];
$id_siswa = $_SESSION['user_id'];

// Ambil data ebook
$query = $db->prepare("
    SELECT e.*, ke.nama_kategori
    FROM ebook e
    JOIN kategori_ebook ke ON e.id_kategori_ebook = ke.id_kategori_ebook
    WHERE e.id_ebook = ?
");

$query->execute([$id_ebook]);
$ebook = $query->fetch();

if (!$ebook) {
    header("Location: ebook.php");
    exit;
}

// Catat riwayat baca
$query = $db->prepare("
    INSERT INTO riwayat_baca_ebook (id_riwayat, id_siswa, id_ebook)
    VALUES (UUID(), ?, ?)
");
$query->execute([$id_siswa, $id_ebook]);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca <?= $ebook['judul'] ?> - Elibrary</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/custom-theme.css">
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        #pdf-container {
            width: 100%;
            height: calc(100vh - 130px);
            overflow: hidden;
            background: #f8f9fa;
            position: relative;
        }

        #pdf-viewer {
            width: 100%;
            height: 100%;
            border: none;
        }

        .toolbar {
            background: white;
            padding: 0.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .page-info {
            min-width: 100px;
            text-align: center;
        }
    </style>
</head>

<body class="min-vh-100 d-flex flex-column">
    <!-- Navbar Sederhana -->
    <nav class="navbar navbar-expand navbar-light bg-white border-bottom shadow-sm">
        <div class="container-fluid px-4">
            <a href="ebook.php" class="btn btn-link text-decoration-none p-0">
                <i class="bi bi-arrow-left me-2"></i>Kembali
            </a>
            <div class="d-flex align-items-center gap-3">
                <h6 class="mb-0"><?= $ebook['judul'] ?></h6>
                <span class="badge bg-primary bg-opacity-10 text-primary">
                    <?= $ebook['nama_kategori'] ?>
                </span>
            </div>
            <div class="dropdown">
                <button class="btn btn-link text-decoration-none p-0" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="detail-ebook.php?id=<?= $ebook['id_ebook'] ?>">
                            <i class="bi bi-info-circle me-2"></i>Detail E-Book
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <button class="dropdown-item" onclick="toggleFullscreen()">
                            <i class="bi bi-fullscreen me-2"></i>Layar Penuh
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Toolbar -->
    <div class="toolbar d-flex justify-content-center gap-3">
        <button class="btn btn-light" onclick="zoomOut()">
            <i class="bi bi-zoom-out"></i>
        </button>
        <button class="btn btn-light" onclick="zoomIn()">
            <i class="bi bi-zoom-in"></i>
        </button>
        <div class="btn-group">
            <button class="btn btn-light" onclick="prevPage()">
                <i class="bi bi-chevron-left"></i>
            </button>
            <div class="btn btn-light page-info">
                Halaman <span id="page-num"></span> dari <span id="page-count"></span>
            </div>
            <button class="btn btn-light" onclick="nextPage()">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
    </div>

    <!-- PDF Container -->
    <div id="pdf-container">
        <canvas id="pdf-viewer"></canvas>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Inisialisasi PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null,
            pageNum = 1,
            pageRendering = false,
            pageNumPending = null,
            scale = 1.5,
            canvas = document.getElementById('pdf-viewer'),
            ctx = canvas.getContext('2d');

        async function renderPage(num) {
            pageRendering = true;
            const page = await pdfDoc.getPage(num);

            const viewport = page.getViewport({
                scale
            });
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderContext = {
                canvasContext: ctx,
                viewport: viewport
            };

            try {
                await page.render(renderContext).promise;
                pageRendering = false;

                if (pageNumPending !== null) {
                    renderPage(pageNumPending);
                    pageNumPending = null;
                }
            } catch (error) {
                console.error('Error rendering page:', error);
                pageRendering = false;
            }

            document.getElementById('page-num').textContent = num;
        }

        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        function prevPage() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        }

        function nextPage() {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        }

        function zoomIn() {
            scale *= 1.2;
            queueRenderPage(pageNum);
        }

        function zoomOut() {
            scale /= 1.2;
            queueRenderPage(pageNum);
        }

        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }

        // Load PDF
        async function loadPDF() {
            try {
                const loadingTask = pdfjsLib.getDocument('<?= $ebook['file_path'] ?>');
                pdfDoc = await loadingTask.promise;
                document.getElementById('page-count').textContent = pdfDoc.numPages;
                renderPage(pageNum);
            } catch (error) {
                console.error('Error loading PDF:', error);
                alert('Gagal memuat PDF. Silakan coba lagi nanti.');
            }
        }

        // Handle window resize
        window.addEventListener('resize', () => {
            if (pdfDoc) {
                queueRenderPage(pageNum);
            }
        });

        loadPDF();
    </script>
</body>

</html>