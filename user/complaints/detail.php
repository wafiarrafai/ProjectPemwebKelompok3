<?php
session_start();

$userName = $_SESSION['name']
    ?? $_SESSION['username']
    ?? 'Pengguna';

// DATA CONTOH SEMENTARA
$complaints = [
    1 => [
        'title' => 'Keluhan umum',
        'category' => 'Kesehatan umum',
        'date' => '18 Nov 2026',
        'status' => 'Diproses',
        'description' => 'Saya ingin mencatat keluhan dan meminta informasi mengenai langkah administrasi berikutnya.',
        'note' => 'Catatan sedang ditinjau oleh pengelola. Informasi ini bersifat administratif.'
    ],

    2 => [
        'title' => 'Catatan kesehatan',
        'category' => 'Kesehatan mental',
        'date' => '15 Nov 2026',
        'status' => 'Menunggu',
        'description' => 'Saya ingin menyampaikan catatan kesehatan saya.',
        'note' => 'Keluhan telah diterima dan menunggu peninjauan.'
    ],

    3 => [
        'title' => 'Keluhan ringan',
        'category' => 'Kesehatan umum',
        'date' => '11 Nov 2026',
        'status' => 'Selesai',
        'description' => 'Saya ingin mencatat informasi mengenai keluhan saya.',
        'note' => 'Catatan telah selesai ditindaklanjuti.'
    ],

    4 => [
        'title' => 'Catatan pemeriksaan',
        'category' => 'Keluhan fisik',
        'date' => '08 Nov 2026',
        'status' => 'Menunggu',
        'description' => 'Saya ingin mencatat informasi mengenai pemeriksaan.',
        'note' => 'Catatan menunggu proses peninjauan.'
    ]
];

// AMBIL ID KELUHAN
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || !isset($complaints[$id])) {
    http_response_code(404);
    $complaint = null;
} else {
    $complaint = $complaints[$id];
}

// ESCAPE HTML
function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// STATUS
$status = $complaint['status'] ?? '';

function detailStatusClass($status)
{
    if ($status === 'Menunggu') {
        return 'waiting';
    }

    if ($status === 'Diproses') {
        return 'processing';
    }

    return 'completed';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Keluhan - SEHATIN</title>

    <link rel="stylesheet" href="../../assets/css/reset.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/user-complaints.css">
    <link rel="stylesheet" href="../../assets/css/detail.css">
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

                    <a href="create.php" class="complaint-nav-link">
                        <span>＋</span>
                        Buat keluhan
                    </a>

                    <a href="history.php" class="complaint-nav-link active">
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

            <!-- HEADER -->
            <header class="complaint-page-header">

                <div class="complaint-breadcrumb">

                    <a href="history.php">Riwayat keluhan</a>

                    <span>/</span>

                    <span>Detail</span>

                </div>

                <h1>Detail keluhan</h1>

            </header>


            <?php if (!$complaint): ?>

                <!-- NOT FOUND -->
                <section class="detail-not-found">

                    <h2>Keluhan tidak ditemukan</h2>

                    <p>
                        Data keluhan yang kamu cari tidak tersedia.
                    </p>

                    <a href="history.php" class="detail-back-button">
                        Kembali ke riwayat
                    </a>

                </section>

            <?php else: ?>


                <!-- DETAIL CONTENT -->
                <div class="detail-content-grid">

                    <!-- COMPLAINT INFORMATION -->
                    <section class="detail-complaint-card">

                        <div class="detail-card-heading">

                            <h2>
                                <?= e($complaint['title']) ?>
                            </h2>

                            <span class="history-status <?= detailStatusClass($status) ?>">
                                <?= e($status) ?>
                            </span>

                        </div>


                        <!-- DATE AND CATEGORY -->
                        <p class="detail-metadata">

                            Dikirim <?= e($complaint['date']) ?>

                            <span>·</span>

                            Kategori: <?= e($complaint['category']) ?>

                        </p>


                        <!-- DESCRIPTION -->
                        <div class="detail-section">

                            <h3>Deskripsi</h3>

                            <p>
                                <?= e($complaint['description']) ?>
                            </p>

                        </div>


                        <!-- FOLLOW UP NOTE -->
                        <div class="detail-followup">

                            <h3>Catatan tindak lanjut</h3>

                            <p>
                                <?= e($complaint['note']) ?>
                            </p>

                        </div>


                        <!-- BACK BUTTON -->
                        <div class="detail-actions">

                            <a href="history.php" class="detail-back-button">
                                Kembali ke riwayat
                            </a>

                        </div>

                    </section>


                    <!-- STATUS PROGRESS -->
                    <aside class="detail-progress-card">

                        <h2>Perkembangan status</h2>


                        <!-- WAITING -->
                        <div class="detail-progress-item">

                            <span class="history-status waiting">
                                Menunggu
                            </span>

                            <p>
                                Keluhan diterima · <?= e($complaint['date']) ?>
                            </p>

                        </div>


                        <!-- PROCESSING -->
                        <div class="detail-progress-item">

                            <span class="history-status processing">
                                Diproses
                            </span>

                            <p>
                                Sedang ditindaklanjuti
                            </p>

                        </div>


                        <!-- COMPLETED -->
                        <div class="detail-progress-item">

                            <span class="history-status completed">
                                Selesai
                            </span>

                            <p>
                                <?= $status === 'Selesai'
                                    ? 'Keluhan telah selesai'
                                    : 'Belum selesai' ?>
                            </p>

                        </div>

                    </aside>

                </div>

            <?php endif; ?>


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