<?php

// Memeriksa login dan role admin
$required_role = "admin";
require_once __DIR__ . "/../../includes/auth-check.php";
require_once __DIR__ . "/../../config/database.php";

$adminName = $_SESSION["name"]
    ?? $_SESSION["username"]
    ?? "Admin";

// Membuat token CSRF jika belum tersedia
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Memproses penghapusan artikel
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_article"])) {

    $articleId = filter_input(INPUT_POST, "article_id", FILTER_VALIDATE_INT);
    $csrfToken = $_POST["csrf_token"] ?? "";

    // Memeriksa token CSRF
    if (
        !is_string($csrfToken) ||
        !hash_equals($_SESSION["csrf_token"], $csrfToken)
    ) {
        $_SESSION["article_error"] = "Permintaan tidak valid.";
        header("Location: index.php");
        exit;
    }

    if (!$articleId || $articleId <= 0) {
        $_SESSION["article_error"] = "ID artikel tidak valid.";
        header("Location: index.php");
        exit;
    }

    // Menghapus artikel berdasarkan ID
    $stmtDelete = $conn->prepare(
        "DELETE FROM articles WHERE id = ?"
    );

    if (!$stmtDelete) {
        $_SESSION["article_error"] = "Gagal menyiapkan penghapusan artikel.";
    } else {
        $stmtDelete->bind_param("i", $articleId);

        if ($stmtDelete->execute()) {
            if ($stmtDelete->affected_rows > 0) {
                $_SESSION["article_success"] = "Artikel berhasil dihapus.";
            } else {
                $_SESSION["article_error"] = "Artikel tidak ditemukan.";
            }
        } else {
            $_SESSION["article_error"] = "Gagal menghapus artikel.";
        }

        $stmtDelete->close();
    }

    // Mengganti token setelah proses
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

    header("Location: index.php");
    exit;
}

// Mengambil kategori dari database
$categories = [];

$sqlCategories = "SELECT id, name FROM article_categories ORDER BY name ASC";
$resultCategories = $conn->query($sqlCategories);

if ($resultCategories) {
    while ($row = $resultCategories->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Mengambil artikel dari database
$articles = [];

$sqlArticles = "SELECT
                    a.id,
                    a.title,
                    ac.name AS category,
                    DATE_FORMAT(a.updated_at, '%d %b %Y') AS updated,
                    a.status
                FROM articles a
                LEFT JOIN article_categories ac
                    ON a.category_id = ac.id
                ORDER BY a.updated_at DESC";

$resultArticles = $conn->query($sqlArticles);

if ($resultArticles) {
    while ($row = $resultArticles->fetch_assoc()) {

        // Mengubah status database menjadi teks tampilan
        $row["status"] = $row["status"] === "published"
            ? "Terbit"
            : "Draf";

        $articles[] = $row;
    }
}

// Filter pencarian
$keyword = trim($_GET["search"] ?? "");
$statusFilter = $_GET["status"] ?? "";
$categoryFilter = $_GET["category"] ?? "";

// Validasi filter status
if (!in_array($statusFilter, ["", "Terbit", "Draf"], true)) {
    $statusFilter = "";
}

// Validasi filter kategori
$allowedCategories = array_column($categories, "name");

if (
    $categoryFilter !== "" &&
    !in_array($categoryFilter, $allowedCategories, true)
) {
    $categoryFilter = "";
}

// Menerapkan filter artikel
$filteredArticles = array_filter(
    $articles,
    function ($article) use (
        $keyword,
        $statusFilter,
        $categoryFilter
    ) {
        $matchKeyword = $keyword === ""
            || stripos($article["title"], $keyword) !== false;

        $matchStatus = $statusFilter === ""
            || $article["status"] === $statusFilter;

        $matchCategory = $categoryFilter === ""
            || $article["category"] === $categoryFilter;

        return $matchKeyword && $matchStatus && $matchCategory;
    }
);

// Mengamankan output HTML
function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
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
            <?php if (!empty($_SESSION["article_success"])): ?>
                <p><?= e($_SESSION["article_success"]) ?></p>
                <?php unset($_SESSION["article_success"]); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION["article_error"])): ?>
                <p><?= e($_SESSION["article_error"]) ?></p>
                <?php unset($_SESSION["article_error"]); ?>
            <?php endif; ?>

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

                            <?php foreach ($categories as $category): ?>

                                <option
                                    value="<?= e($category['name']) ?>"
                                    <?= $categoryFilter === $category['name'] ? 'selected' : '' ?>
                                >
                                    <?= e($category['name']) ?>
                                </option>

                            <?php endforeach; ?>

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

                                    <form method="POST" action="index.php"
                                        onsubmit="return confirm('Yakin ingin menghapus artikel ini?');"
                                        style="display: inline;">

                                        <input type="hidden" name="article_id"
                                            value="<?= (int) $article['id'] ?>">

                                        <input type="hidden" name="csrf_token"
                                            value="<?= e($_SESSION['csrf_token']) ?>">

                                        <button type="submit" name="delete_article" value="1">
                                            Hapus
                                        </button>
                                    </form>
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