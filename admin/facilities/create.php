
<?php

$required_role = "admin";
require_once __DIR__ . "/../../includes/auth-check.php";
require_once __DIR__ . "/../../config/database.php";

$adminName = $_SESSION["name"]
    ?? $_SESSION["username"]
    ?? "Admin";

$allowedTypes = ["Puskesmas", "Klinik", "Rumah sakit"];

$errorMessage = "";

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// NILAI FORM
$name = "";
$type = "";
$address = "";
$phone = "";
$description = "";

// PROSES SIMPAN
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $csrfToken = $_POST["csrf_token"] ?? "";

    $name = trim($_POST["name"] ?? "");
    $type = trim($_POST["type"] ?? "");
    $address = trim($_POST["address"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if (
        !is_string($csrfToken) ||
        !hash_equals($_SESSION["csrf_token"], $csrfToken)
    ) {
        $errorMessage = "Permintaan tidak valid. Silakan coba lagi.";

    } elseif (
        $name === "" ||
        $type === "" ||
        $address === "" ||
        $description === ""
    ) {
        $errorMessage = "Nama, jenis, alamat, dan deskripsi wajib diisi.";

    } elseif (!in_array($type, $allowedTypes, true)) {
        $errorMessage = "Jenis fasilitas tidak valid.";

    } else {

        $stmt = $conn->prepare(
            "INSERT INTO facilities
                (name, type, address, phone, description)
             VALUES (?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            $errorMessage = "Gagal menyiapkan penyimpanan data.";

        } else {

            $stmt->bind_param(
                "sssss",
                $name,
                $type,
                $address,
                $phone,
                $description
            );

            if ($stmt->execute()) {

                $_SESSION["facility_success"] =
                    "Fasilitas berhasil ditambahkan.";

                $_SESSION["csrf_token"] = bin2hex(random_bytes(32));

                $stmt->close();

                header("Location: index.php");
                exit;

            } else {
                $errorMessage = "Gagal menyimpan fasilitas.";
            }

            $stmt->close();
        }
    }
}

// FUNGSI OUTPUT
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Fasilitas - SEHATIN</title>

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

            <div class="admin-sidebar-bottom">
                <p><?= e($adminName) ?> · Admin</p>
                <a href="../../logout.php">Keluar dari akun</a>
            </div>

        </aside>

        <!-- MAIN CONTENT -->
        <main class="admin-main">

            <header>
                <h1>Tambah fasilitas</h1>
                <p>Masukkan informasi fasilitas kesehatan baru.</p>
            </header>

            <section>

                <a href="index.php">← Kembali ke daftar fasilitas</a>

                <?php if ($errorMessage !== ""): ?>
                    <p role="alert"><?= e($errorMessage) ?></p>
                <?php endif; ?>

                <form action="create.php" method="POST">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($_SESSION["csrf_token"]) ?>"
                    >

                    <div>
                        <label for="name">Nama fasilitas</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="<?= e($name) ?>"
                            placeholder="Masukkan nama fasilitas"
                            required
                        >
                    </div>

                    <div>
                        <label for="type">Jenis fasilitas</label>
                        <select id="type" name="type" required>
                            <option value="">Pilih jenis fasilitas</option>

                            <?php foreach ($allowedTypes as $allowedType): ?>
                                <option
                                    value="<?= e($allowedType) ?>"
                                    <?= $type === $allowedType ? "selected" : "" ?>
                                >
                                    <?= e($allowedType) ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div>
                        <label for="address">Alamat</label>
                        <textarea
                            id="address"
                            name="address"
                            rows="3"
                            placeholder="Masukkan alamat fasilitas"
                            required
                        ><?= e($address) ?></textarea>
                    </div>

                    <div>
                        <label for="phone">Nomor telepon</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="<?= e($phone) ?>"
                            placeholder="Masukkan nomor telepon"
                        >
                    </div>

                    <div>
                        <label for="description">Deskripsi</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="5"
                            placeholder="Masukkan deskripsi fasilitas"
                            required
                        ><?= e($description) ?></textarea>
                    </div>

                    <button type="submit">
                        Simpan fasilitas
                    </button>

                    <a href="index.php">Batal</a>

                </form>

            </section>

        </main>

    </div>

</body>
</html>
