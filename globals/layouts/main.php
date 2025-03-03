<?php
// Path: globals/layouts/main.php

session_start();

function startLayout($title = "E-Library")
{
    ob_start();
?>
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?></title>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/custom-theme.css">
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    </head>

    <body class="min-vh-100 d-flex flex-column">
        <nav class="navbar navbar-expand-lg navbar-light border-bottom sticky-top bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
                    <i class="bi bi-book-half fs-4 text-primary"></i>
                    <span>E-Library</span>
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link px-3" href="index.php">
                                <i class="bi bi-house-door me-1"></i>Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="cari.php">
                                <i class="bi bi-search me-1"></i>Cari Buku
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="ebook.php">
                                <i class="bi bi-journal-text me-1"></i>E-Book
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="tentang.php">
                                <i class="bi bi-info-circle me-1"></i>Tentang
                            </a>
                        </li>
                        <?php if (isset($_SESSION['siswa'])): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle px-3 d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                    <img src="<?= $_SESSION['siswa']['foto'] ?? 'assets/images/default.jpg' ?>"
                                        class="rounded-circle"
                                        width="24"
                                        height="24"
                                        alt="<?= $_SESSION['siswa']['nama'] ?>"
                                        style="object-fit: cover;">
                                    <span><?= $_SESSION['siswa']['nama'] ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="siswa/dashboard/index.php">
                                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="siswa/profil/index.php">
                                            <i class="bi bi-person me-2"></i>Profil Saya
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="siswa/peminjaman/index.php">
                                            <i class="bi bi-journal-check me-2"></i>Peminjaman
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="siswa/riwayat/index.php">
                                            <i class="bi bi-clock-history me-2"></i>Riwayat Baca
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="siswa/auth/logout.php">
                                            <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        <?php elseif (isset($_SESSION['admin'])): ?>
                            <li class="nav-item">
                                <a class="nav-link px-3" href="admin/dashboard/index.php">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard Admin
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link px-3" href="siswa/auth/login.php">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="container py-4 flex-grow-1">
        <?php
    }

    function endLayout()
    {
        ?>
        </main>

        <footer class="py-4 mt-auto bg-light border-top">
            <div class="container">
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="bi bi-book-half fs-4 text-primary"></i>
                            <h5 class="mb-0">E-Library</h5>
                        </div>
                        <p class="text-muted mb-0">Dikembangkan oleh Tim Atlas</p>
                        <p class="text-muted mb-0">Sistem Perpustakaan Digital</p>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="row g-4">
                            <div class="col-6">
                                <h6 class="fw-bold mb-3">Tautan</h6>
                                <ul class="list-unstyled mb-0">
                                    <li><a href="index.php" class="text-decoration-none text-muted">Beranda</a></li>
                                    <li><a href="cari.php" class="text-decoration-none text-muted">Cari Buku</a></li>
                                    <li><a href="ebook.php" class="text-decoration-none text-muted">E-Book</a></li>
                                    <li><a href="tentang.php" class="text-decoration-none text-muted">Tentang</a></li>
                                </ul>
                            </div>
                            <div class="col-6">
                                <h6 class="fw-bold mb-3">Kontak</h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="text-muted d-flex align-items-start">
                                        <i class="bi bi-envelope me-2 flex-shrink-0"></i>
                                        <span class="text-break">team.atlas.dev@gmail.com</span>
                                    </li>
                                    <li class="text-muted d-flex align-items-center">
                                        <i class="bi bi-globe me-2 flex-shrink-0"></i>
                                        <span>atlas-dev.com</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <hr>
                        <p class="text-muted text-center mb-0">&copy; <?= date('Y') ?> Atlas Development Team. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>

        <script src="assets/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
<?php
        return ob_get_clean();
    }
?>