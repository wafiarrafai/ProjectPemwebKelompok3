<?php

$required_role = "user";
require_once __DIR__ . "/../../includes/auth-check.php";
require_once __DIR__ . "/../../config/database.php";

$userName = $_SESSION["name"] ?? "Pengguna";

$message = "";
$message = $_SESSION["flash_message"] ?? "";
unset($_SESSION["flash_message"]);
$category = "";
$title = "";
$description = "";

// Token keamanan untuk form
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Memproses form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $category = trim($_POST["category"] ?? "");
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $confirmation = $_POST["confirmation"] ?? "";
    $csrfToken = $_POST["csrf_token"] ?? "";

    if (
        !is_string($csrfToken) ||
        !hash_equals($_SESSION["csrf_token"], $csrfToken)
    ) {
        $message = "Permintaan tidak valid. Silakan muat ulang halaman.";

    } elseif (
        $category === "" ||
        $title === "" ||
        $description === "" ||
        $confirmation !== "yes"
    ) {
        $message = "Lengkapi semua informasi dan centang konfirmasi.";

    } elseif (strlen($title) > 150) {
        $message = "Judul maksimal 150 karakter.";

    } else {

        // Mencari ID kategori berdasarkan nama
        $categorySql = "SELECT id
                        FROM complaint_categories
                        WHERE name = ?
                        LIMIT 1";

        $categoryStmt = $conn->prepare($categorySql);

        if ($categoryStmt) {

            $categoryStmt->bind_param("s", $category);
            $categoryStmt->execute();
            $categoryStmt->bind_result($categoryId);

            if ($categoryStmt->fetch()) {

                $categoryStmt->close();

                // Menyimpan keluhan milik user yang sedang login
                $insertSql = "INSERT INTO complaints
                              (user_id, category_id, title, description)
                              VALUES (?, ?, ?, ?)";

                $insertStmt = $conn->prepare($insertSql);

                if ($insertStmt) {

                    $userId = (int) $_SESSION["user_id"];

                    $insertStmt->bind_param(
                        "iiss",
                        $userId,
                        $categoryId,
                        $title,
                        $description
                    );

                    if ($insertStmt->execute()) {
                        $_SESSION["flash_message"] = "Keluhan berhasil disimpan.";

                        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

                        header("Location: create.php");
                        exit;
                    }else {
                        $message = "Keluhan gagal disimpan. Silakan coba lagi.";
                    }

                    $insertStmt->close();

                } else {
                    $message = "Terjadi kesalahan pada sistem.";
                }

            } else {
                $categoryStmt->close();
                $message = "Kategori keluhan tidak ditemukan.";
            }

        } else {
            $message = "Terjadi kesalahan pada sistem.";
        }
    }
}

function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Buat Keluhan - SEHATIN</title>

    <link rel="stylesheet" href="../../assets/css/reset.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/user-complaints.css">
</head>

