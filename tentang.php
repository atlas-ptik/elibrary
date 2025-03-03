<?php
// Path: tentang.php

require_once "globals/layouts/main.php";
require_once "globals/config/database.php";

startLayout("Tentang Atlas");
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-body p-0">
                <div class="bg-primary bg-opacity-10 p-5 text-center">
                    <h2 class="display-5 fw-bold mb-3">ATLAS</h2>
                    <p class="lead">Tim Pengembang Aplikasi & Sistem Digital</p>
                </div>

                <div class="p-5">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <h4 class="fw-bold mb-4 text-primary">Tentang Kami</h4>
                            <p>Atlas adalah tim pengembang aplikasi yang berfokus pada pembuatan solusi digital inovatif. Kami menghadirkan solusi teknologi modern dengan pendekatan yang kreatif dan efisien.</p>
                            <p>Didirikan oleh sekelompok mahasiswa dengan keahlian di berbagai bidang teknologi informasi, Atlas berkomitmen untuk mengembangkan aplikasi berkualitas tinggi yang memenuhi kebutuhan pengguna.</p>
                        </div>
                    </div>

                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary text-white p-2 rounded-3 me-3">
                                            <i class="bi bi-code-square fs-4"></i>
                                        </div>
                                        <h5 class="mb-0">Pengembangan Web</h5>
                                    </div>
                                    <p class="mb-0">Kami mengembangkan aplikasi web modern menggunakan teknologi terkini seperti PHP, MySQL, dan Bootstrap.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary text-white p-2 rounded-3 me-3">
                                            <i class="bi bi-phone fs-4"></i>
                                        </div>
                                        <h5 class="mb-0">Aplikasi Mobile</h5>
                                    </div>
                                    <p class="mb-0">Mengembangkan aplikasi mobile responsif dan user-friendly untuk berbagai kebutuhan.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary text-white p-2 rounded-3 me-3">
                                            <i class="bi bi-palette fs-4"></i>
                                        </div>
                                        <h5 class="mb-0">UI/UX Design</h5>
                                    </div>
                                    <p class="mb-0">Merancang interface yang intuitif dan menarik dengan fokus pada pengalaman pengguna yang optimal.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card h-100 border-0 bg-light">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary text-white p-2 rounded-3 me-3">
                                            <i class="bi bi-database fs-4"></i>
                                        </div>
                                        <h5 class="mb-0">Database Management</h5>
                                    </div>
                                    <p class="mb-0">Mengoptimalkan penyimpanan dan pengelolaan data dengan database yang terstruktur dan efisien.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4 mb-5">
            <a href="index.php" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-house-door me-1"></i>Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<?= endLayout(); ?>