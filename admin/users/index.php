<?php

$required_role = "admin";
require_once __DIR__ . "/../../includes/auth-check.php";
require_once __DIR__ . "/../../config/database.php";

$adminName = $_SESSION['name']
    ?? $_SESSION['username']
    ?? 'Admin';

// CSRF TOKEN
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// NOTIFIKASI
$successMessage = $_SESSION['user_success'] ?? '';
$errorMessage = $_SESSION['user_error'] ?? '';

unset($_SESSION['user_success'], $_SESSION['user_error']);

// HAPUS PENGGUNA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {

    $csrfToken = $_POST['csrf_token'] ?? '';
    $deleteId = filter_var($_POST['delete_id'], FILTER_VALIDATE_INT);

    if (
        !hash_equals($_SESSION['csrf_token'], $csrfToken)
        || !$deleteId
        || $deleteId <= 0
    ) {
        $_SESSION['user_error'] = "Permintaan tidak valid.";
    } elseif ($deleteId === (int) $_SESSION['user_id']) {
        $_SESSION['user_error'] = "Akun yang sedang digunakan tidak dapat dihapus.";
    } else {

        // Hanya akun dengan role user yang boleh dihapus dari halaman ini.
        $stmt = $conn->prepare(
            "DELETE FROM users WHERE id = ? AND role = 'user'"
        );

        if ($stmt) {
            $stmt->bind_param("i", $deleteId);

            try {
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $_SESSION['user_success'] = "Pengguna berhasil dihapus.";
                } else {
                    $_SESSION['user_error'] = "Pengguna tidak ditemukan atau tidak dapat dihapus.";
                }
            } catch (mysqli_sql_exception $e) {
                $_SESSION['user_error'] = "Pengguna tidak dapat dihapus karena masih memiliki data terkait.";
            }

            $stmt->close();
        } else {
            $_SESSION['user_error'] = "Terjadi kesalahan saat menyiapkan penghapusan.";
        }
    }

    header("Location: index.php");
    exit;
}

// PENCARIAN DAN FILTER ROLE
$keyword = trim($_GET['search'] ?? '');
$roleFilter = $_GET['role'] ?? '';

$allowedRoles = ['', 'user', 'admin'];

if (!in_array($roleFilter, $allowedRoles, true)) {
    $roleFilter = '';
}

// AMBIL DATA PENGGUNA
$users = [];

$sqlUsers = "SELECT id, name, email, role, created_at
             FROM users
             ORDER BY created_at DESC";

$resultUsers = $conn->query($sqlUsers);

if ($resultUsers) {
    while ($row = $resultUsers->fetch_assoc()) {
        $users[] = $row;
    }
}

// FILTER DATA
$filteredUsers = array_filter(
    $users,
    function ($user) use ($keyword, $roleFilter) {

        $matchKeyword = $keyword === ''
            || stripos($user['name'], $keyword) !== false
            || stripos($user['email'], $keyword) !== false;

        $matchRole = $roleFilter === ''
            || $user['role'] === $roleFilter;

        return $matchKeyword && $matchRole;
    }
);

// FUNGSI KEAMANAN OUTPUT
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// STATISTIK
$totalUsers = count($users);
$regularUsers = 0;
$adminUsers = 0;

foreach ($users as $user) {
    if ($user['role'] === 'user') {
        $regularUsers++;
    } elseif ($user['role'] === 'admin') {
        $adminUsers++;
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
            <?php if ($successMessage): ?>
                <div class="admin-users-alert">
                    <?= e($successMessage) ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
                <div class="admin-users-alert">
                    <?= e($errorMessage) ?>
                </div>
            <?php endif; ?>


            <!-- SUMMARY -->
            <section class="admin-users-summary">

                <div class="user-summary-card">

                    <span>Total akun</span>

                    <h2><?= $totalUsers ?></h2>

                    <p>Seluruh akun terdaftar</p>

                </div>

                <div class="user-summary-card">

                    <span>Akun pengguna</span>

                    <h2><?= $regularUsers ?></h2>

                    <p>Jumlah akun pengguna</p>

                </div>

                <div class="user-summary-card">

                    <span>Akun admin</span>

                    <h2><?= $adminUsers ?></h2>

                    <p>Jumlah akun administrator</p>

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

                       <select name="role">

                            <option value="">Semua jenis akun</option>

                            <option value="user" <?= $roleFilter === 'user' ? 'selected' : '' ?>>
                                Pengguna
                            </option>

                            <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>
                                Admin
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
                                <th>JENIS AKUN</th>
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

                               <td class="user-date-cell">
                                    <?= e(date('d M Y', strtotime($user['created_at']))) ?>
                                </td>

                                <td>

                                    <?php if ($user['role'] === 'admin'): ?>

                                        <span class="user-status active">
                                            Admin
                                        </span>

                                    <?php else: ?>

                                        <span class="user-status inactive">
                                            Pengguna
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <div class="admin-user-actions">

                                        <a href="detail.php?id=<?= (int) $user['id'] ?>" class="user-detail-button">
                                            Detail
                                        </a>

                                        <?php if ($user['role'] === 'user'): ?>

                                            <form action="index.php" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">

                                                <input type="hidden" name="delete_id" value="<?= (int) $user['id'] ?>">

                                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

                                                <button type="submit" class="user-delete-button">
                                                    Hapus
                                                </button>

                                            </form>

                                        <?php endif; ?>

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