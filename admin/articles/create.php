<?php

$required_role = "admin";
require_once __DIR__ . "/../../includes/auth-check.php";
require_once __DIR__ . "/../../config/database.php";

$adminName = $_SESSION["name"]
    ?? $_SESSION["username"]
    ?? "Admin";

$adminId = (int) $_SESSION["user_id"];

$successMessage = "";
$errorMessage = "";

$title = "";
$category = "";
$summary = "";
$content = "";
$status = "Draf";

// Token keamanan form
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
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

// PROSES FORM
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $summary = trim($_POST["summary"] ?? "");
    $content = trim($_POST["content"] ?? "");

    $action = $_POST["action"] ?? "draft";

    $status = $action === "publish" ? "Terbit" : "Draf";

    $csrfToken = $_POST["csrf_token"] ?? "";

    if (
        !hash_equals($_SESSION["csrf_token"], $csrfToken)
    ) {
        $errorMessage = "Permintaan tidak valid. Silakan muat ulang halaman.";
    } elseif (
        $title === "" ||
        $category === "" ||
        $summary === "" ||
        $content === ""
    ) {
        $errorMessage = "Semua kolom wajib diisi sebelum menyimpan artikel.";
    } elseif (!in_array($action, ["draft", "publish"], true)) {
        $errorMessage = "Aksi penyimpanan tidak valid.";
    } else {

        // Memastikan kategori benar-benar tersedia
        $categoryId = filter_var($category, FILTER_VALIDATE_INT);

        $categoryExists = false;

        if ($categoryId && $categoryId > 0) {
            $checkCategory = $conn->prepare(
                "SELECT id FROM article_categories WHERE id = ?"
            );

            if ($checkCategory) {
                $checkCategory->bind_param("i", $categoryId);
                $checkCategory->execute();
                $checkCategory->store_result();

                $categoryExists = $checkCategory->num_rows > 0;

                $checkCategory->close();
            }
        }

        if (!$categoryExists) {
            $errorMessage = "Kategori artikel tidak valid.";
        } else {

            // Menentukan status database
            $dbStatus = $action === "publish"
                ? "published"
                : "draft";

            // Menyimpan artikel ke database
            $sqlInsert = "INSERT INTO articles
                            (category_id, author_id, title, excerpt, content, status)
                          VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sqlInsert);

            if ($stmt) {

                $stmt->bind_param(
                    "iissss",
                    $categoryId,
                    $adminId,
                    $title,
                    $summary,
                    $content,
                    $dbStatus
                );

                if ($stmt->execute()) {

                    $_SESSION["article_success"] =
                        $dbStatus === "published"
                        ? "Artikel berhasil diterbitkan!"
                        : "Artikel berhasil disimpan sebagai draf!";

                    // Membuat token baru
                    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

                    $stmt->close();

                    header("Location: index.php");
                    exit;

                } else {
                    $errorMessage = "Artikel gagal disimpan. Silakan coba kembali.";
                }

                $stmt->close();

            } else {
                $errorMessage = "Terjadi kesalahan saat menyiapkan penyimpanan artikel.";
            }
        }
    }
}

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

    <title>Tulis Artikel - SEHATIN</title>

    <link rel="stylesheet" href="../../assets/css/reset.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="../../assets/css/admin-article-form.css">
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
        <main class="admin-main admin-article-form-main">

            <!-- BREADCRUMB -->
            <div class="article-form-breadcrumb">

                <a href="index.php">Artikel</a>

                <span>/</span>

                <span>Tambah artikel</span>

            </div>


            <!-- PAGE HEADER -->
            <header class="article-form-header">

                <h1>Tulis artikel</h1>

                <div class="article-form-actions">

                    <button type="submit" form="articleForm" name="action" value="draft" class="article-draft-button">
                        Simpan draf
                    </button>

                    <button type="submit" form="articleForm" name="action" value="publish"
                        class="article-publish-button">
                        Terbitkan
                    </button>

                </div>

            </header>


            <!-- NOTIFICATION -->
            <?php if ($errorMessage): ?>

            <div class="article-form-alert">
                <?= e($errorMessage) ?>
            </div>

            <?php endif; ?>


            <!-- FORM GRID -->
            <div class="article-form-grid">


                <!-- LEFT: ARTICLE FORM -->
                <section class="article-editor-card">

                    <form action="create.php" method="POST" id="articleForm">
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e($_SESSION['csrf_token']) ?>"
                        >

                        <!-- TITLE -->
                        <div class="article-form-group">

                            <label for="articleTitle">
                                Judul artikel
                            </label>

                            <input type="text" id="articleTitle" name="title" placeholder="Tulis judul yang jelas"
                                value="<?= e($title) ?>" required>

                        </div>


                        <!-- CATEGORY -->
                        <div class="article-form-group">

                            <label for="articleCategory">
                                Kategori
                            </label>
                            
                            <select id="articleCategory" name="category" required>

                                <option value="">Pilih kategori</option>

                                <?php foreach ($categories as $item): ?>

                                    <option
                                        value="<?= (int) $item["id"] ?>"
                                        <?= $category == $item["id"] ? "selected" : "" ?>
                                    >
                                        <?= e($item["name"]) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- SUMMARY -->
                        <div class="article-form-group">

                            <label for="articleSummary">
                                Ringkasan
                            </label>

                            <textarea id="articleSummary" name="summary" rows="3"
                                placeholder="Ringkasan singkat untuk kartu artikel"
                                required><?= e($summary) ?></textarea>

                        </div>


                        <!-- CONTENT -->
                        <div class="article-form-group">

                            <label for="articleContent">
                                Isi artikel
                            </label>

                            <p class="article-form-helper">
                                Gunakan paragraf dan subjudul agar mudah dibaca.
                            </p>

                            <textarea id="articleContent" name="content" rows="6"
                                placeholder="Tulis konten artikel di sini." required><?= e($content) ?></textarea>

                        </div>

                    </form>

                </section>


                <!-- RIGHT: PUBLICATION SETTINGS -->
                <aside class="article-publication-card">

                    <h2>Pengaturan publikasi</h2>


                    <div class="publication-setting">

                        <label>Status</label>

                        <span class="publication-status">
                            Draf
                        </span>

                    </div>


                    <div class="publication-divider"></div>


                    <div class="publication-setting">

                        <label>Penulis</label>

                        <p>
                            <?= e($adminName) ?> · Admin
                        </p>

                    </div>


                    <p class="publication-description">
                        Artikel hanya tampil untuk publik setelah diterbitkan.
                    </p>

                </aside>

            </div>


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