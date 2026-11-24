<?php
require_once "config/database.php";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Mengambil data dari form
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    // Validasi input
    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {
        $message = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format email tidak valid.";
    } elseif ($password !== $confirm_password) {
        $message = "Konfirmasi password tidak cocok.";
    } else {

        // Memeriksa apakah email sudah digunakan
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Email sudah terdaftar.";
        } else {

            // Mengubah password menjadi hash
            $password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Menyimpan akun baru
            $sql = "INSERT INTO users (name, email, password, role)
                    VALUES (?, ?, ?, 'user')";

            $stmt = $conn->prepare($sql);

            $stmt->bind_param(
                "sss",
                $name,
                $email,
                $password_hash
            );

            if ($stmt->execute()) {
                header("Location: login.php?registered=1");
                exit;
            } else {
                $message = "Registrasi gagal. Silakan coba lagi.";
            }
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi SEHATIN</title>

    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css?v=<?= filemtime(__DIR__ . '/assets/css/auth.css') ?>">
    <link rel="stylesheet" href="assets/css/register.css?v=<?= filemtime(__DIR__ . '/assets/css/register.css') ?>">
    <script src="assets/js/auth-motion.js?v=<?= filemtime(__DIR__ . '/assets/js/auth-motion.js') ?>" defer></script>
</head>

<body class="auth-body">

    <main class="login-page">
        <div class="login-container register-container">
            <section class="login-left">
                <a href="index.php" class="auth-brand-icon" aria-label="Kembali ke beranda">S</a>

                <div class="login-introduction">
                    <span class="welcome-label">GABUNG DENGAN SEHATIN</span>
                    <h1>Mulai kelola informasi kesehatanmu dengan lebih mudah.</h1>
                    <p>Buat akun untuk menyimpan profil dan memantau catatan keluhanmu.</p>
                </div>

                <div class="login-left-footer">
                    SEHATIN — Temukan informasi kesehatan dengan mudah.
                </div>
            </section>

            <section class="login-right">
                <div class="login-form-wrapper register-form-wrapper">
                    <a href="index.php" class="login-brand">
                        <span class="brand-icon">S</span>
                        <span>sehatin</span>
                    </a>

                    <nav class="auth-breadcrumb" aria-label="Lokasi halaman">
                        <a href="index.php">Beranda</a>
                        <span aria-hidden="true">/</span>
                        <span>Daftar</span>
                    </nav>

                    <div class="login-heading register-heading">
                        <h2>Buat akun baru</h2>
                        <p>Lengkapi data berikut untuk mulai menggunakan SEHATIN.</p>
                    </div>

                    <?php if (!empty($message)): ?>
                    <div class="auth-error" role="alert">
                        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" action="register.php" class="login-form register-form">
                        <div class="form-group">
                            <label for="name">Nama lengkap</label>
                            <input type="text" id="name" name="name" placeholder="Nama sesuai identitas"
                                autocomplete="name" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" placeholder="nama@email.com"
                                autocomplete="email" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Kata sandi</label>
                            <input type="password" id="password" name="password" placeholder="Buat kata sandi"
                                autocomplete="new-password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi kata sandi</label>
                            <input type="password" id="confirm_password" name="confirm_password"
                                placeholder="Ulangi kata sandi" autocomplete="new-password" required>
                        </div>

                        <button type="submit" class="login-button">Daftar</button>
                    </form>

                    <div class="login-register register-switch">
                        <p>Sudah punya akun?</p>
                        <a href="login.php">Masuk</a>
                    </div>

                    <p class="login-note register-note">
                        Data akunmu digunakan untuk menyimpan profil dan riwayat keluhan.
                    </p>
                </div>
            </section>
        </div>
    </main>
</body>

</html>
