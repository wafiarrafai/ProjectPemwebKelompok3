<?php

$articles = [
    [
        'id' => 1,
        'category' => 'Kesehatan umum',
        'title' => 'Kenali sinyal tubuh dengan lebih tenang',
        'description' => 'Panduan umum untuk mencatat hal yang ingin kamu diskusikan dengan tenaga kesehatan.',
        'reading_time' => '4 menit baca',
        'image_type' => 'health'
    ],
    [
        'id' => 2,
        'category' => 'Pola hidup',
        'title' => 'Rutinitas kecil untuk waktu istirahat',
        'description' => 'Ide kebiasaan sehari-hari yang bisa dipertimbangkan sesuai kebutuhanmu.',
        'reading_time' => '3 menit baca',
        'image_type' => 'lifestyle'
    ],
    [
        'id' => 3,
        'category' => 'Keluarga',
        'title' => 'Menyimpan informasi kesehatan keluarga',
        'description' => 'Cara sederhana menyiapkan informasi sebelum mencari layanan yang sesuai.',
        'reading_time' => '5 menit baca',
        'image_type' => 'family'
    ],
    [
        'id' => 4,
        'category' => 'Kesehatan umum',
        'title' => 'Pentingnya pemeriksaan kesehatan rutin',
        'description' => 'Mengenal manfaat pemeriksaan kesehatan secara berkala.',
        'reading_time' => '4 menit baca',
        'image_type' => 'health'
    ],
    [
        'id' => 5,
        'category' => 'Pola hidup',
        'title' => 'Membangun kebiasaan hidup aktif',
        'description' => 'Langkah sederhana untuk mulai bergerak lebih aktif setiap hari.',
        'reading_time' => '3 menit baca',
        'image_type' => 'lifestyle'
    ],
    [
        'id' => 6,
        'category' => 'Keluarga',
        'title' => 'Menjaga kesehatan bersama keluarga',
        'description' => 'Kebiasaan sehat yang dapat dilakukan bersama orang-orang terdekat.',
        'reading_time' => '5 menit baca',
        'image_type' => 'family'
    ]
];

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Artikel Kesehatan - SEHATIN</title>

    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/articles.css">
</head>

