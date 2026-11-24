<?php
session_start();
require_once __DIR__ . "/config/database.php";
$error = "";

// Jika sudah login, arahkan ke dashboard sesuai role
if (isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] === "admin") {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit;
}

// Memproses form login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    // Validasi input
    if ($email === "" || $password === "") {
        $error = "Email dan kata sandi wajib diisi.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";

    } else {
        // Mencari akun berdasarkan email
        $sql = "SELECT id, name, email, password, role
                FROM users
                WHERE email = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();

            // Mengambil hasil query
            $stmt->bind_result(
                $userId,
                $userName,
                $userEmail,
                $storedPassword,
                $userRole
            );

            // Memeriksa akun dan password
                if ($stmt->fetch() && is_string($storedPassword) && password_verify($password, $storedPassword)) {

                $stmt->close();

                // Mengganti ID session setelah login
                session_regenerate_id(true);

                // Menyimpan identitas pengguna
                $_SESSION["user_id"] = $userId;
                $_SESSION["name"] = $userName;
                $_SESSION["email"] = $userEmail;
                $_SESSION["role"] = $userRole;

                // Mengarahkan berdasarkan role
                if ($userRole === "admin") {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: user/dashboard.php");
                }
                exit;

            } else {
                $error = "Email atau kata sandi salah.";
                $stmt->close();
            }
        } else {
            $error = "Terjadi kesalahan pada sistem. Silakan coba lagi.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Masuk - SEHATIN</title>

    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/auth.css?v=<?= filemtime(__DIR__ . '/assets/css/auth.css') ?>">
    <script src="assets/js/auth-motion.js?v=<?= filemtime(__DIR__ . '/assets/js/auth-motion.js') ?>" defer></script>
</head>

<body class="auth-body">

    <main class="login-page">

        <div class="login-container">

            <!-- LEFT PANEL -->
            <section class="login-left">

                <a href="index.php" class="auth-brand-icon">
                    S
                </a>

                <div class="login-introduction">

                    <span class="welcome-label">
                        SELAMAT DATANG DI SEHATIN
                    </span>

                    <h1>
                        Informasi dan catatan kesehatan,
                        lebih mudah ditemukan.
                    </h1>

                    <p>
                        Masuk untuk mengelola profil dan
                        memantau catatan keluhanmu.
                    </p>

                </div>

                <div class="login-left-footer">
                    SEHATIN — Temukan informasi kesehatan dengan mudah.
                </div>

            </section>


            <!-- RIGHT PANEL -->
            <section class="login-right">

                <div class="login-form-wrapper">

                    <!-- BRAND -->
                    <a href="index.php" class="login-brand">
                        <span class="brand-icon">S</span>
                        <span>sehatin</span>
                    </a>


                    <!-- BREADCRUMB -->
                    <div class="auth-breadcrumb">
                        <a href="index.php">Akun</a>
                        <span>/</span>
                        <span>Masuk</span>
                    </div>


                    <!-- HEADING -->
                    <div class="login-heading">

                        <h2>Masuk ke akunmu</h2>

                        <p>
                            Gunakan email dan kata sandi yang terdaftar.
                        </p>

                    </div>


                    <!-- ERROR MESSAGE -->
                    <?php if (!empty($error)): ?>

                    <div class="auth-error">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>

                    <?php endif; ?>


                    <!-- LOGIN FORM -->
                    <form action="login.php" method="POST" class="login-form">

                        <div class="form-group">

                            <label for="email">
                                Email
                            </label>

                            <input type="email" id="email" name="email" placeholder="nama@email.com"
                                autocomplete="email" required>

                        </div>


                        <div class="form-group">

                            <label for="password">
                                Kata sandi
                            </label>

                            <input type="password" id="password" name="password" placeholder="Masukkan kata sandi"
                                autocomplete="current-password" required>

                        </div>


                        <button type="submit" class="login-button">
                            Masuk
                        </button>

                    </form>

                    <aside class="demo-accounts" aria-label="Kredensial akun demo">
                        <div class="demo-accounts-heading">
                            <h3>Akun demo praktikum</h3>
                            <span>Untuk mencoba fitur</span>
                        </div>

                        <div class="demo-accounts-list">
                            <section class="demo-account">
                                <h4>Admin</h4>
                                <dl class="demo-account-details">
                                    <div>
                                        <dt>Email</dt>
                                        <dd><code>admin@gmail.com</code></dd>
                                    </div>
                                    <div>
                                        <dt>Kata sandi</dt>
                                        <dd><code>admin1234</code></dd>
                                    </div>
                                </dl>
                            </section>

                            <section class="demo-account">
                                <h4>Pengguna</h4>
                                <dl class="demo-account-details">
                                    <div>
                                        <dt>Email</dt>
                                        <dd><code>user@gmail.com</code></dd>
                                    </div>
                                    <div>
                                        <dt>Kata sandi</dt>
                                        <dd><code>user1234</code></dd>
                                    </div>
                                </dl>
                            </section>
                        </div>
                    </aside>


                    <!-- REGISTER LINK -->
                    <div class="login-register">

                        <p>Belum punya akun?</p>

                        <a href="register.php">
                            Daftar sekarang
                        </a>

                    </div>


                    <!-- FOOTER -->
                    <p class="login-note">
                        Data akunmu digunakan untuk menyimpan
                        profil dan riwayat keluhan.
                    </p>

                </div>

            </section>

        </div>

    </main>

</body>

</html>
