<?php

$required_role = "admin";
require_once __DIR__ . "/../../includes/auth-check.php";
require_once __DIR__ . "/../../config/database.php";

$adminName = $_SESSION["name"]
    ?? $_SESSION["username"]
    ?? "Admin";

// CSRF token
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// NOTIFIKASI
$successMessage = $_SESSION["category_success"] ?? "";
$errorMessage = $_SESSION["category_error"] ?? "";

unset($_SESSION["category_success"], $_SESSION["category_error"]);

// PROSES HAPUS KATEGORI
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {

    $deleteId = filter_input(INPUT_POST, "delete_id", FILTER_VALIDATE_INT);
    $csrfToken = $_POST["csrf_token"] ?? "";

    if (
        !is_string($csrfToken) ||
        !hash_equals($_SESSION["csrf_token"], $csrfToken)
    ) {
        $_SESSION["category_error"] = "Permintaan tidak valid.";
    } elseif (!$deleteId || $deleteId <= 0) {
        $_SESSION["category_error"] = "ID kategori tidak valid.";
    } else {

        // Memeriksa apakah kategori masih digunakan artikel
        $stmtCheck = $conn->prepare(
            "SELECT COUNT(*) AS total
             FROM articles
             WHERE category_id = ?"
        );

        $stmtCheck->bind_param("i", $deleteId);
        $stmtCheck->execute();

        $resultCheck = $stmtCheck->get_result();
        $usage = $resultCheck->fetch_assoc()["total"];

        $stmtCheck->close();

        if ($usage > 0) {
            $_SESSION["category_error"] =
                "Kategori tidak bisa dihapus karena masih digunakan oleh artikel.";
        } else {

            $stmtDelete = $conn->prepare(
                "DELETE FROM article_categories WHERE id = ?"
            );

            $stmtDelete->bind_param("i", $deleteId);

            if ($stmtDelete->execute() && $stmtDelete->affected_rows > 0) {
                $_SESSION["category_success"] = "Kategori berhasil dihapus.";
            } else {
                $_SESSION["category_error"] = "Kategori gagal dihapus atau tidak ditemukan.";
            }

            $stmtDelete->close();
        }
    }

    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

    header("Location: index.php");
    exit;
}

// PENCARIAN
$keyword = trim($_GET["search"] ?? "");

// MENGAMBIL KATEGORI DAN JUMLAH ARTIKEL
$categories = [];

$sqlCategories = "SELECT
                    c.id,
                    c.name,
                    COUNT(a.id) AS articles
                  FROM article_categories c
                  LEFT JOIN articles a ON c.id = a.category_id
                  GROUP BY c.id, c.name
                  ORDER BY c.name ASC";

$resultCategories = $conn->query($sqlCategories);

if ($resultCategories) {
    while ($row = $resultCategories->fetch_assoc()) {
        $row["description"] = "";
        $row["status"] = "Aktif";

        $categories[] = $row;
    }
}

// FILTER KATEGORI
$filteredCategories = array_filter(
    $categories,
    function ($category) use ($keyword) {
        return $keyword === ""
            || stripos($category["name"], $keyword) !== false;
    }
);

// RINGKASAN
$totalCategories = count($categories);

$resultTotalArticles = $conn->query(
    "SELECT COUNT(*) AS total FROM articles"
);

$totalArticles = $resultTotalArticles
    ? (int) $resultTotalArticles->fetch_assoc()["total"]
    : 0;