<body>

    <main class="articles-page">

        <!-- BREADCRUMB -->
        <nav class="article-breadcrumb">
            <a href="index.php">Beranda</a>
            <span>/</span>
            <span>Artikel</span>
        </nav>


        <!-- HEADING -->
        <section class="articles-heading">

            <h1>Artikel kesehatan</h1>

            <p>
                Baca informasi umum yang ditulis dengan bahasa sederhana dan jelas.
            </p>

        </section>


        <!-- SEARCH -->
        <section class="article-search">

            <label for="searchArticle">Cari artikel</label>

            <div class="article-search-form">

                <input type="text" id="searchArticle" placeholder="Cari judul atau topik..." autocomplete="off">

                <button type="button" id="searchArticleButton">
                    Cari
                </button>

            </div>

        </section>


        <!-- CATEGORY FILTER -->
        <section class="article-filters">

            <button type="button" class="article-filter active" data-category="Semua artikel">
                Semua artikel
            </button>

            <button type="button" class="article-filter" data-category="Keluarga">
                Keluarga
            </button>

            <button type="button" class="article-filter" data-category="Pola hidup">
                Pola hidup
            </button>

            <button type="button" class="article-filter" data-category="Kesehatan umum">
                Kesehatan umum
            </button>

        </section>


        <!-- ARTICLE LIST -->
        <section class="article-list">

            <div class="article-grid" id="articleGrid">

                <?php foreach ($articles as $article): ?>

                <article class="article-card"
                    data-category="<?= htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8') ?>"
                    data-search="<?= htmlspecialchars($article['title'] . ' ' . $article['description'] . ' ' . $article['category'], ENT_QUOTES, 'UTF-8') ?>">

                    <!-- ILLUSTRATION -->
                    <div class="article-image <?= htmlspecialchars($article['image_type'], ENT_QUOTES, 'UTF-8') ?>">

                        <div class="image-label">
                            INFO KESEHATAN
                        </div>

                        <div class="image-circle"></div>
                        <div class="image-circle-small"></div>

                    </div>


                    <!-- ARTICLE CONTENT -->
                    <div class="article-content">

                        <span class="article-category">
                            <?= htmlspecialchars($article['category'], ENT_QUOTES, 'UTF-8') ?>
                        </span>

                        <h2>
                            <?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>
                        </h2>

                        <p>
                            <?= htmlspecialchars($article['description'], ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <span class="article-reading-time">
                            <?= htmlspecialchars($article['reading_time'], ENT_QUOTES, 'UTF-8') ?>
                        </span>

                        <a href="articles-detail.php?id=<?= (int) $article['id'] ?>" class="article-read-link"
                            aria-label="Baca <?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>">
                            Baca artikel
                        </a>

                    </div>

                </article>

                <?php endforeach; ?>

            </div>


            <!-- EMPTY MESSAGE -->
            <div class="article-empty" id="articleEmpty" hidden>
                <h3>Artikel tidak ditemukan</h3>
                <p>Coba gunakan kata kunci atau kategori lainnya.</p>
            </div>


            <!-- PAGINATION -->
            <div class="article-pagination" id="articlePagination">

                <button type="button" id="prevPage" disabled>
                    Sebelumnya
                </button>

                <div id="pageNumbers"></div>

                <button type="button" id="nextPage">
                    Berikutnya
                </button>

            </div>

        </section>

    </main>


    <!-- ARTICLE INTERACTION -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        const cards = Array.from(
            document.querySelectorAll('.article-card')
        );

        const searchInput = document.getElementById('searchArticle');
        const searchButton = document.getElementById('searchArticleButton');

        const filterButtons = document.querySelectorAll('.article-filter');

        const prevButton = document.getElementById('prevPage');
        const nextButton = document.getElementById('nextPage');

        const pageNumbers = document.getElementById('pageNumbers');
        const emptyMessage = document.getElementById('articleEmpty');
        const pagination = document.getElementById('articlePagination');

        const perPage = 3;

        let currentPage = 1;
        let activeCategory = 'Semua artikel';
        let searchKeyword = '';

        function getFilteredCards() {

            return cards.filter(function(card) {

                const category = card.dataset.category;
                const searchText = card.dataset.search.toLowerCase();

                const matchCategory =
                    activeCategory === 'Semua artikel' ||
                    category === activeCategory;

                const matchSearch =
                    searchText.includes(searchKeyword);

                return matchCategory && matchSearch;

            });

        }

        function renderArticles() {

            const filteredCards = getFilteredCards();

            const totalPages = Math.ceil(filteredCards.length / perPage);

            if (currentPage > totalPages) {
                currentPage = Math.max(1, totalPages);
            }

            cards.forEach(function(card) {
                card.style.display = 'none';
            });

            const start = (currentPage - 1) * perPage;
            const end = start + perPage;

            filteredCards.slice(start, end).forEach(function(card) {
                card.style.display = 'block';
            });

            emptyMessage.hidden = filteredCards.length > 0;
            pagination.style.display = filteredCards.length > 0 ? 'flex' : 'none';

            prevButton.disabled = currentPage <= 1;
            nextButton.disabled = currentPage >= totalPages;

            pageNumbers.innerHTML = '';

            for (let i = 1; i <= totalPages; i++) {

                const button = document.createElement('button');

                button.type = 'button';
                button.textContent = i;

                if (i === currentPage) {
                    button.classList.add('active');
                }

                button.addEventListener('click', function() {
                    currentPage = i;
                    renderArticles();
                });

                pageNumbers.appendChild(button);
            }

        }

        function applySearch() {

            searchKeyword = searchInput.value.trim().toLowerCase();
            currentPage = 1;

            renderArticles();

        }

        searchButton.addEventListener('click', applySearch);

        searchInput.addEventListener('input', applySearch);

        searchInput.addEventListener('keydown', function(event) {

            if (event.key === 'Enter') {
                applySearch();
            }

        });

        filterButtons.forEach(function(button) {

            button.addEventListener('click', function() {

                filterButtons.forEach(function(item) {
                    item.classList.remove('active');
                });

                button.classList.add('active');

                activeCategory = button.dataset.category;

                currentPage = 1;

                renderArticles();

            });

        });

        prevButton.addEventListener('click', function() {

            if (currentPage > 1) {
                currentPage--;
                renderArticles();
            }

        });

        nextButton.addEventListener('click', function() {

            const totalPages = Math.ceil(
                getFilteredCards().length / perPage
            );

            if (currentPage < totalPages) {
                currentPage++;
                renderArticles();
            }

        });

        renderArticles();

    });
    </script>

</body>

</html>