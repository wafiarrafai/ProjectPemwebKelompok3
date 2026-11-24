<?php

$required_role = "user";
require_once __DIR__ . "/../includes/auth-check.php";
require_once __DIR__ . "/../config/database.php";

$profile = [
    "name" => $_SESSION["name"] ?? "Pengguna",
    "email" => $_SESSION["email"] ?? "",
    "created_at" => null
];

$userId = (int) $_SESSION["user_id"];
$stmt = $conn->prepare(
    "SELECT name, email, created_at FROM users WHERE id = ? AND role = 'user' LIMIT 1"
);

if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($profileName, $profileEmail, $profileCreatedAt);

    if ($stmt->fetch()) {
        $profile["name"] = $profileName;
        $profile["email"] = $profileEmail;
        $profile["created_at"] = $profileCreatedAt;
    }

    $stmt->close();
}

function profileEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

$profileInitial = preg_match('/^./u', $profile["name"], $initialMatch)
    ? strtoupper($initialMatch[0])
    : "P";
$joinedDate = $profile["created_at"]
    ? date("d M Y", strtotime($profile["created_at"]))
    : "—";
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - SEHATIN</title>

    <link rel="stylesheet" href="../assets/css/reset.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/user-complaints.css">
    <link rel="stylesheet" href="../assets/css/user-profile.css">
</head>

<body class="complaint-page profile-page">
    <div class="complaint-layout">
        <aside class="complaint-sidebar">
            <a href="../index.php" class="complaint-brand">
                <span class="complaint-brand-icon">S</span>
                <span>sehatin</span>
            </a>

            <div class="complaint-sidebar-menu">
                <p class="complaint-sidebar-label">AKUN SAYA</p>
                <nav class="complaint-nav" aria-label="Navigasi akun">
                    <a href="dashboard.php" class="complaint-nav-link">
                        <span aria-hidden="true">⌂</span>Ringkasan
                    </a>
                    <a href="complaints/create.php" class="complaint-nav-link">
                        <span aria-hidden="true">＋</span>Buat keluhan
                    </a>
                    <a href="complaints/history.php" class="complaint-nav-link">
                        <span aria-hidden="true">▤</span>Riwayat keluhan
                    </a>
                    <a href="profile.php" class="complaint-nav-link active" aria-current="page">
                        <span aria-hidden="true">♙</span>Profil
                    </a>
                </nav>
            </div>

            <div class="complaint-sidebar-bottom">
                <p><?= profileEscape($profile["name"]) ?></p>
                <span>Akun Pengguna</span>
                <a href="../logout.php">Keluar dari akun</a>
            </div>
        </aside>

        <main class="complaint-main profile-main">
            <header class="complaint-page-header">
                <nav class="complaint-breadcrumb" aria-label="Lokasi halaman">
                    <a href="dashboard.php">Ringkasan</a>
                    <span aria-hidden="true">/</span>
                    <span>Profil</span>
                </nav>
                <h1>Profil</h1>
                <p>Informasi akun yang digunakan untuk layanan SEHATIN.</p>
            </header>

            <section class="profile-summary-card" aria-label="Ringkasan profil">
                <div class="profile-avatar" aria-hidden="true"><?= profileEscape($profileInitial) ?></div>
                <div class="profile-summary-copy">
                    <span class="profile-eyebrow">AKUN PENGGUNA</span>
                    <h2><?= profileEscape($profile["name"]) ?></h2>
                    <p><?= profileEscape($profile["email"]) ?></p>
                </div>
            </section>

            <section class="profile-details-card">
                <div class="profile-section-heading">
                    <div>
                        <h2>Informasi akun</h2>
                        <p>Ringkasan data profil yang tersimpan.</p>
                    </div>
                    <span class="profile-status">Aktif</span>
                </div>

                <dl class="profile-details-list">
                    <div>
                        <dt>Nama lengkap</dt>
                        <dd><?= profileEscape($profile["name"]) ?></dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd><?= profileEscape($profile["email"]) ?></dd>
                    </div>
                    <div>
                        <dt>Jenis akun</dt>
                        <dd>Pengguna</dd>
                    </div>
                    <div>
                        <dt>Terdaftar sejak</dt>
                        <dd><?= profileEscape($joinedDate) ?></dd>
                    </div>
                </dl>
            </section>

            <section class="profile-shortcuts" aria-label="Tautan cepat">
                <a href="complaints/create.php" class="profile-shortcut-primary">Buat keluhan</a>
                <a href="complaints/history.php" class="profile-shortcut-secondary">Lihat riwayat keluhan</a>
            </section>
        </main>
    </div>
</body>

</html>
