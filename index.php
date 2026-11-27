<?php

// SESSION
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$isLoggedIn = isset($_SESSION["user_id"]);
$userName = $_SESSION["name"] ?? "";
$userRole = $_SESSION["role"] ?? "";

// KONEKSI DATABASE
require_once __DIR__ . "/config/database.php";

// MENGAMBIL 3 ARTIKEL TERBARU
$articles = [];

$sqlArticles = "SELECT
                    a.id,
                    a.title,
                    a.excerpt,
                    a.content,
                    c.name AS category
                FROM articles a
                INNER JOIN article_categories c
                    ON a.category_id = c.id
                WHERE a.status = 'published'
                ORDER BY a.created_at DESC
                LIMIT 3";

$resultArticles = $conn->query($sqlArticles);

if ($resultArticles) {
    while ($row = $resultArticles->fetch_assoc()) {

        // MENGHITUNG WAKTU BACA
        $wordCount = str_word_count(strip_tags($row['content']));
        $readingMinutes = max(1, (int) ceil($wordCount / 200));

        $articles[] = [
            'id' => $row['id'],
            'category' => $row['category'],
            'title' => $row['title'],
            'description' => $row['excerpt'],
            'time' => $readingMinutes . ' menit baca'
        ];
    }
}

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
    <link rel="stylesheet" href="assets/css/home.css?v=<?= filemtime(__DIR__ . '/assets/css/home.css') ?>">
    <script src="assets/js/script.js?v=<?= filemtime(__DIR__ . '/assets/js/script.js') ?>" defer></script>
</head>

<body class="home-body">

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
            <?php if ($isLoggedIn): ?>
                <?php
                $dashboardUrl = $userRole === "admin"
                    ? "admin/dashboard.php"
                    : "user/dashboard.php";
                ?>
                <a href="<?= htmlspecialchars($dashboardUrl) ?>" class="home-auth-link">
                    <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>
                </a>
                <a href="logout.php" class="home-auth-link home-auth-link-primary">
                    Keluar
                </a>
            <?php else: ?>
                <a href="login.php" class="home-auth-link">Masuk</a>
                <a href="register.php" class="home-auth-link home-auth-link-primary">
                    Daftar
                </a>
            <?php endif; ?>
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

            <!-- HERO PHOTO -->
            <div class="hero-visual">
                <img
                    class="hero-photo"
                    src="assets/images/sehatin-hero.webp"
                    alt="Dokter dan pasien berbincang di ruang konsultasi yang nyaman"
                    fetchpriority="high"
                    decoding="async">

                <div class="hero-photo-caption">
                    <span>INFORMASI KESEHATAN UNTUK SEMUA</span>
                    <strong>Temukan informasi yang kamu butuhkan.</strong>
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

                <?php if (empty($articles)): ?>

                <div class="article-empty-state">
                    <strong>Belum ada artikel yang diterbitkan.</strong>
                    <p>Silakan kembali lagi nanti untuk melihat bacaan pilihan.</p>
                </div>

                <?php else: ?>

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
                            <a href="articles-detail.php?id=<?= (int) $article['id'] ?>">
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

                <?php endif; ?>

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
