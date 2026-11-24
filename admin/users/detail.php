
<?php

$required_role = "admin";
require_once __DIR__ . "/../../includes/auth-check.php";
require_once __DIR__ . "/../../config/database.php";

$adminName = $_SESSION['name'] ?? 'Admin';

// Ambil ID pengguna dari URL
$userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$userId || $userId <= 0) {
    header("Location: index.php");
    exit;
}

// Ambil detail pengguna
$stmt = $conn->prepare(
    "SELECT id, name, email, role, created_at
     FROM users
     WHERE id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $userId);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

// Jika pengguna tidak ditemukan
if (!$user) {
    header("Location: index.php");
    exit;
}

// Ambil riwayat keluhan pengguna
$stmt = $conn->prepare(
    "SELECT c.id, c.title, c.status, c.created_at,
            cc.name AS category_name
     FROM complaints c
     LEFT JOIN complaint_categories cc
        ON c.category_id = cc.id
     WHERE c.user_id = ?
     ORDER BY c.created_at DESC"
);

$stmt->bind_param("i", $userId);
$stmt->execute();

$resultComplaints = $stmt->get_result();

$complaints = [];

while ($row = $resultComplaints->fetch_assoc()) {
    $complaints[] = $row;
}

$stmt->close();

// Fungsi keamanan output
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Pengguna - SEHATIN</title>

    <link rel="stylesheet" href="../../assets/css/reset.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="../../assets/css/admin-users.css">
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

                    <a href="../categories/index.php" class="admin-nav-link">
                        <span>▦</span>
                        Kategori
                    </a>

                    <a href="../facilities/index.php" class="admin-nav-link">
                        <span>＋</span>
                        Fasilitas
                    </a>

                    <a href="../complaints/index.php" class="admin-nav-link">
                        <span>▣</span>
                        Keluhan
                    </a>

                    <a href="index.php" class="admin-nav-link active">
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
        <main class="admin-main admin-users-main">

            <header class="admin-users-header">

                <div>
                    <h1>Detail pengguna</h1>
                    <p>Informasi akun dan riwayat keluhan pengguna SEHATIN.</p>
                </div>

            </header>

            <!-- INFORMASI PENGGUNA -->
            <section class="admin-users-summary">

                <div class="user-summary-card">

                    <span>Nama pengguna</span>
                    <h2><?= e($user['name']) ?></h2>

                    <p>ID akun: <?= (int) $user['id'] ?></p>

                </div>

                <div class="user-summary-card">

                    <span>Email</span>
                    <h2><?= e($user['email']) ?></h2>

                    <p>Alamat email akun</p>

                </div>

                <div class="user-summary-card">

                    <span>Jenis akun</span>

                    <h2>
                        <?= $user['role'] === 'admin' ? 'Admin' : 'Pengguna' ?>
                    </h2>

                    <p>
                        Terdaftar sejak
                        <?= e(date('d M Y', strtotime($user['created_at']))) ?>
                    </p>

                </div>

            </section>

            <!-- RIWAYAT KELUHAN -->
            <section class="admin-users-table-card">

                <div class="admin-users-table-heading">

                    <h2>Riwayat keluhan</h2>

                    <span>
                        <?= count($complaints) ?> keluhan
                    </span>

                </div>

                <div class="admin-users-table-wrapper">

                    <table class="admin-users-table">

                        <thead>
                            <tr>
                                <th>JUDUL KELUHAN</th>
                                <th>KATEGORI</th>
                                <th>STATUS</th>
                                <th>TANGGAL</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (count($complaints) > 0): ?>

                                <?php foreach ($complaints as $complaint): ?>

                                    <tr>

                                        <td>
                                            <?= e($complaint['title']) ?>
                                        </td>

                                        <td>
                                            <?= e($complaint['category_name'] ?? 'Tidak berkategori') ?>
                                        </td>

                                        <td>
                                            <?= e($complaint['status']) ?>
                                        </td>

                                        <td class="user-date-cell">
                                            <?= e(date('d M Y', strtotime($complaint['created_at']))) ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="4" class="admin-users-empty">
                                        Pengguna ini belum memiliki keluhan.
                                    </td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

            <a href="index.php" class="user-detail-button">
                Kembali ke daftar pengguna
            </a>

            <footer class="admin-footer">
                <p>© 2026 SEHATIN. Admin Dashboard.</p>
            </footer>

        </main>

    </div>

</body>

</html>
