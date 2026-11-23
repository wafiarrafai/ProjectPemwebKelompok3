<?php

// Memastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

// Mengambil role yang dibutuhkan halaman
$required_role = $required_role ?? null;

// Memeriksa kesesuaian role
if ($required_role !== null && $_SESSION["role"] !== $required_role) {

    // Mengarahkan pengguna ke dashboard sesuai role
    if ($_SESSION["role"] === "admin") {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/dashboard.php");
    }
    exit;
}