<body class="complaint-page">

    <div class="complaint-layout">

        <!-- SIDEBAR -->
        <aside class="complaint-sidebar">

            <a href="../../index.php" class="complaint-brand">
                <span class="complaint-brand-icon">S</span>
                <span>sehatin</span>
            </a>

            <div class="complaint-sidebar-menu">

                <p class="complaint-sidebar-label">
                    AKUN SAYA
                </p>

                <nav class="complaint-nav">

                    <a href="../dashboard.php" class="complaint-nav-link">
                        <span>⌂</span>
                        Ringkasan
                    </a>

                    <a href="create.php" class="complaint-nav-link active">
                        <span>＋</span>
                        Buat keluhan
                    </a>

                    <a href="history.php" class="complaint-nav-link">
                        <span>▤</span>
                        Riwayat keluhan
                    </a>

                    <a href="../profile.php" class="complaint-nav-link">
                        <span>♙</span>
                        Profil
                    </a>

                </nav>

            </div>


            <div class="complaint-sidebar-bottom">

                <p><?= e($userName) ?></p>

                <span>Akun Pengguna</span>

                <a href="../../logout.php">
                    Keluar dari akun
                </a>

            </div>

        </aside>


        <!-- MAIN CONTENT -->
        <main class="complaint-main">

            <!-- PAGE HEADER -->
            <header class="complaint-page-header">

                <div class="complaint-breadcrumb">
                    <a href="../dashboard.php">Ringkasan</a>
                    <span>/</span>
                    <span>Buat keluhan</span>
                </div>

                <h1>Buat keluhan</h1>

                <p>
                    Ceritakan keluhan secara singkat agar dapat dicatat
                    dan ditindaklanjuti.
                </p>

            </header>


            <!-- CONTENT GRID -->
            <div class="complaint-content-grid">

                <!-- FORM CARD -->
                <section class="complaint-form-card">

                    <?php if ($message !== ''): ?>

                    <div class="complaint-message">
                        <?= e($message) ?>
                    </div>

                    <?php endif; ?>


                    <form action="create.php" method="POST">
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= e($_SESSION['csrf_token']) ?>"
                        >

                        <!-- CATEGORY -->
                        <div class="complaint-form-group">

                            <label for="category">
                                Kategori keluhan
                            </label>

                            <small>
                                Pilih kategori yang paling sesuai.
                            </small>

                            <select name="category" id="category" required>

                                <option value="">Pilih kategori</option>

                                <option value="Kesehatan umum" <?= $category === 'Kesehatan umum' ? 'selected' : '' ?>>
                                    Kesehatan umum
                                </option>

                                <option value="Keluhan fisik" <?= $category === 'Keluhan fisik' ? 'selected' : '' ?>>
                                    Keluhan fisik
                                </option>

                                <option value="Kesehatan mental"
                                    <?= $category === 'Kesehatan mental' ? 'selected' : '' ?>>
                                    Kesehatan mental
                                </option>

                                <option value="Lainnya" <?= $category === 'Lainnya' ? 'selected' : '' ?>>
                                    Lainnya
                                </option>

                            </select>

                        </div>


                        <!-- TITLE -->
                        <div class="complaint-form-group">

                            <label for="title">
                                Judul singkat
                            </label>

                            <input type="text" id="title" name="title" placeholder="Contoh: kondisi yang ingin dicatat"
                                value="<?= e($title) ?>" maxlength="150" required>

                        </div>


                        <!-- DESCRIPTION -->
                        <div class="complaint-form-group">

                            <label for="description">
                                Deskripsi keluhan
                            </label>

                            <small>
                                Hindari membagikan informasi pribadi yang tidak diperlukan.
                            </small>

                            <textarea id="description" name="description" rows="5"
                                placeholder="Tulis informasi yang ingin kamu sampaikan..."
                                required><?= e($description) ?></textarea>

                        </div>


                        <!-- CONFIRMATION -->
                        <div class="complaint-confirmation">

                            <input type="checkbox" id="confirmation" name="confirmation" value="yes" required
                                <?= isset($_POST['confirmation']) && $_POST['confirmation'] === 'yes' ? 'checked' : '' ?>>

                            <label for="confirmation">
                                Informasi yang saya kirim sudah benar.
                            </label>

                        </div>


                        <!-- BUTTONS -->
                        <div class="complaint-form-actions">

                            <button type="submit" class="complaint-submit">
                                Kirim keluhan
                            </button>

                            <a href="../dashboard.php" class="complaint-cancel">
                                Batal
                            </a>

                        </div>

                    </form>

                </section>


                <!-- INFORMATION CARD -->
                <aside class="complaint-info-card">

                    <h2>Sebelum mengirim</h2>

                    <p>
                        Form ini adalah sarana pencatatan dan tindak lanjut
                        administratif. Sehatin tidak menyediakan diagnosis
                        atau saran pengobatan.
                    </p>

                    <div class="complaint-info-divider"></div>

                    <p>
                        Jika membutuhkan pertolongan segera, hubungi
                        fasilitas kesehatan atau layanan darurat setempat.
                    </p>

                </aside>

            </div>


            <!-- FOOTER -->
            <footer class="complaint-footer">

                <p>
                    © 2026 SEHATIN. Informasi kesehatan untuk semua.
                </p>

            </footer>

        </main>

    </div>

</body>

</html>