// HELPER
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manajemen Kategori - SEHATIN</title>

    <link rel="stylesheet" href="../../assets/css/reset.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="../../assets/css/admin-categories.css">
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

                    <a href="../articles/index.php" class="admin-nav-link">
                        <span>▤</span>
                        Artikel
                    </a>

                    <a href="index.php" class="admin-nav-link active">
                        <span>▦</span>
                        Kategori
                    </a>

                    <a href="../facilities/" class="admin-nav-link">
                        <span>＋</span>
                        Fasilitas
                    </a>

                    <a href="../complaints/index.php" class="admin-nav-link">
                        <span>▣</span>
                        Keluhan
                    </a>

                    <a href="../users/" class="admin-nav-link">
                        <span>♙</span>
                        Pengguna
                    </a>

                </nav>

            </div>

            <!-- SIDEBAR BOTTOM -->
            <div class="admin-sidebar-bottom">

                <p><?= e($adminName) ?> · Admin</p>

                <a href="../../logout.php">
                    Keluar dari akun
                </a>

            </div>

        </aside>


        <!-- MAIN CONTENT -->
        <main class="admin-main admin-categories-main">

            <!-- HEADER -->
            <header class="categories-header">

                <div>
                    <h1>Manajemen kategori</h1>

                    <p>
                        Kelola kategori untuk mengelompokkan artikel kesehatan.
                    </p>
                </div>

                <a href="create.php" class="categories-add-button">
                    + Tambah kategori
                </a>

            </header>


            
            <!-- NOTIFICATION -->
            <?php if ($successMessage !== ""): ?>
                <div class="categories-alert">
                    <?= e($successMessage) ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage !== ""): ?>
                <div class="categories-alert">
                    <?= e($errorMessage) ?>
                </div>
            <?php endif; ?>


            <!-- SEARCH -->
            <section class="categories-search-section">

                <form action="index.php" method="GET">

                    <label for="searchCategory">
                        Cari kategori
                    </label>

                    <div class="categories-search-controls">

                        <input type="text" id="searchCategory" name="search" placeholder="Cari nama kategori"
                            value="<?= e($keyword) ?>">

                        <button type="submit">
                            Cari
                        </button>

                    </div>

                </form>

            </section>


            <!-- CATEGORY SUMMARY -->
            <section class="categories-summary">

                <div class="category-summary-card">

                    <span>Total kategori</span>

                    <h2>
                        <?= $totalCategories ?>
                    </h2>

                    <p>Kategori tersedia</p>

                </div>

                <div class="category-summary-card">

                    <span>Total artikel</span>

                    <h2>
                        <?= $totalArticles ?>
                    </h2>

                    <p>Artikel dalam kategori</p>

                </div>

            </section>


            <!-- CATEGORY TABLE -->
            <section class="categories-table-card">

                <div class="categories-table-heading">

                    <h2>Daftar kategori</h2>

                    <span>
                        <?= count($filteredCategories) ?> kategori
                    </span>

                </div>

                <div class="categories-table-wrapper">

                    <table class="categories-table">

                        <thead>

                            <tr>
                                <th>KATEGORI</th>
                                <th>DESKRIPSI</th>
                                <th>ARTIKEL</th>
                                <th>STATUS</th>
                                <th>AKSI</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (count($filteredCategories) > 0): ?>

                            <?php foreach ($filteredCategories as $category): ?>

                            <tr>

                                <td class="category-name-cell">
                                    <?= e($category['name']) ?>
                                </td>

                                <td class="category-description-cell">
                                    <?= e($category['description']) ?>
                                </td>

                                <td class="category-count-cell">
                                    <?= (int) $category['articles'] ?> artikel
                                </td>

                                <td>
                                    <span class="category-active-status">
                                        <?= e($category['status']) ?>
                                    </span>
                                </td>

                                <td>

                                    <div class="category-actions">

                                        <a href="edit.php?id=<?= (int) $category['id'] ?>" class="category-edit-button">
                                            Edit
                                        </a>

                                        <form method="POST" action="index.php"
                                            onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">

                                            <input type="hidden" name="delete_id" value="<?= (int) $category['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                            <button type="submit" class="category-delete-button">
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                            <?php endforeach; ?>

                            <?php else: ?>

                            <tr>
                                <td colspan="5" class="categories-empty">
                                    Kategori tidak ditemukan.
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