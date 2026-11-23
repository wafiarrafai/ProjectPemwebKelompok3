<?php
session_start();

$adminName = $_SESSION['name']
    ?? $_SESSION['username']
    ?? 'Admin';

// DATA PENGGUNA CONTOH
if (!isset($_SESSION['admin_users'])) {
    $_SESSION['admin_users'] = [
        [
            'id' => 1,
            'name' => 'Aqil',
            'email' => 'aqil@email.com',
            'joined' => '20 Nov 2026',
            'status' => 'Aktif'
        ],
        [
            'id' => 2,
            'name' => 'Siti Rahma',
            'email' => 'siti@email.com',
            'joined' => '19 Nov 2026',
            'status' => 'Aktif'
        ],
        [
            'id' => 3,
            'name' => 'Rina Putri',
            'email' => 'rina@email.com',
            'joined' => '18 Nov 2026',
            'status' => 'Aktif'
        ],
        [
            'id' => 4,
            'name' => 'Budi Santoso',
            'email' => 'budi@email.com',
            'joined' => '17 Nov 2026',
            'status' => 'Nonaktif'
        ],
        [
            'id' => 5,
            'name' => 'Dina Amelia',
            'email' => 'dina@email.com',
            'joined' => '15 Nov 2026',
            'status' => 'Aktif'
        ]
    ];
}

// HAPUS PENGGUNA
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['delete_id'])
) {
    $deleteId = (int) $_POST['delete_id'];

    $_SESSION['admin_users'] = array_values(
        array_filter(
            $_SESSION['admin_users'],
            function ($user) use ($deleteId) {
                return $user['id'] !== $deleteId;
            }
        )
    );

    header('Location: index.php?deleted=1');
    exit;
}

// PENCARIAN DAN FILTER
$keyword = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';

$filteredUsers = array_filter(
    $_SESSION['admin_users'],
    function ($user) use ($keyword, $statusFilter) {

        $matchKeyword = $keyword === ''
            || stripos($user['name'], $keyword) !== false
            || stripos($user['email'], $keyword) !== false;

        $matchStatus = $statusFilter === ''
            || $user['status'] === $statusFilter;

        return $matchKeyword && $matchStatus;
    }
);

// HELPER
function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// STATISTIK
$totalUsers = count($_SESSION['admin_users']);
$activeUsers = 0;
$inactiveUsers = 0;

foreach ($_SESSION['admin_users'] as $user) {
    if ($user['status'] === 'Aktif') {
        $activeUsers++;
    } else {
        $inactiveUsers++;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manajemen Pengguna - SEHATIN</title>

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

            <!-- SIDEBAR BOTTOM -->
            <div class="admin-sidebar-bottom">

                <p><?= e($adminName) ?> · Admin</p>

                <a href="../../logout.php">
                    Keluar dari akun
                </a>

            </div>

        </aside>


        <!-- MAIN CONTENT -->
        <main class="admin-main admin-users-main">

            <!-- HEADER -->
            <header class="admin-users-header">

                <div>
                    <h1>Manajemen pengguna</h1>

                    <p>
                        Lihat dan kelola akun pengguna yang terdaftar di SEHATIN.
                    </p>
                </div>

            </header>


            <!-- NOTIFICATION -->
            <?php if (isset($_GET['deleted'])): ?>

            <div class="admin-users-alert">
                Pengguna berhasil dihapus.
            </div>

            <?php endif; ?>


            <!-- SUMMARY -->
            <section class="admin-users-summary">

                <div class="user-summary-card">

                    <span>Total pengguna</span>

                    <h2><?= $totalUsers ?></h2>

                    <p>Seluruh akun terdaftar</p>

                </div>

                <div class="user-summary-card">

                    <span>Pengguna aktif</span>

                    <h2><?= $activeUsers ?></h2>

                    <p>Akun aktif</p>

                </div>

                <div class="user-summary-card">

                    <span>Pengguna nonaktif</span>

                    <h2><?= $inactiveUsers ?></h2>

                    <p>Akun nonaktif</p>

                </div>

            </section>


            <!-- SEARCH -->
            <section class="admin-users-search">

                <form action="index.php" method="GET">

                    <label for="searchUser">
                        Cari pengguna
                    </label>

                    <div class="admin-users-search-controls">

                        <input type="text" id="searchUser" name="search" placeholder="Nama atau email pengguna"
                            value="<?= e($keyword) ?>">

                        <select name="status">

                            <option value="">Semua status</option>

                            <option value="Aktif" <?= $statusFilter === 'Aktif' ? 'selected' : '' ?>>
                                Aktif
                            </option>

                            <option value="Nonaktif" <?= $statusFilter === 'Nonaktif' ? 'selected' : '' ?>>
                                Nonaktif
                            </option>

                        </select>

                        <button type="submit">
                            Cari
                        </button>

                    </div>

                </form>

            </section>


            <!-- USER TABLE -->
            <section class="admin-users-table-card">

                <div class="admin-users-table-heading">

                    <h2>Daftar pengguna</h2>

                    <span>
                        <?= count($filteredUsers) ?> pengguna
                    </span>

                </div>

                <div class="admin-users-table-wrapper">

                    <table class="admin-users-table">

                        <thead>

                            <tr>
                                <th>NAMA PENGGUNA</th>
                                <th>EMAIL</th>
                                <th>BERGABUNG</th>
                                <th>STATUS</th>
                                <th>AKSI</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (count($filteredUsers) > 0): ?>

                            <?php foreach ($filteredUsers as $user): ?>

                            <tr>

                                <td class="user-name-cell">

                                    <div class="user-name-wrapper">

                                        <span class="user-avatar">
                                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                        </span>

                                        <span>
                                            <?= e($user['name']) ?>
                                        </span>

                                    </div>

                                </td>

                                <td class="user-email-cell">
                                    <?= e($user['email']) ?>
                                </td>

                                <td class="user-date-cell">
                                    <?= e($user['joined']) ?>
                                </td>

                                <td>

                                    <?php if ($user['status'] === 'Aktif'): ?>

                                    <span class="user-status active">
                                        Aktif
                                    </span>

                                    <?php else: ?>

                                    <span class="user-status inactive">
                                        Nonaktif
                                    </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <div class="admin-user-actions">

                                        <a href="detail.php?id=<?= (int) $user['id'] ?>" class="user-detail-button">
                                            Detail
                                        </a>

                                        <form action="index.php" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">

                                            <input type="hidden" name="delete_id" value="<?= (int) $user['id'] ?>">

                                            <button type="submit" class="user-delete-button">
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                            <?php endforeach; ?>

                            <?php else: ?>

                            <tr>

                                <td colspan="5" class="admin-users-empty">
                                    Pengguna tidak ditemukan.
                                    Coba kata kunci lain.
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