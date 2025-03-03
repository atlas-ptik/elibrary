<?php
// Path: siswa/layouts/siswa-layout.php

session_start();
if (!isset($_SESSION['siswa'])) {
    header("Location: ../auth/login.php");
    exit;
}

function startLayout($title = "Dashboard Siswa")
{
    ob_start();
?>
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?> - E-Library</title>
        <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../../assets/css/custom-theme.css">
        <link rel="icon" type="image/x-icon" href="../../assets/favicon.ico">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    </head>

    <body class="min-vh-100 d-flex flex-column">
        <nav class="navbar navbar-expand-lg navbar-light border-bottom sticky-top bg-white shadow-sm">
            <div class="container-fluid px-4">
                <a class="navbar-brand d-flex align-items-center gap-2" href="../../index.php">
                    <i class="bi bi-book-half fs-4"></i>
                    <span>Elibrary</span>
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link px-3" href="../dashboard/index.php">
                                <i class="bi bi-speedometer2 me-1"></i>Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="../peminjaman/index.php">
                                <i class="bi bi-journal-check me-1"></i>Peminjaman
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="../riwayat/index.php">
                                <i class="bi bi-clock-history me-1"></i>Riwayat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-3" href="../ebook/index.php">
                                <i class="bi bi-journal-text me-1"></i>E-Book
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="<?= isset($_SESSION['siswa']['foto']) ? '../../' . $_SESSION['siswa']['foto'] : '../../assets/images/default.jpg' ?>"
                                    class="rounded-circle" width="32" height="32" alt="Foto Profil">
                                <span><?= $_SESSION['siswa']['nama'] ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="../profil/index.php">
                                        <i class="bi bi-person-circle me-2"></i>Profil Saya
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="../auth/logout.php">
                                        <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="container-fluid flex-grow-1 py-4 px-4">
            <div class="row g-4">
                <div class="col-12 mb-2">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="../dashboard/index.php" class="text-decoration-none">Dashboard</a>
                            </li>
                            <?php if ($title !== "Dashboard Siswa"): ?>
                                <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
                            <?php endif; ?>
                        </ol>
                    </nav>
                </div>
            <?php
            return ob_get_clean();
        }

        function endLayout()
        {
            ob_start();
            ?>
            </div>
        </main>

        <footer class="py-4 bg-light border-top">
            <div class="container-fluid px-4">
                <div class="row">
                    <div class="col-12">
                        <p class="text-muted text-center mb-0">&copy; <?= date('Y') ?> SMAN 1 Tompaso. All rights reserved.</p>
                    </div>
                </div>
            </div>
        </footer>

        <script src="../../assets/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
<?php
            return ob_get_clean();
        }
?>