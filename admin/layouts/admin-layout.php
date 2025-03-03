<?php
// Path: admin/layouts/admin-layout.php

function adminLayout($title = "Admin Dashboard - Elibrary", $active = "dashboard")
{
    if (!isset($_SESSION['admin_id'])) {
        header("Location: ../auth/login.php");
        exit;
    }

    ob_start();
?>
    <!DOCTYPE html>
    <html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $title ?></title>
        <link rel="stylesheet" href="../../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="../../assets/css/custom-theme.css">
        <link rel="icon" type="image/x-icon" href="../../assets/favicon.ico">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            #sidebar {
                width: 280px;
                transition: all 0.3s;
            }

            #content {
                margin-left: 280px;
                transition: all 0.3s;
            }

            @media (max-width: 768px) {
                #sidebar {
                    margin-left: -280px;
                }

                #sidebar.active {
                    margin-left: 0;
                }

                #content {
                    margin-left: 0;
                }

                #content.active {
                    margin-left: 280px;
                }
            }

            .nav-link {
                border-radius: 8px;
                margin-bottom: 4px;
            }

            .nav-link:hover {
                background-color: var(--bs-primary-bg-subtle);
            }

            .nav-link.active {
                background-color: var(--bs-primary);
                color: white !important;
            }

            .nav-link.active i {
                color: white !important;
            }
        </style>
    </head>

    <body class="bg-light">
        <div class="d-flex">
            <!-- Sidebar -->
            <div id="sidebar" class="bg-white shadow-sm fixed-top h-100 overflow-auto p-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <i class="bi bi-book-half fs-4 text-primary"></i>
                    <h5 class="mb-0">Elibrary</h5>
                </div>

                <p class="text-muted small mb-2">MENU UTAMA</p>
                <nav class="nav flex-column mb-4">
                    <a href="../dashboard/index.php" class="nav-link text-dark <?= $active === 'dashboard' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2 me-2 <?= $active === 'dashboard' ? '' : 'text-primary' ?>"></i>
                        Dashboard
                    </a>
                    <a href="../admin/index.php" class="nav-link text-dark <?= $active === 'admin' ? 'active' : '' ?>">
                        <i class="bi bi-person-badge me-2 <?= $active === 'admin' ? '' : 'text-primary' ?>"></i>
                        Admin
                    </a>
                    <a href="../siswa/index.php" class="nav-link text-dark <?= $active === 'siswa' ? 'active' : '' ?>">
                        <i class="bi bi-people me-2 <?= $active === 'siswa' ? '' : 'text-primary' ?>"></i>
                        Siswa
                    </a>
                </nav>

                <p class="text-muted small mb-2">MANAJEMEN BUKU</p>
                <nav class="nav flex-column mb-4">
                    <a href="../buku/index.php" class="nav-link text-dark <?= $active === 'buku' ? 'active' : '' ?>">
                        <i class="bi bi-book me-2 <?= $active === 'buku' ? '' : 'text-primary' ?>"></i>
                        Buku
                    </a>
                    <a href="../kategori/index.php" class="nav-link text-dark <?= $active === 'kategori' ? 'active' : '' ?>">
                        <i class="bi bi-tags me-2 <?= $active === 'kategori' ? '' : 'text-primary' ?>"></i>
                        Kategori
                    </a>
                    <a href="../rak/index.php" class="nav-link text-dark <?= $active === 'rak' ? 'active' : '' ?>">
                        <i class="bi bi-archive me-2 <?= $active === 'rak' ? '' : 'text-primary' ?>"></i>
                        Rak Buku
                    </a>
                </nav>

                <p class="text-muted small mb-2">MANAJEMEN E-BOOK</p>
                <nav class="nav flex-column mb-4">
                    <a href="../ebook/index.php" class="nav-link text-dark <?= $active === 'ebook' ? 'active' : '' ?>">
                        <i class="bi bi-journal-text me-2 <?= $active === 'ebook' ? '' : 'text-primary' ?>"></i>
                        E-Book
                    </a>
                </nav>

                <p class="text-muted small mb-2">TRANSAKSI</p>
                <nav class="nav flex-column">
                    <a href="../peminjaman/index.php" class="nav-link text-dark <?= $active === 'peminjaman' ? 'active' : '' ?>">
                        <i class="bi bi-journal-check me-2 <?= $active === 'peminjaman' ? '' : 'text-primary' ?>"></i>
                        Peminjaman
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div id="content">
                <!-- Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4 fixed-top">
                    <div class="container-fluid px-4">
                        <button id="sidebarToggle" class="btn btn-link text-dark d-lg-none">
                            <i class="bi bi-list fs-5"></i>
                        </button>

                        <ul class="navbar-nav ms-auto align-items-center">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                                    <img src="../../assets/images/default.jpg" class="rounded-circle" width="32" height="32">
                                    <span><?= $_SESSION['admin_nama'] ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="../auth/logout.php">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Page Content -->
                <div class="container-fluid px-4 pt-5 mt-4">
                <?php
                return ob_get_clean();
            }

            function endLayout()
            {
                ob_start();
                ?>
                </div>
            </div>
        </div>

        <script src="../../assets/js/bootstrap.bundle.min.js"></script>
        <script>
            document.getElementById('sidebarToggle').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('active');
                document.getElementById('content').classList.toggle('active');
            });
        </script>
    </body>

    </html>
<?php
                return ob_get_clean();
            }
?>