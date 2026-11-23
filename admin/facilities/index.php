<?php
session_start();

$adminName = $_SESSION['name']
    ?? $_SESSION['username']
    ?? 'Admin';

// DATA FASILITAS CONTOH
if (!isset($_SESSION['admin_facilities'])) {
    $_SESSION['admin_facilities'] = [
        [
            'id' => 1,
            'name' => 'Puskesmas Sejahtera',
            'type' => 'Puskesmas',
            'address' => 'Jl. Kesehatan No. 12, Purwokerto',
            'contact' => 'Kontak belum tersedia',
            'status' => 'Aktif'
        ],
        [
            'id' => 2,
            'name' => 'Klinik Bersama',
            'type' => 'Klinik',
            'address' => 'Jl. Melati No. 8, Purwokerto',
            'contact' => 'Kontak belum tersedia',
            'status' => 'Aktif'
        ],
        [
            'id' => 3,
            'name' => 'RSU Contoh Sehat',
            'type' => 'Rumah sakit',
            'address' => 'Area Purwokerto',
            'contact' => 'Kontak belum tersedia',
            'status' => 'Aktif'
        ],
        [
            'id' => 4,
            'name' => 'Puskesmas Sehat Mandiri',
            'type' => 'Puskesmas',
            'address' => 'Jl. Merdeka, Purwokerto',
            'contact' => 'Kontak belum tersedia',
            'status' => 'Aktif'
        ],
        [
            'id' => 5,
            'name' => 'Klinik Medika',
            'type' => 'Klinik',
            'address' => 'Jl. Jenderal Sudirman, Purwokerto',
            'contact' => 'Kontak belum tersedia',
            'status' => 'Aktif'
        ],
        [
            'id' => 6,
            'name' => 'Rumah Sakit Harapan',
            'type' => 'Rumah sakit',
            'address' => 'Purwokerto',
            'contact' => 'Kontak belum tersedia',
            'status' => 'Aktif'
        ]
    ];
}

// HAPUS FASILITAS
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['delete_id'])
) {
    $deleteId = (int) $_POST['delete_id'];

    $_SESSION['admin_facilities'] = array_values(
        array_filter(
            $_SESSION['admin_facilities'],
            function ($facility) use ($deleteId) {
                return $facility['id'] !== $deleteId;
            }
        )
    );

    header('Location: index.php?deleted=1');
    exit;
}

// FILTER DAN PENCARIAN
$keyword = trim($_GET['search'] ?? '');
$typeFilter = $_GET['type'] ?? '';

$filteredFacilities = array_filter(
    $_SESSION['admin_facilities'],
    function ($facility) use ($keyword, $typeFilter) {

        $matchKeyword = $keyword === ''
            || stripos($facility['name'], $keyword) !== false
            || stripos($facility['address'], $keyword) !== false;

        $matchType = $typeFilter === ''
            || $facility['type'] === $typeFilter;

        return $matchKeyword && $matchType;
    }
);

// HELPER
function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$totalFacilities = count($_SESSION['admin_facilities']);

$totalPuskesmas = 0;
$totalKlinik = 0;
$totalHospital = 0;

