<?php

require_once __DIR__ . "/config/database.php";

// AMBIL ID ARTIKEL DARI URL
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

$article = null;

// AMBIL DETAIL ARTIKEL DARI DATABASE
if ($id !== false && $id !== null && $id > 0) {

    $stmt = $conn->prepare(
        "SELECT
            a.id,
            a.title,
            a.excerpt,
            a.content,
            c.name AS category
         FROM articles a
         INNER JOIN article_categories c
            ON a.category_id = c.id
         WHERE a.id = ?
           AND a.status = 'published'
         LIMIT 1"
    );

    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $article = $result->fetch_assoc();

        $stmt->close();
    }
}

// LENGKAPI DATA ARTIKEL
if ($article) {

    $wordCount = str_word_count(strip_tags($article['content']));
    $article['reading_time'] = max(1, (int) ceil($wordCount / 200));

    $category = strtolower($article['category']);

    if (strpos($category, 'pola hidup') !== false) {
        $article['image_type'] = 'lifestyle';
    } elseif (strpos($category, 'keluarga') !== false) {
        $article['image_type'] = 'family';
    } else {
        $article['image_type'] = 'health';
    }

    // MEMECAH ISI ARTIKEL MENJADI PARAGRAF
    $article['content'] = preg_split(
        '/\r\n|\r|\n/',
        trim($article['content'])
    );

}

// FUNGSI KEAMANAN OUTPUT
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
        <?= $article ? e($article['title']) : 'Artikel Tidak Ditemukan' ?> - SEHATIN
    </title>

    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/articles-detail.css">
</head>

<body class="article-detail-page">

    <?php if (!$article): ?>

    <main class="article-not-found">
        <div class="not-found-icon">📄</div>
        <h1>Artikel tidak ditemukan</h1>
        <p>Maaf, artikel yang kamu cari tidak tersedia.</p>
        <a href="articles.php" class="back-button">
            Kembali ke Artikel
        </a>
    </main>

    <?php else: ?>

    <main class="article-detail-container">

        <!-- Breadcrumb -->
        <nav class="article-breadcrumb">
            <a href="index.php">Beranda</a>
            <span>/</span>
            <a href="articles.php">Artikel</a>
            <span>/</span>
            <span>Detail Artikel</span>
        </nav>

        <!-- Header Artikel -->
        <header class="article-header">

            <a href="articles.php" class="article-back-link">
                ← Kembali ke Artikel
            </a>

            <span class="article-category">
                <?= e($article['category']) ?>
            </span>

            <h1><?= e($article['title']) ?></h1>

            <p class="article-description">
                <?= e($article['excerpt']) ?>
            </p>

            <div class="article-meta">
                <span>✦ SEHATIN</span>
                <span>•</span>
                <span><?= e($article['reading_time']) ?> menit baca</span>
            </div>

        </header>

        <!-- Ilustrasi Artikel -->
        <section class="article-cover">
            <?php if ($article['image_type'] === 'health'): ?>
            <div class="cover-illustration health-illustration">
                <span>🩺</span>
                <span>💚</span>
                <span>🩹</span>
            </div>

            <?php elseif ($article['image_type'] === 'lifestyle'): ?>
            <div class="cover-illustration lifestyle-illustration">
                <span>🌱</span>
                <span>☀️</span>
                <span>💧</span>
            </div>

            <?php else: ?>
            <div class="cover-illustration family-illustration">
                <span>👨‍👩‍👧‍👦</span>
                <span>💚</span>
            </div>
            <?php endif; ?>
        </section>

        <!-- Isi Artikel -->
        <article class="article-content">

            <div class="article-content-heading">
                <span class="heading-line"></span>
                <h2>Isi Artikel</h2>
            </div>

            <?php foreach ($article['content'] as $paragraph): ?>
            <p><?= e($paragraph) ?></p>
            <?php endforeach; ?>

            <div class="article-note">
                <span class="note-icon">💡</span>
                <div>
                    <strong>Informasi Kesehatan</strong>
                    <p>
                        Artikel ini bertujuan memberikan informasi umum.
                        Untuk keluhan kesehatan tertentu, konsultasikan
                        dengan tenaga kesehatan profesional.
                    </p>
                </div>
            </div>

        </article>

        <!-- Footer Artikel -->
        <footer class="article-footer">
            <div>
                <h3>Jaga kesehatan bersama SEHATIN</h3>
                <p>Mulai dari informasi, untuk hidup yang lebih sehat.</p>
            </div>

            <a href="articles.php" class="back-button">
                Lihat Artikel Lainnya
            </a>
        </footer>

    </main>

    <?php endif; ?>

</body>

</html>