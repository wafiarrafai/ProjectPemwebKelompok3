<?php

$facilities = [
    [
        'type' => 'Puskesmas',
        'name' => 'Puskesmas Sejahtera',
        'address' => 'Jl. Kesehatan No. 12, Purwokerto',
        'contact' => 'Kontak belum tersedia'
    ],
    [
        'type' => 'Klinik',
        'name' => 'Klinik Bersama',
        'address' => 'Jl. Melati No. 8, Purwokerto',
        'contact' => 'Kontak belum tersedia'
    ],
    [
        'type' => 'Rumah sakit',
        'name' => 'RSU Contoh Sehat',
        'address' => 'Area Purwokerto',
        'contact' => 'Kontak belum tersedia'
    ],
    [
        'type' => 'Puskesmas',
        'name' => 'Puskesmas Sehat Mandiri',
        'address' => 'Jl. Merdeka, Purwokerto',
        'contact' => 'Kontak belum tersedia'
    ],
    [
        'type' => 'Klinik',
        'name' => 'Klinik Medika',
        'address' => 'Jl. Jenderal Sudirman, Purwokerto',
        'contact' => 'Kontak belum tersedia'
    ],
    [
        'type' => 'Rumah sakit',
        'name' => 'Rumah Sakit Harapan',
        'address' => 'Purwokerto',
        'contact' => 'Kontak belum tersedia'
    ]
];

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fasilitas Kesehatan - SEHATIN</title>

    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/facilities.css">
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


    <main class="container facilities-page">

        <!-- BREADCRUMB -->
        <nav class="breadcrumb">
            <a href="index.php">Beranda</a>
            <span>/</span>
            <span>Fasilitas kesehatan</span>
        </nav>


        <!-- PAGE HEADING -->
        <section class="facilities-heading">

            <h1>Temukan fasilitas kesehatan</h1>

            <p>
                Lihat informasi dasar fasilitas. Hubungi fasilitas
                untuk memastikan layanan dan jam operasional.
            </p>

        </section>


        <!-- SEARCH -->
        <section class="facility-search">

            <label for="searchFacility">
                Cari fasilitas
            </label>

            <div class="facility-search-form">

                <input type="text" id="searchFacility" placeholder="Nama fasilitas atau area" autocomplete="off">

                <button type="button" id="searchFacilityButton">
                    Cari
                </button>

            </div>

        </section>


        <!-- CATEGORY FILTER -->
        <section class="facility-filters">

            <button class="facility-filter active" data-type="Semua jenis">
                Semua jenis
            </button>

            <button class="facility-filter" data-type="Puskesmas">
                Puskesmas
            </button>

            <button class="facility-filter" data-type="Klinik">
                Klinik
            </button>

            <button class="facility-filter" data-type="Rumah sakit">
                Rumah sakit
            </button>

        </section>


        <!-- FACILITY LIST -->
        <section class="facility-list">

            <div class="facility-grid" id="facilityGrid">

                <?php foreach ($facilities as $facility): ?>

                <article class="facility-card" data-type="<?php echo htmlspecialchars($facility['type']); ?>"
                    data-name="<?php echo htmlspecialchars($facility['name']); ?>"
                    data-address="<?php echo htmlspecialchars($facility['address']); ?>">

                    <span class="facility-type">
                        <?php echo htmlspecialchars($facility['type']); ?>
                    </span>

                    <h2>
                        <?php echo htmlspecialchars($facility['name']); ?>
                    </h2>

                    <p class="facility-address">
                        <?php echo htmlspecialchars($facility['address']); ?>
                    </p>

                    <p class="facility-contact">
                        <?php echo htmlspecialchars($facility['contact']); ?>
                    </p>

                </article>

                <?php endforeach; ?>

            </div>

            <p id="facilityEmptyMessage" class="empty-message" hidden>
                Fasilitas tidak ditemukan. Coba kata kunci lain.
            </p>

        </section>

    </main>


    <!-- FOOTER -->
    <footer class="main-footer">

        <a href="index.php" class="brand">
            <span class="brand-icon">S</span>
            <span>sehatin</span>
        </a>

        <p>
            Sehatin menyediakan informasi umum dan pencatatan keluhan.
        </p>

        <a href="#" class="back-to-top">
            Kembali ke atas
        </a>

    </footer>

</body>

</html>