foreach ($_SESSION['admin_facilities'] as $facility) {
    if ($facility['type'] === 'Puskesmas') {
        $totalPuskesmas++;
    } elseif ($facility['type'] === 'Klinik') {
        $totalKlinik++;
    } elseif ($facility['type'] === 'Rumah sakit') {
        $totalHospital++;
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manajemen Fasilitas - SEHATIN</title>

    <link rel="stylesheet" href="../../assets/css/reset.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="../../assets/css/admin-facilities.css">
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

                    <a href="index.php" class="admin-nav-link active">
                        <span>＋</span>
                        Fasilitas
                    </a>

                    <a href="../complaints/index.php" class="admin-nav-link">
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
        <main class="admin-main admin-facilities-main">

            <!-- HEADER -->
            <header class="admin-facilities-header">

                <div>
                    <h1>Manajemen fasilitas</h1>

                    <p>
                        Kelola informasi fasilitas kesehatan dan kontak yang tersedia.
                    </p>
                </div>

                <a href="create.php" class="facility-add-button">
                    + Tambah fasilitas
                </a>

            </header>


            <!-- NOTIFICATION -->
            <?php if (isset($_GET['deleted'])): ?>

            <div class="admin-facility-alert">
                Fasilitas berhasil dihapus.
            </div>

            <?php endif; ?>


            <!-- SUMMARY -->
            <section class="admin-facility-summary">

                <div class="facility-summary-card">

                    <span>Total fasilitas</span>

                    <h2><?= $totalFacilities ?></h2>

                    <p>Fasilitas terdaftar</p>

                </div>

                <div class="facility-summary-card">

                    <span>Puskesmas</span>

                    <h2><?= $totalPuskesmas ?></h2>

                    <p>Fasilitas puskesmas</p>

                </div>

                <div class="facility-summary-card">

                    <span>Klinik</span>

                    <h2><?= $totalKlinik ?></h2>

                    <p>Fasilitas klinik</p>

                </div>

                <div class="facility-summary-card">

                    <span>Rumah sakit</span>

                    <h2><?= $totalHospital ?></h2>

                    <p>Fasilitas rumah sakit</p>

                </div>

            </section>


            <!-- SEARCH AND FILTER -->
            <section class="admin-facility-search">

                <form action="index.php" method="GET">

                    <label for="searchFacility">
                        Cari fasilitas
                    </label>

                    <div class="admin-facility-search-controls">

                        <input type="text" id="searchFacility" name="search" placeholder="Nama fasilitas atau alamat"
                            value="<?= e($keyword) ?>">

                        <select name="type">

                            <option value="">Semua jenis</option>

                            <option value="Puskesmas" <?= $typeFilter === 'Puskesmas' ? 'selected' : '' ?>>
                                Puskesmas
                            </option>

                            <option value="Klinik" <?= $typeFilter === 'Klinik' ? 'selected' : '' ?>>
                                Klinik
                            </option>

                            <option value="Rumah sakit" <?= $typeFilter === 'Rumah sakit' ? 'selected' : '' ?>>
                                Rumah sakit
                            </option>

                        </select>

                        <button type="submit">
                            Cari
                        </button>

                    </div>

                </form>

            </section>


            <!-- FACILITY TABLE -->
            <section class="admin-facility-table-card">

                <div class="admin-facility-table-heading">

                    <h2>Daftar fasilitas</h2>

                    <span>
                        <?= count($filteredFacilities) ?> fasilitas
                    </span>

                </div>

                <div class="admin-facility-table-wrapper">

                    <table class="admin-facility-table">

                        <thead>

                            <tr>
                                <th>NAMA FASILITAS</th>
                                <th>JENIS</th>
                                <th>ALAMAT</th>
                                <th>KONTAK</th>
                                <th>STATUS</th>
                                <th>AKSI</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (count($filteredFacilities) > 0): ?>

                            <?php foreach ($filteredFacilities as $facility): ?>

                            <tr>

                                <td class="facility-name-cell">
                                    <?= e($facility['name']) ?>
                                </td>

                                <td>
                                    <span class="facility-type-badge">
                                        <?= e($facility['type']) ?>
                                    </span>
                                </td>

                                <td class="facility-address-cell">
                                    <?= e($facility['address']) ?>
                                </td>

                                <td class="facility-contact-cell">
                                    <?= e($facility['contact']) ?>
                                </td>

                                <td>
                                    <span class="facility-active-badge">
                                        <?= e($facility['status']) ?>
                                    </span>
                                </td>

                                <td>

                                    <div class="admin-facility-actions">

                                        <a href="edit.php?id=<?= (int) $facility['id'] ?>" class="facility-edit-button">
                                            Edit
                                        </a>

                                        <form action="index.php" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus fasilitas ini?')">

                                            <input type="hidden" name="delete_id" value="<?= (int) $facility['id'] ?>">

                                            <button type="submit" class="facility-delete-button">
                                                Hapus
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                            <?php endforeach; ?>

                            <?php else: ?>

                            <tr>

                                <td colspan="6" class="admin-facility-empty">
                                    Fasilitas tidak ditemukan.
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