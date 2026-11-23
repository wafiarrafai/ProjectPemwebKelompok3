<?php
// DATA FASILITAS KESEHATAN
$facilities = [
    1 => [
        'type' => 'Puskesmas',
        'name' => 'Puskesmas Sejahtera',
        'address' => 'Jl. Kesehatan No. 12, Purwokerto',
        'contact' => 'Kontak belum tersedia',
        'description' => 'Puskesmas Sejahtera merupakan salah satu fasilitas kesehatan tingkat pertama. Pengguna dapat menghubungi fasilitas secara langsung untuk memperoleh informasi mengenai layanan yang tersedia.',
        'services' => [
            'Informasi layanan kesehatan',
            'Pelayanan kesehatan dasar',
            'Informasi pemeriksaan kesehatan'
        ]
    ],
    2 => [
        'type' => 'Klinik',
        'name' => 'Klinik Bersama',
        'address' => 'Jl. Melati No. 8, Purwokerto',
        'contact' => 'Kontak belum tersedia',
        'description' => 'Klinik Bersama menyediakan informasi fasilitas kesehatan bagi masyarakat. Silakan menghubungi pihak klinik untuk memastikan jenis layanan dan jadwal operasional.',
        'services' => [
            'Informasi pelayanan klinik',
            'Konsultasi layanan',
            'Informasi pemeriksaan'
        ]
    ],
    3 => [
        'type' => 'Rumah sakit',
        'name' => 'RSU Contoh Sehat',
        'address' => 'Area Purwokerto',
        'contact' => 'Kontak belum tersedia',
        'description' => 'RSU Contoh Sehat merupakan data fasilitas rumah sakit pada direktori SEHATIN. Informasi layanan lebih lanjut dapat dikonfirmasi langsung kepada pihak rumah sakit.',
        'services' => [
            'Informasi layanan rumah sakit',
            'Informasi rawat jalan',
            'Informasi fasilitas kesehatan'
        ]
    ],
    4 => [
        'type' => 'Puskesmas',
        'name' => 'Puskesmas Sehat Mandiri',
        'address' => 'Jl. Merdeka, Purwokerto',
        'contact' => 'Kontak belum tersedia',
        'description' => 'Puskesmas Sehat Mandiri tercantum dalam direktori fasilitas kesehatan SEHATIN. Pengguna dapat menghubungi fasilitas untuk mengetahui informasi pelayanan yang tersedia.',
        'services' => [
            'Informasi layanan kesehatan',
            'Pelayanan kesehatan dasar',
            'Informasi pemeriksaan'
        ]
    ],
    5 => [
        'type' => 'Klinik',
        'name' => 'Klinik Medika',
        'address' => 'Jl. Jenderal Sudirman, Purwokerto',
        'contact' => 'Kontak belum tersedia',
        'description' => 'Klinik Medika merupakan salah satu pilihan fasilitas kesehatan dalam direktori SEHATIN. Pastikan informasi layanan dan jam operasional melalui pihak klinik.',
        'services' => [
            'Informasi pelayanan klinik',
            'Informasi pemeriksaan',
            'Konsultasi layanan'
        ]
    ],
    6 => [
        'type' => 'Rumah sakit',
        'name' => 'Rumah Sakit Harapan',
        'address' => 'Purwokerto',
        'contact' => 'Kontak belum tersedia',
        'description' => 'Rumah Sakit Harapan ditampilkan sebagai bagian dari direktori fasilitas kesehatan SEHATIN. Hubungi pihak rumah sakit untuk memperoleh informasi lebih lanjut.',
        'services' => [
            'Informasi layanan rumah sakit',
            'Informasi rawat jalan',
            'Informasi fasilitas kesehatan'
        ]
    ]
];

// MENGAMBIL ID FASILITAS DARI URL
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$facility = ($id !== false && $id !== null)
    ? ($facilities[$id] ?? null)
    : null;

// FUNGSI UNTUK MENGAMANKAN OUTPUT
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= $facility ? e($facility['name']) : 'Fasilitas Tidak Ditemukan' ?> - SEHATIN
    </title>

    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/facility-detail.css">
    <script src="assets/js/script.js" defer></script>
</head>

