<?php

$required_role = "user";
require_once __DIR__ . "/../includes/auth-check.php";
require_once __DIR__ . "/../config/database.php";

$userId = (int) $_SESSION["user_id"];

$userName = $_SESSION["name"]
    ?? $_SESSION["username"]
    ?? "Pengguna";

// NILAI AWAL STATISTIK
$totalComplaints = 0;
$pendingComplaints = 0;
$completedComplaints = 0;

// MENGAMBIL STATISTIK KELUHAN PENGGUNA
$sqlStats = "SELECT
    COUNT(*) AS total,
    SUM(status IN ('Menunggu', 'Diproses')) AS pending,
    SUM(status = 'Selesai') AS completed
FROM complaints
WHERE user_id = ?";

$stmtStats = $conn->prepare($sqlStats);

if ($stmtStats) {

    $stmtStats->bind_param("i", $userId);
    $stmtStats->execute();

    $stmtStats->bind_result(
        $total,
        $pending,
        $completed
    );

    if ($stmtStats->fetch()) {
        $totalComplaints = (int) $total;
        $pendingComplaints = (int) $pending;
        $completedComplaints = (int) $completed;
    }

    $stmtStats->close();
}

// MENGAMBIL 5 KELUHAN TERBARU MILIK PENGGUNA
$recentComplaints = [];

$sqlRecent = "SELECT
    c.id,
    c.title,
    cc.name AS category,
    DATE_FORMAT(c.created_at, '%d %b %Y') AS date,
    c.status
FROM complaints c
JOIN complaint_categories cc
    ON c.category_id = cc.id
WHERE c.user_id = ?
ORDER BY c.created_at DESC
LIMIT 5";

$stmtRecent = $conn->prepare($sqlRecent);

if ($stmtRecent) {

    $stmtRecent->bind_param("i", $userId);
    $stmtRecent->execute();

    $stmtRecent->bind_result(
        $complaintId,
        $complaintTitle,
        $complaintCategory,
        $complaintDate,
        $complaintStatus
    );

    while ($stmtRecent->fetch()) {
        $recentComplaints[] = [
            "id" => $complaintId,
            "title" => $complaintTitle,
            "category" => $complaintCategory,
            "date" => $complaintDate,
            "status" => $complaintStatus
        ];
    }

    $stmtRecent->close();
}

// FUNGSI KEAMANAN OUTPUT
function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

// MENENTUKAN CLASS STATUS
function complaintStatusClass($status)
{
    if ($status === "Menunggu") {
        return "waiting";
    }

    if ($status === "Diproses") {
        return "processing";
    }

    return "completed";
}

?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Pengguna - SEHATIN</title>

    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/user-dashboard.css">
</head>

