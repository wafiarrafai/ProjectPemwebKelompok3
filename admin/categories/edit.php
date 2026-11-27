<?php

$required_role = "admin";
require_once __DIR__ . "/../../includes/auth-check.php";
require_once __DIR__ . "/../../config/database.php";

$adminName = $_SESSION["name"]
    ?? $_SESSION["username"]
    ?? "Admin";

$errorMessage = "";

// MENGAMBIL ID KATEGORI
$categoryId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$categoryId || $categoryId <= 0) {
    header("Location: index.php");
    exit;
}

// CSRF TOKEN
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// MENGAMBIL DATA KATEGORI
$stmtCategory = $conn->prepare(
    "SELECT id, name FROM article_categories WHERE id = ?"
);

$stmtCategory->bind_param("i", $categoryId);
$stmtCategory->execute();

$resultCategory = $stmtCategory->get_result();
$category = $resultCategory->fetch_assoc();

$stmtCategory->close();

if (!$category) {
    header("Location: index.php");
    exit;
}

// PROSES UPDATE KATEGORI
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $csrfToken = $_POST["csrf_token"] ?? "";

    if (
        !is_string($csrfToken) ||
        !hash_equals($_SESSION["csrf_token"], $csrfToken)
    ) {
        $errorMessage = "Permintaan tidak valid.";
    } elseif ($name === "") {
        $errorMessage = "Nama kategori wajib diisi.";
    } elseif (mb_strlen($name) > 100) {
        $errorMessage = "Nama kategori maksimal 100 karakter.";
    } else {

        // MEMERIKSA DUPLIKASI NAMA
        $stmtCheck = $conn->prepare(
            "SELECT id
             FROM article_categories
             WHERE name = ? AND id != ?"
        );

        $stmtCheck->bind_param("si", $name, $categoryId);
        $stmtCheck->execute();

        $resultCheck = $stmtCheck->get_result();

        if ($resultCheck->num_rows > 0) {
            $errorMessage = "Nama kategori tersebut sudah digunakan.";
        } else {

            // MEMPERBARUI KATEGORI
            $stmtUpdate = $conn->prepare(
                "UPDATE article_categories
                 SET name = ?
                 WHERE id = ?"
            );

            $stmtUpdate->bind_param("si", $name, $categoryId);

            if ($stmtUpdate->execute()) {

                $_SESSION["category_success"] =
                    "Kategori berhasil diperbarui.";

                $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

                $stmtUpdate->close();
                $stmtCheck->close();

                header("Location: index.php");
                exit;

            } else {
                $errorMessage = "Gagal memperbarui kategori.";
            }

            $stmtUpdate->close();
        }

        $stmtCheck->close();
    }

    // Mempertahankan input jika validasi gagal
    $category["name"] = $name;
}

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

    <title>Edit Kategori - SEHATIN</title>

    <link rel="stylesheet" href="../../assets/css/reset.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="../../assets/css/admin-categories.css">
    <link rel="stylesheet" href="../../assets/css/admin-record-forms.css?v=<?= filemtime(__DIR__ . '/../../assets/css/admin-record-forms.css') ?>">
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

            <div class="admin-sidebar-bottom">

                <p><?= e($adminName) ?> · Admin</p>

                <a href="../../logout.php">
                    Keluar dari akun
                </a>

            </div>

        </aside>

        <!-- MAIN CONTENT -->
        <main class="admin-main admin-categories-main admin-form-page">
            <header class="admin-form-page-header">
                <a href="index.php" class="admin-form-back">← Kembali ke daftar kategori</a>
                <span class="admin-form-kicker">PENGELOLAAN KATEGORI</span>
                <h1>Edit kategori</h1>
                <p>Perbarui nama kategori artikel kesehatan.</p>
            </header>

            <?php if ($errorMessage !== ""): ?>

                <div class="categories-alert admin-form-alert" role="alert">
                    <?= e($errorMessage) ?>
                </div>

            <?php endif; ?>

            <section class="admin-form-panel category-form-panel">
                <div class="admin-form-panel-heading">
                    <h2>Informasi kategori</h2>
                    <p>Pastikan nama kategori mudah dikenali saat mengelola artikel.</p>
                </div>

                <form method="POST" action="edit.php?id=<?= (int) $categoryId ?>" class="admin-record-form category-record-form">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($_SESSION["csrf_token"]) ?>"
                    >

                    <div class="admin-form-field category-name-field">
                        <label for="name">Nama kategori</label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            maxlength="100"
                            value="<?= e($category["name"]) ?>"
                            required
                        >
                    </div>

                    <div class="admin-form-actions">
                        <a href="index.php" class="admin-form-cancel">Batal</a>
                        <button type="submit" class="admin-form-submit">Simpan perubahan</button>
                    </div>

                </form>

            </section>

            <footer class="admin-footer">
                <p>© 2026 SEHATIN. Admin Dashboard.</p>
            </footer>

        </main>

    </div>

</body>
</html>
