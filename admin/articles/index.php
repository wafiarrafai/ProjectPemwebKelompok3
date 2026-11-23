<?php
session_start();

$adminName = $_SESSION['name']
    ?? $_SESSION['username']
    ?? 'Admin';

// DATA ARTIKEL CONTOH
$articles = [
    [
        'id' => 1,
        'title' => 'Kenali sinyal tubuh dengan lebih tenang',
        'category' => 'Kesehatan umum',
        'updated' => '20 Nov 2026',
        'status' => 'Terbit'
    ],
    [
        'id' => 2,
        'title' => 'Rutinitas kecil untuk waktu istirahat',
        'category' => 'Pola hidup',
        'updated' => '18 Nov 2026',
        'status' => 'Terbit'
    ],
    [
        'id' => 3,
        'title' => 'Informasi layanan kesehatan keluarga',
        'category' => 'Keluarga',
        'updated' => '16 Nov 2026',
        'status' => 'Draf'
    ]
];

// FILTER PENCARIAN
$keyword = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$categoryFilter = $_GET['category'] ?? '';

$filteredArticles = array_filter($articles, function ($article) use (
    $keyword,
    $statusFilter,
    $categoryFilter
) {
    $matchKeyword = $keyword === ''
        || stripos($article['title'], $keyword) !== false;

    $matchStatus = $statusFilter === ''
        || $article['status'] === $statusFilter;

    $matchCategory = $categoryFilter === ''
        || $article['category'] === $categoryFilter;

    return $matchKeyword && $matchStatus && $matchCategory;
});

function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manajemen Artikel - SEHATIN</title>

    <link rel="stylesheet" href="../../assets/css/reset.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="../../assets/css/admin-articles.css">
</head>

<body class="admin-dashboard-page">

    <div class="admin-layout">

        <!-- SIDEBAR -->
        <aside class="admin-sidebar">

            <a href="../dashboard.php" class="admin-brand">
                <span class="admin-brand-icon">S</span>
                <span>sehatin</span>
            </a>

            <div class="admin-menu">

                <p class="admin-menu-label">PENGELOLAAN</p>

                <nav class="admin-nav">

                    <a href="../dashboard.php" class="admin-nav-link">
                        <span>●</span>
                        Dashboard
                    </a>

                    <a href="index.php" class="admin-nav-link active">
                        <span>▤</span>
                        Artikel
                    </a>

                    <a href="../categories/" class="admin-nav-link">
                        <span>▦</span>
                        Kategori
                    </a>

                    <a href="../facilities/" class="admin-nav-link">
                        <span>＋</span>
                        Fasilitas
                    </a>

                    <a href="../complaints/" class="admin-nav-link">
                        <span>▣</span>
                        Keluhan
                    </a>

                    <a href="../users/" class="admin-nav-link">
                        <span>♙</span>
                        Pengguna
                    </a>

                </nav>

            </div>

            <div class="admin-sidebar-bottom">

                <p><?= e($adminName) ?> · Admin</p>

                <a href="../../logout.php">
                    Keluar dari akun
                </a>

            </div>

        </aside>


        <!-- MAIN CONTENT -->
        <main class="admin-main admin-articles-main">

            <!-- PAGE HEADER -->
            <header class="articles-header">

                <div>
                    <h1>Manajemen artikel</h1>

                    <p>
                        Kelola draf dan artikel yang sudah diterbitkan.
                    </p>
                </div>

                <a href="create.php" class="articles-add-button">
                    + Tambah artikel
                </a>

            </header>


            <!-- SEARCH AND FILTER -->
            <section class="articles-search-section">

                <form action="index.php" method="GET">

                    <label for="searchArticle">
                        Cari artikel
                    </label>

                    <div class="articles-search-controls">

                        <input type="text" id="searchArticle" name="search" placeholder="Cari judul"
                            value="<?= e($keyword) ?>">

                        <select name="status">

                            <option value="">Semua status</option>

                            <option value="Terbit" <?= $statusFilter === 'Terbit' ? 'selected' : '' ?>>
                                Terbit
                            </option>

                            <option value="Draf" <?= $statusFilter === 'Draf' ? 'selected' : '' ?>>
                                Draf
                            </option>

                        </select>


                        <select name="category">

                            <option value="">Kategori</option>

                            <option value="Kesehatan umum"
                                <?= $categoryFilter === 'Kesehatan umum' ? 'selected' : '' ?>>
                                Kesehatan umum
                            </option>

                            <option value="Pola hidup" <?= $categoryFilter === 'Pola hidup' ? 'selected' : '' ?>>
                                Pola hidup
                            </option>

                            <option value="Keluarga" <?= $categoryFilter === 'Keluarga' ? 'selected' : '' ?>>
                                Keluarga
                            </option>

                        </select>

                        <button type="submit" class="articles-search-button">
                            Cari
                        </button>

                    </div>

                </form>

            </section>


            <!-- ARTICLE TABLE -->
            <section class="articles-table-card">

                <div class="articles-table-wrapper">

                    <table class="articles-table">

                        <thead>

                            <tr>
                                <th>ARTIKEL</th>
                                <th>KATEGORI</th>
                                <th>DIPERBARUI</th>
                                <th>STATUS</th>
                                <th>AKSI</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (count($filteredArticles) > 0): ?>

                            <?php foreach ($filteredArticles as $article): ?>

                            <tr>

                                <td class="article-title-cell">
                                    <?= e($article['title']) ?>
                                </td>

                                <td class="article-category-cell">
                                    <?= e($article['category']) ?>
                                </td>

                                <td class="article-date-cell">
                                    <?= e($article['updated']) ?>
                                </td>

                                <td>
                                    <span
                                        class="article-status <?= $article['status'] === 'Terbit' ? 'published' : 'draft' ?>">
                                        <?= e($article['status']) ?>
                                    </span>
                                </td>

                                <td>
                                    <a href="edit.php?id=<?= (int) $article['id'] ?>" class="article-edit-button">
                                        Edit
                                    </a>
                                </td>

                            </tr>

                            <?php endforeach; ?>

                            <?php else: ?>

                            <tr>
                                <td colspan="5" class="articles-empty">
                                    Artikel tidak ditemukan.
                                    Coba kata kunci atau filter lain.
                                </td>
                            </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>


            <!-- FOOTER -->
            <footer class="admin-footer">

                <p>
                    © 2026 SEHATIN. Admin Dashboard.
                </p>

            </footer>

        </main>

    </div>

</body>

</html>