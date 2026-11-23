<?php
session_start();

$adminName = $_SESSION['name']
    ?? $_SESSION['username']
    ?? 'Admin';

// DATA KATEGORI CONTOH
if (!isset($_SESSION['admin_categories'])) {
    $_SESSION['admin_categories'] = [
        [
            'id' => 1,
            'name' => 'Kesehatan umum',
            'description' => 'Informasi kesehatan dan tips menjaga kondisi tubuh.',
            'articles' => 8,
            'status' => 'Aktif'
        ],
        [
            'id' => 2,
            'name' => 'Pola hidup',
            'description' => 'Informasi kebiasaan sehat dan gaya hidup.',
            'articles' => 6,
            'status' => 'Aktif'
        ],
        [
            'id' => 3,
            'name' => 'Keluarga',
            'description' => 'Informasi kesehatan untuk seluruh anggota keluarga.',
            'articles' => 5,
            'status' => 'Aktif'
        ],
        [
            'id' => 4,
            'name' => 'Kesehatan mental',
            'description' => 'Informasi mengenai kesehatan mental dan emosional.',
            'articles' => 3,
            'status' => 'Aktif'
        ]
    ];
}

// HAPUS KATEGORI
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['delete_id'])
) {

    $deleteId = (int) $_POST['delete_id'];

    $_SESSION['admin_categories'] = array_values(
        array_filter(
            $_SESSION['admin_categories'],
            function ($category) use ($deleteId) {
                return $category['id'] !== $deleteId;
            }
        )
    );

    header('Location: index.php?deleted=1');
    exit;
}

// PENCARIAN
$keyword = trim($_GET['search'] ?? '');

$filteredCategories = array_filter(
    $_SESSION['admin_categories'],
    function ($category) use ($keyword) {
        return $keyword === ''
            || stripos($category['name'], $keyword) !== false
            || stripos($category['description'], $keyword) !== false;
    }
);

// HELPER
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
            <?php if (isset($_GET['deleted'])): ?>

            <div class="categories-alert">
                Kategori berhasil dihapus.
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
                        <?= count($_SESSION['admin_categories']) ?>
                    </h2>

                    <p>Kategori tersedia</p>

                </div>

                <div class="category-summary-card">

                    <span>Total artikel</span>

                    <h2>
                        <?php
                        $totalArticles = 0;

                        foreach ($_SESSION['admin_categories'] as $category) {
                            $totalArticles += $category['articles'];
                        }

                        echo $totalArticles;
                        ?>
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