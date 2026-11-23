<?php
$articles = [
    [
        'category' => 'Kesehatan umum',
        'title' => 'Kenali sinyal tubuh dengan lebih tenang',
        'description' => 'Panduan umum untuk mencatat hal yang ingin kamu diskusikan dengan tenaga kesehatan.',
        'time' => '4 menit baca'
    ],
    [
        'category' => 'Pola hidup',
        'title' => 'Rutinitas kecil untuk waktu istirahat',
        'description' => 'Ide kebiasaan sehari-hari yang bisa dipertimbangkan sesuai kebutuhanmu.',
        'time' => '3 menit baca'
    ],
    [
        'category' => 'Keluarga',
        'title' => 'Menyimpan informasi kesehatan keluarga',
        'description' => 'Cara sederhana menyiapkan informasi sebelum mencari layanan yang sesuai.',
        'time' => '5 menit baca'
    ]
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>SEHATIN - Informasi Kesehatan untuk Semua</title>

    <meta name="description"
        content="Temukan informasi kesehatan umum, fasilitas kesehatan, dan catat keluhanmu bersama SEHATIN.">

    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/home.css">
    <script src="assets/js/script.js" defer></script>
</head>

<body>

    <header class="home-header">
        <div class="home-header-inner">
            <a href="index.php" class="home-brand" aria-label="Sehatin, beranda">
                <span class="home-brand-icon" aria-hidden="true">S</span>
                <span>sehatin</span>
            </a>

            <nav class="home-header-nav" aria-label="Navigasi utama">
                <a href="index.php" aria-current="page">Beranda</a>
                <a href="articles.php">Artikel</a>
                <a href="facilities.php">Fasilitas</a>
            </nav>

            <div class="home-header-actions">
                <a href="login.php" class="home-auth-link">Masuk</a>
                <a href="register.php" class="home-auth-link home-auth-link-primary">Daftar</a>
            </div>
        </div>
    </header>

    <main class="container">

        <!-- HERO SECTION -->
        <section class="hero">

            <div class="hero-content">
                <span class="eyebrow">
                    INFORMASI KESEHATAN UNTUK SEMUA
                </span>

                <h1>
                    Informasi sehat, untuk langkah yang lebih tenang.
                </h1>

                <p class="hero-description">
                    Baca artikel kesehatan umum, temukan fasilitas,
                    dan catat keluhanmu dalam satu tempat.
                </p>

                <div class="hero-buttons">
                    <a href="articles.php" class="btn btn-primary">
                        Jelajahi artikel
                    </a>

                    <a href="facilities.php" class="btn btn-secondary">
                        Cari fasilitas
                    </a>
                </div>

                <p class="hero-note">
                    Informasi umum, bukan diagnosis atau pengganti
                    tenaga kesehatan.
                </p>
            </div>

            <!-- HERO ILLUSTRATION -->
            <div class="hero-visual">

                <div class="circle circle-coral"></div>
                <div class="circle circle-yellow"></div>

                <div class="info-card">
                    <span>MULAI DARI INFORMASI</span>

                    <h2>
                        Cari tahu hal yang bisa kamu lakukan hari ini.
                    </h2>

                    <p>
                        Artikel, fasilitas, dan catatan keluhan
                        dalam satu alur.
                    </p>
                </div>

                <div class="facility-card">
                    <span>DIREKTORI FASILITAS</span>
                    <strong>Info penting tersusun rapi</strong>
                </div>

            </div>

        </section>


        <!-- SELECTED ARTICLES -->
        <section class="articles-section" id="artikel">

            <div class="section-heading">
                <div>
                    <h2>Bacaan pilihan</h2>
                    <p>
                        Artikel umum yang dikurasi untuk dibaca dengan nyaman.
                    </p>
                </div>

                <a href="articles.php" class="text-link">
                    Lihat semua artikel
                </a>
            </div>

            <div class="article-grid">

                <?php foreach ($articles as $index => $article): ?>

                <article class="article-card">

                    <div class="article-thumbnail thumbnail-<?php echo $index + 1; ?>">
                        <div class="thumbnail-label">
                            INFO KESEHATAN
                        </div>

                        <div class="thumbnail-circle"></div>
                        <div class="thumbnail-circle-small"></div>
                    </div>

                    <div class="article-content">

                        <span class="article-category">
                            <?php echo htmlspecialchars($article['category']); ?>
                        </span>

                        <h3>
                            <a href="articles-detail.php?id=<?= $index + 1 ?>">
                                <?php echo htmlspecialchars($article['title']); ?>
                            </a>
                        </h3>

                        <p>
                            <?php echo htmlspecialchars($article['description']); ?>
                        </p>

                        <span class="read-time">
                            <?php echo htmlspecialchars($article['time']); ?>
                        </span>

                    </div>

                </article>

                <?php endforeach; ?>

            </div>

        </section>


        <!-- QUICK ACCESS -->
        <section class="quick-section">

            <div class="section-heading">
                <div>
                    <h2>Mulai dari yang kamu butuhkan</h2>
                </div>
            </div>

            <div class="quick-grid">

                <a href="articles.php" class="quick-card">
                    <span class="quick-number">01</span>

                    <h3>Baca artikel</h3>

                    <p>
                        Temukan informasi kesehatan umum yang
                        ringkas dan mudah dibaca.
                    </p>
                </a>

                <a href="facilities.php" class="quick-card">
                    <span class="quick-number">02</span>

                    <h3>Cari fasilitas</h3>

                    <p>
                        Lihat alamat dan kontak dasar fasilitas
                        kesehatan.
                    </p>
                </a>

                <a href="login.php" class="quick-card">
                    <span class="quick-number">03</span>

                    <h3>Catat keluhan</h3>

                    <p>
                        Simpan keluhan dan pantau status tindak lanjut
                        administratif.
                    </p>
                </a>

            </div>

        </section>

    </main>

</body>

</html>