<body>

    <!-- NAVBAR -->
    <header class="main-navbar">

        <a href="index.php" class="brand">
            <span class="brand-icon">S</span>
            <span>sehatin</span>
        </a>

        <nav class="nav-links">
            <a href="index.php">Beranda</a>
            <a href="articles.php">Artikel</a>
            <a href="facilities.php" class="nav-active">Fasilitas</a>
        </nav>

        <div class="nav-actions">
            <a href="login.php" class="nav-login">Masuk</a>
            <a href="register.php" class="nav-register">Daftar</a>
        </div>

    </header>


    <main class="container facility-detail-page">

        <?php if ($facility): ?>

        <!-- BREADCRUMB -->
        <nav class="breadcrumb">
            <a href="index.php">Beranda</a>
            <span>/</span>
            <a href="facilities.php">Fasilitas</a>
            <span>/</span>
            <span><?= e($facility['name']) ?></span>
        </nav>


        <!-- BACK BUTTON -->
        <a href="facilities.php" class="back-link">
            <span>&larr;</span>
            Kembali ke daftar fasilitas
        </a>


        <!-- DETAIL HEADER -->
        <section class="detail-hero">

            <div class="detail-hero-content">

                <span class="detail-eyebrow">
                    INFORMASI FASILITAS KESEHATAN
                </span>

                <span class="facility-type">
                    <?= e($facility['type']) ?>
                </span>

                <h1><?= e($facility['name']) ?></h1>

                <p>
                    Informasi dasar fasilitas kesehatan
                    untuk membantu kamu menemukan layanan
                    yang sesuai dengan kebutuhan.
                </p>

            </div>

            <div class="detail-illustration">
                <div class="illustration-circle"></div>

                <div class="illustration-card">
                    <span class="illustration-icon">+</span>
                    <strong>SEHATIN</strong>
                    <small>Informasi kesehatan untuk semua</small>
                </div>
            </div>

        </section>


        <!-- DETAIL CONTENT -->
        <section class="detail-content">

            <div class="detail-main">

                <!-- DESCRIPTION -->
                <article class="detail-card">

                    <div class="detail-card-heading">
                        <span class="detail-icon">01</span>
                        <h2>Tentang fasilitas</h2>
                    </div>

                    <p class="detail-description">
                        <?= e($facility['description']) ?>
                    </p>

                </article>


                <!-- SERVICES -->
                <article class="detail-card">

                    <div class="detail-card-heading">
                        <span class="detail-icon">02</span>
                        <h2>Informasi layanan</h2>
                    </div>

                    <p class="section-note">
                        Berikut informasi umum yang dapat
                        ditanyakan langsung kepada fasilitas.
                    </p>

                    <ul class="service-list">

                        <?php foreach ($facility['services'] as $service): ?>

                        <li>
                            <span class="check-icon">&#10003;</span>
                            <?= e($service) ?>
                        </li>

                        <?php endforeach; ?>

                    </ul>

                    <p class="service-note">
                        Ketersediaan layanan perlu dikonfirmasi
                        langsung kepada pihak fasilitas.
                    </p>

                </article>

            </div>


            <!-- SIDEBAR INFORMATION -->
            <aside class="detail-sidebar">

                <div class="contact-card">

                    <span class="sidebar-eyebrow">
                        INFORMASI KONTAK
                    </span>

                    <h2>Informasi fasilitas</h2>

                    <div class="contact-item">

                        <span class="contact-icon">&#9906;</span>

                        <div>
                            <strong>Alamat</strong>
                            <p><?= e($facility['address']) ?></p>
                        </div>

                    </div>

                    <div class="contact-item">

                        <span class="contact-icon">&#9742;</span>

                        <div>
                            <strong>Kontak</strong>
                            <p><?= e($facility['contact']) ?></p>
                        </div>

                    </div>

                    <div class="contact-divider"></div>

                    <p class="contact-note">
                        Hubungi fasilitas secara langsung
                        untuk memastikan informasi layanan
                        dan jam operasional.
                    </p>

                </div>


                <div class="help-card">

                    <span class="help-icon">i</span>

                    <h3>Butuh informasi lebih lanjut?</h3>

                    <p>
                        Pastikan informasi fasilitas sebelum
                        berkunjung.
                    </p>

                    <a href="facilities.php" class="help-link">
                        Lihat fasilitas lainnya
                        <span>&rarr;</span>
                    </a>

                </div>

            </aside>

        </section>


        <!-- DISCLAIMER -->
        <div class="detail-disclaimer">
            <span>!</span>
            <p>
                Informasi pada halaman ini bersifat umum.
                Data layanan dan jam operasional dapat berubah.
                Silakan konfirmasi kepada fasilitas terkait.
            </p>
        </div>


        <?php else: ?>

        <!-- FACILITY NOT FOUND -->
        <section class="facility-not-found">

            <div class="not-found-icon">?</div>

            <h1>Fasilitas tidak ditemukan</h1>

            <p>
                Maaf, informasi fasilitas yang kamu cari
                belum tersedia atau tidak ditemukan.
            </p>

            <a href="facilities.php" class="back-button">
                Kembali ke daftar fasilitas
            </a>

        </section>

        <?php endif; ?>

    </main>


    <!-- FOOTER -->
    <footer class="main-footer">

        <a href="index.php" class="brand">
            <span class="brand-icon">S</span>
            <span>sehatin</span>
        </a>

        <p>
            Sehatin menyediakan informasi umum dan
            pencatatan keluhan.
        </p>

        <a href="#" class="back-to-top">
            Kembali ke atas
        </a>

    </footer>

</body>

</html>