<body class="user-dashboard">

    <div class="dashboard-layout">

        <!-- SIDEBAR -->
        <aside class="user-sidebar">

            <a href="../index.php" class="user-brand">
                <span class="user-brand-icon">S</span>
                <span>sehatin</span>
            </a>


            <!-- MENU -->
            <div class="sidebar-section">

                <p class="sidebar-label">AKUN SAYA</p>

                <nav class="sidebar-menu">

                    <a href="dashboard.php" class="sidebar-link active">
                        <span class="menu-icon">⌂</span>
                        Ringkasan
                    </a>

                    <a href="#buat-keluhan" class="sidebar-link">
                        <span class="menu-icon">＋</span>
                        Buat keluhan
                    </a>

                    <a href="#riwayat-keluhan" class="sidebar-link">
                        <span class="menu-icon">▤</span>
                        Riwayat keluhan
                    </a>

                    <a href="profile.php" class="sidebar-link">
                        <span class="menu-icon">♙</span>
                        Profil
                    </a>

                </nav>

            </div>


            <!-- SIDEBAR BOTTOM -->
            <div class="sidebar-bottom">

                <p class="sidebar-user-name">
                    <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>
                </p>

                <span class="sidebar-user-role">Akun Pengguna</span>

                <a href="../logout.php" class="sidebar-logout">
                    Keluar dari akun
                </a>

            </div>

        </aside>


        <!-- MAIN CONTENT -->
        <main class="dashboard-main">

            <!-- TOP HEADER -->
            <header class="dashboard-header">

                <div class="dashboard-welcome">

                    <h1>
                        Halo, <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>!
                    </h1>

                    <p>
                        Ringkasan aktivitasmu di Sehatin.
                    </p>

                </div>

                <a href="#buat-keluhan" class="header-action">
                    Buat keluhan
                </a>

            </header>


            <!-- WELCOME BANNER -->
            <section class="dashboard-banner">

                <div class="banner-content">

                    <span class="banner-label">
                        CATATAN KESEHATAN
                    </span>

                    <h2>
                        Simpan keluhanmu, pantau statusnya dengan mudah.
                    </h2>

                    <p>
                        Pencatatan ini bersifat administratif, bukan diagnosis.
                    </p>

                </div>

                <a href="#buat-keluhan" class="banner-button">
                    Tulis keluhan
                </a>

            </section>


            <!-- STATISTICS -->
            <section class="dashboard-statistics">

                <article class="stat-card">

                    <span class="stat-label">
                        Total keluhan
                    </span>

                    <h2>
                        <?= sprintf('%02d', $totalComplaints) ?>
                    </h2>

                    <p>Data tersimpan</p>

                </article>


                <article class="stat-card">

                    <span class="stat-label">
                        Menunggu
                    </span>

                    <h2>
                        <?= sprintf('%02d', $pendingComplaints) ?>
                    </h2>

                    <p>Perlu ditinjau</p>

                </article>


                <article class="stat-card">

                    <span class="stat-label">
                        Selesai
                    </span>

                    <h2>
                        <?= sprintf('%02d', $completedComplaints) ?>
                    </h2>

                    <p>Data selesai</p>

                </article>

            </section>


            <!-- RECENT COMPLAINTS -->
            <section class="recent-complaints" id="riwayat-keluhan">

                <div class="section-heading">

                    <h2>Keluhan terbaru</h2>

                    <a href="#riwayat-keluhan">
                        Lihat semua
                    </a>

                </div>


                <div class="complaint-table">

                    <div class="complaint-table-header">
                        <span>Keluhan</span>
                        <span>Status</span>
                        <span>Aksi</span>
                    </div>


                    <?php if (empty($recentComplaints)): ?>
                        
                        <p>Belum ada keluhan yang dicatat.</p>

                    <?php else: ?>

                        <?php foreach ($recentComplaints as $complaint): ?>

                            <div class="complaint-row">

                                <div class="complaint-information">

                                    <strong>
                                        <?= e($complaint['title']) ?>
                                    </strong>

                                    <p>
                                        <?= e($complaint['category']) ?>
                                        <span>·</span>
                                        <?= e($complaint['date']) ?>
                                    </p>

                                </div>

                                <span class="complaint-status <?= e(complaintStatusClass($complaint['status'])) ?>">
                                    <?= e($complaint['status']) ?>
                                </span>

                                <a
                                    href="complaints/detail.php?id=<?= (int) $complaint['id'] ?>"
                                    class="complaint-detail"
                                >
                                    Detail
                                </a>

                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            </section>


            <!-- QUICK ACTION -->
            <section class="dashboard-quick-action" id="buat-keluhan">

                <div>
                    <h2>Ingin mencatat keluhan baru?</h2>
                    <p>
                        Siapkan informasi keluhanmu untuk dicatat.
                    </p>
                </div>

                <a href="#buat-keluhan" class="quick-action-button">
                    Buat keluhan
                </a>

            </section>


            <!-- FOOTER -->
            <footer class="dashboard-footer">

                <p>
                    © 2026 SEHATIN. Informasi kesehatan untuk semua.
                </p>

            </footer>

        </main>

    </div>

</body>

</html>
