<?php

$required_role = "admin";
require_once __DIR__ . "/../../includes/auth-check.php";
require_once __DIR__ . "/../../config/database.php";

$adminName = $_SESSION["name"]
    ?? $_SESSION["username"]
    ?? "Admin";

// AMBIL DATA KELUHAN DARI DATABASE
$complaints = [];

$sql = "SELECT
            c.id,
            c.title,
            u.name AS user_name,
            cc.name AS category,
            DATE_FORMAT(c.created_at, '%d %b %Y') AS date,
            c.status,
            c.description
        FROM complaints c
        JOIN users u
            ON c.user_id = u.id
        JOIN complaint_categories cc
            ON c.category_id = cc.id
        ORDER BY c.created_at DESC";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->execute();

    $stmt->bind_result(
        $complaintId,
        $complaintTitle,
        $complaintUser,
        $complaintCategory,
        $complaintDate,
        $complaintStatus,
        $complaintDescription
    );

    while ($stmt->fetch()) {
        $complaints[] = [
            "id" => $complaintId,
            "title" => $complaintTitle,
            "user" => $complaintUser,
            "category" => $complaintCategory,
            "date" => $complaintDate,
            "status" => $complaintStatus,
            "description" => $complaintDescription
        ];
    }

    $stmt->close();
}

// AMBIL KATEGORI UNIK DARI DATA KELUHAN
$categories = [];

foreach ($complaints as $complaint) {
    if (!in_array($complaint["category"], $categories, true)) {
        $categories[] = $complaint["category"];
    }
}

// FILTER PENCARIAN
$keyword = trim($_GET["search"] ?? "");
$statusFilter = $_GET["status"] ?? "";
$categoryFilter = $_GET["category"] ?? "";

// VALIDASI FILTER STATUS
$allowedStatuses = ["", "Menunggu", "Diproses", "Selesai"];

if (!in_array($statusFilter, $allowedStatuses, true)) {
    $statusFilter = "";
}

// VALIDASI FILTER KATEGORI
if (
    $categoryFilter !== "" &&
    !in_array($categoryFilter, $categories, true)
) {
    $categoryFilter = "";
}

// TERAPKAN FILTER
$filteredComplaints = array_filter(
    $complaints,
    function ($complaint) use (
        $keyword,
        $statusFilter,
        $categoryFilter
    ) {
        $matchKeyword = $keyword === ""
            || stripos($complaint["title"], $keyword) !== false
            || stripos($complaint["user"], $keyword) !== false;

        $matchStatus = $statusFilter === ""
            || $complaint["status"] === $statusFilter;

        $matchCategory = $categoryFilter === ""
            || $complaint["category"] === $categoryFilter;

        return $matchKeyword && $matchStatus && $matchCategory;
    }
);

// DOWNLOAD DATA CSV
if (isset($_GET["download"])) {

    header("Content-Type: text/csv; charset=UTF-8");
    header('Content-Disposition: attachment; filename="data-keluhan-sehatin.csv"');

    $output = fopen("php://output", "w");

    // BOM agar karakter Indonesia terbaca di Excel
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    fputcsv($output, [
        "ID",
        "Judul Keluhan",
        "Nama Pengguna",
        "Kategori",
        "Tanggal",
        "Status",
        "Deskripsi"
    ]);

    foreach ($filteredComplaints as $complaint) {
        fputcsv($output, [
            $complaint["id"],
            $complaint["title"],
            $complaint["user"],
            $complaint["category"],
            $complaint["date"],
            $complaint["status"],
            $complaint["description"]
        ]);
    }

    fclose($output);
    exit;
}

// HELPER
function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

