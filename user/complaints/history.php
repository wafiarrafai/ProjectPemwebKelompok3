<?php

// Memeriksa login dan role pengguna
$required_role = "user";
require_once __DIR__ . "/../../includes/auth-check.php";

// Koneksi database
require_once __DIR__ . "/../../config/database.php";

$userId = (int) $_SESSION["user_id"];

$userName = $_SESSION["name"] ?? "Pengguna";

// Mengambil keluhan milik pengguna yang sedang login
$complaints = [];

$sql = "SELECT
            c.id,
            cc.name AS category,
            c.title,
            DATE_FORMAT(c.created_at, '%d %b %Y') AS date,
            c.status
        FROM complaints c
        JOIN complaint_categories cc
            ON c.category_id = cc.id
        WHERE c.user_id = ?
        ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);

if ($stmt) {

    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $stmt->bind_result(
        $complaintId,
        $complaintCategory,
        $complaintTitle,
        $complaintDate,
        $complaintStatus
    );

    while ($stmt->fetch()) {
        $complaints[] = [
            "id" => $complaintId,
            "category" => $complaintCategory,
            "title" => $complaintTitle,
            "date" => $complaintDate,
            "status" => $complaintStatus
        ];
    }

    $stmt->close();
}

// Menghitung statistik seluruh keluhan pengguna
$total = count($complaints);
$waiting = 0;
$processing = 0;
$completed = 0;

foreach ($complaints as $complaint) {

    if ($complaint["status"] === "Menunggu") {
        $waiting++;
    } elseif ($complaint["status"] === "Diproses") {
        $processing++;
    } elseif ($complaint["status"] === "Selesai") {
        $completed++;
    }
}

// Filter berdasarkan status
$filter = $_GET["status"] ?? "Semua";

$allowedFilters = [
    "Semua",
    "Menunggu",
    "Diproses",
    "Selesai"
];

if (!in_array($filter, $allowedFilters, true)) {
    $filter = "Semua";
}

$filteredComplaints = array_filter(
    $complaints,
    function ($complaint) use ($filter) {
        return $filter === "Semua"
            || $complaint["status"] === $filter;
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

// Menentukan class status
function statusClass($status)
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

    <title>Riwayat Keluhan - SEHATIN</title>

    <link rel="stylesheet" href="../../assets/css/reset.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/user-complaints.css">
    <link rel="stylesheet" href="../../assets/css/history-complaints.css">
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

                <p class="complaint-sidebar-label">AKUN SAYA</p>

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
                    <a href="../dashboard.php">Ringkasan</a>
                    <span>/</span>
                    <span>Riwayat keluhan</span>
                </div>

                <h1>Riwayat keluhan</h1>

                <p>
                    Lihat perkembangan status dari catatanmu.
                </p>

            </header>


            <!-- STATISTICS -->
            <section class="history-statistics">

                <article class="history-stat-card">

                    <span>Semua</span>

                    <h2><?= sprintf('%02d', $total) ?></h2>

                    <p>Data tercatat</p>

                </article>

                <article class="history-stat-card">

                    <span>Menunggu</span>

                    <h2><?= sprintf('%02d', $waiting) ?></h2>

                    <p>Perlu ditinjau</p>

                </article>

                <article class="history-stat-card">

                    <span>Diproses</span>

                    <h2><?= sprintf('%02d', $processing) ?></h2>

                    <p>Dalam proses</p>

                </article>

                <article class="history-stat-card">

                    <span>Selesai</span>

                    <h2><?= sprintf('%02d', $completed) ?></h2>

                    <p>Data selesai</p>

                </article>

            </section>


            <!-- HISTORY LIST -->
            <section class="history-card">

                <div class="history-heading">

                    <h2>Catatan kamu</h2>

                    <form method="GET" action="history.php" class="history-filter">

                        <select name="status" onchange="this.form.submit()" aria-label="Filter status keluhan">

                            <option value="Semua" <?= $filter === 'Semua' ? 'selected' : '' ?>>
                                Semua status
                            </option>

                            <option value="Menunggu" <?= $filter === 'Menunggu' ? 'selected' : '' ?>>
                                Menunggu
                            </option>

                            <option value="Diproses" <?= $filter === 'Diproses' ? 'selected' : '' ?>>
                                Diproses
                            </option>

                            <option value="Selesai" <?= $filter === 'Selesai' ? 'selected' : '' ?>>
                                Selesai
                            </option>

                        </select>

                    </form>

                </div>


                <!-- TABLE HEADER -->
                <div class="history-table-header">

                    <span>Keluhan utama</span>
                    <span>Status</span>
                    <span>Aksi</span>

                </div>


                <!-- COMPLAINT ITEMS -->
                <?php if (count($filteredComplaints) > 0): ?>

                <?php foreach ($filteredComplaints as $complaint): ?>

                <div class="history-row">

                    <div class="history-information">

                        <strong>
                            <?= e($complaint['title']) ?>
                        </strong>

                        <p>
                            <?= e($complaint['category']) ?>
                            <span>·</span>
                            <?= e($complaint['date']) ?>
                        </p>

                    </div>


                    <span class="history-status <?= statusClass($complaint['status']) ?>">
                        <?= e($complaint['status']) ?>
                    </span>


                    <a href="detail.php?id=<?= (int) $complaint['id'] ?>" class="history-detail-button">
                        Detail
                    </a>

                </div>

                <?php endforeach; ?>

                <?php else: ?>

                <div class="history-empty">

                    <span class="history-empty-icon">▤</span>

                    <h3>Belum ada catatan keluhan</h3>

                    <p>
                        Belum ada keluhan dengan status yang dipilih.
                    </p>

                    <a href="create.php">
                        Buat keluhan
                    </a>

                </div>

                <?php endif; ?>

            </section>


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