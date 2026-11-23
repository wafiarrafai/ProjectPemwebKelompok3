<?php
session_start();

$userName = $_SESSION['name']
    ?? $_SESSION['username']
    ?? 'Pengguna';

$message = '';

$category = '';
$title = '';
$description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $category = trim($_POST['category'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $confirmation = $_POST['confirmation'] ?? '';

    if (
        $category !== '' &&
        $title !== '' &&
        $description !== '' &&
        $confirmation === 'yes'
    ) {
        $message = 'Form berhasil divalidasi. Penyimpanan database belum dihubungkan.';
    } else {
        $message = 'Lengkapi semua informasi dan centang konfirmasi terlebih dahulu.';
    }
}

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