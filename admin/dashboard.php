<?php
session_start();

$adminName = $_SESSION['name']
    ?? $_SESSION['username']
    ?? 'Admin';

// DATA CONTOH SEMENTARA
$totalUsers = 128;
$totalArticles = 24;
$totalFacilities = 8;
$totalComplaints = 12;

// DATA KELUHAN TERBARU
$recentComplaints = [
    [
        'name' => 'Keluhan umum - Siti R.',
        'category' => 'Kesehatan umum',
        'date' => '20 Nov 2026',
        'status' => 'Menunggu'
    ],
    [
        'name' => 'Informasi fasilitas - Budi A.',
        'category' => 'Direktori fasilitas',
        'date' => '19 Nov 2026',
        'status' => 'Diproses'
    ],
    [
        'name' => 'Keluhan umum - Rina P.',
        'category' => 'Kesehatan umum',
        'date' => '18 Nov 2026',
        'status' => 'Selesai'
    ]
];

function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function adminStatusClass($status)
{
    if ($status === 'Menunggu') {
        return 'admin-waiting';
    }

    if ($status === 'Diproses') {
        return 'admin-processing';
    }

    return 'admin-completed';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Admin - SEHATIN</title>

    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
</head>

<body class="admin-dashboard-page">

    <div class="admin-layout">

        <!-- SIDEBAR -->
        <aside class="admin-sidebar">

            <a href="dashboard.php" class="admin-brand">
                <span class="admin-brand-icon">S</span>
                <span>sehatin</span>
            </a>

            <div class="admin-menu">

                <p class="admin-menu-label">
                    PENGELOLAAN
                </p>

                <nav class="admin-nav">

                    <a href="dashboard.php" class="admin-nav-link active">
                        <span>●</span>
                        Dashboard
                    </a>

                    <a href="articles/" class="admin-nav-link">
                        <span>▤</span>
                        Artikel
                    </a>

                    <a href="categories/" class="admin-nav-link">
                        <span>▦</span>
                        Kategori
                    </a>

                    <a href="facilities/" class="admin-nav-link">
                        <span>＋</span>
                        Fasilitas
                    </a>

                    <a href="complaints/" class="admin-nav-link">
                        <span>▣</span>
                        Keluhan
                    </a>

                    <a href="users/" class="admin-nav-link">
                        <span>♙</span>
                        Pengguna
                    </a>

                </nav>

            </div>


            <!-- SIDEBAR BOTTOM -->
            <div class="admin-sidebar-bottom">

                <p><?= e($adminName) ?> · Admin</p>

                <a href="../logout.php">
                    Keluar dari akun
                </a>

            </div>

        </aside>


        <!-- MAIN -->
        <main class="admin-main">

            <!-- HEADER -->
            <header class="admin-header">

                <div>
                    <h1>Dashboard admin</h1>

                    <p>
                        Ringkasan data aplikasi · angka contoh untuk desain.
                    </p>
                </div>

                <div class="admin-date">
                    <?= date('d M Y') ?>
                </div>

            </header>


            <!-- STATISTICS -->
            <section class="admin-statistics">

                <article class="admin-stat-card">

                    <span>Total pengguna</span>

                    <h2><?= sprintf('%02d', $totalUsers) ?></h2>

                    <p>Data contoh</p>

                </article>


                <article class="admin-stat-card">

                    <span>Artikel terbit</span>

                    <h2><?= sprintf('%02d', $totalArticles) ?></h2>

                    <p>Data contoh</p>

                </article>


                <article class="admin-stat-card">

                    <span>Fasilitas</span>

                    <h2><?= sprintf('%02d', $totalFacilities) ?></h2>

                    <p>Data contoh</p>

                </article>


                <article class="admin-stat-card">

                    <span>Keluhan masuk</span>

                    <h2><?= sprintf('%02d', $totalComplaints) ?></h2>

                    <p>Data contoh</p>

                </article>

            </section>


            <!-- RECENT COMPLAINTS -->
            <section class="admin-recent-card">

                <div class="admin-section-heading">

                    <h2>Keluhan terbaru</h2>

                    <a href="complaints/">
                        Lihat semua
                    </a>

                </div>


                <!-- COMPLAINT LIST -->
                <div class="admin-complaint-list">

                    <?php foreach ($recentComplaints as $complaint): ?>

                    <article class="admin-complaint-row">

                        <div class="admin-complaint-info">

                            <strong>
                                <?= e($complaint['name']) ?>
                            </strong>

                            <p>
                                <?= e($complaint['category']) ?>

                                <span>·</span>

                                <?= e($complaint['date']) ?>
                            </p>

                        </div>


                        <span class="admin-status <?= adminStatusClass($complaint['status']) ?>">
                            <?= e($complaint['status']) ?>
                        </span>


                        <a href="complaints/" class="admin-review-button">
                            Tinjau
                        </a>

                    </article>

                    <?php endforeach; ?>

                </div>

            </section>


            <!-- QUICK MANAGEMENT -->
            <section class="admin-management-grid">

                <!-- ARTICLES -->
                <article class="admin-management-card article-management">

                    <h2>Kelola artikel</h2>

                    <p>
                        Buat, perbarui, dan terbitkan informasi umum.
                    </p>

                    <a href="articles/" class="admin-management-button">
                        Buka artikel
                    </a>

                </article>


                <!-- FACILITIES -->
                <article class="admin-management-card facility-management">

                    <h2>Kelola fasilitas</h2>

                    <p>
                        Pastikan informasi alamat dan kontak tetap jelas.
                    </p>

                    <a href="facilities/" class="admin-management-button">
                        Buka fasilitas
                    </a>

                </article>

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