// URL DOWNLOAD MEMPERTAHANKAN FILTER
$downloadParams = $_GET;
$downloadParams["download"] = "1";
$downloadUrl = "index.php?" . http_build_query($downloadParams);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Keluhan Masuk - SEHATIN</title>

    <link rel="stylesheet" href="../../assets/css/reset.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="../../assets/css/admin-complaints.css">
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

                    <a href="../categories/" class="admin-nav-link">
                        <span>▦</span>
                        Kategori
                    </a>

                    <a href="../facilities/" class="admin-nav-link">
                        <span>＋</span>
                        Fasilitas
                    </a>

                    <a href="index.php" class="admin-nav-link active">
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
        <main class="admin-main admin-complaints-main">

            <!-- HEADER -->
            <header class="complaints-header">

                <div>
                    <h1>Keluhan masuk</h1>

                    <p>
                        Tinjau keluhan dan perbarui status tindak lanjut administratif.
                    </p>
                </div>

                <a href="<?= e($downloadUrl) ?>" class="complaints-download-button">
                    Unduh data
                </a>

            </header>


            <!-- SEARCH AND FILTER -->
            <section class="complaints-search-section">

                <form action="index.php" method="GET">

                    <label for="searchComplaint">
                        Cari keluhan
                    </label>

                    <div class="complaints-search-controls">

                        <input type="text" id="searchComplaint" name="search" placeholder="Judul atau nama pengguna"
                            value="<?= e($keyword) ?>">

                        <select name="status">

                            <option value="">Semua status</option>

                            <option value="Menunggu" <?= $statusFilter === 'Menunggu' ? 'selected' : '' ?>>
                                Menunggu
                            </option>

                            <option value="Diproses" <?= $statusFilter === 'Diproses' ? 'selected' : '' ?>>
                                Diproses
                            </option>

                            <option value="Selesai" <?= $statusFilter === 'Selesai' ? 'selected' : '' ?>>
                                Selesai
                            </option>

                        </select>

                        
                        <select name="category">

                            <option value="">Semua kategori</option>

                            <?php foreach ($categories as $category): ?>

                                <option value="<?= e($category) ?>"
                                    <?= $categoryFilter === $category ? "selected" : "" ?>>
                                    <?= e($category) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                        <button type="submit" class="complaints-search-button">
                            Cari
                        </button>

                    </div>

                </form>

            </section>


            <!-- COMPLAINT TABLE -->
            <section class="complaints-table-card">

                <div class="complaints-table-wrapper">

                    <table class="complaints-table">

                        <thead>

                            <tr>
                                <th>KELUHAN</th>
                                <th>KATEGORI</th>
                                <th>TANGGAL</th>
                                <th>STATUS</th>
                                <th>AKSI</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (count($filteredComplaints) > 0): ?>

                            <?php foreach ($filteredComplaints as $complaint): ?>

                            <tr>

                                <td class="complaint-title-cell">

                                    <strong>
                                        <?= e($complaint['title']) ?>
                                    </strong>

                                    <small>
                                        <?= e($complaint['category']) ?>
                                    </small>

                                </td>

                                <td class="complaint-category-cell">
                                    <?= e($complaint['category']) ?>
                                </td>

                                <td class="complaint-date-cell">
                                    <?= e($complaint['date']) ?>
                                </td>

                                <td>

                                    <?php
                                            $statusClass = match ($complaint['status']) {
                                                'Menunggu' => 'waiting',
                                                'Diproses' => 'processing',
                                                'Selesai' => 'completed',
                                                default => ''
                                            };
                                            ?>

                                    <span class="complaint-status <?= $statusClass ?>">
                                        <?= e($complaint['status']) ?>
                                    </span>

                                </td>

                                <td>

                                    <a href="detail.php?id=<?= (int) $complaint['id'] ?>"
                                        class="complaint-review-button">
                                        Tinjau
                                    </a>

                                </td>

                            </tr>

                            <?php endforeach; ?>

                            <?php else: ?>

                            <tr>

                                <td colspan="5" class="complaints-empty">

                                    Keluhan tidak ditemukan.
                                    Coba kata kunci atau filter lain.

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