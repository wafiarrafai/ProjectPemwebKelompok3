<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = 'Fitur autentikasi belum dihubungkan ke database.';
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
    <link rel="stylesheet" href="assets/css/auth.css">
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