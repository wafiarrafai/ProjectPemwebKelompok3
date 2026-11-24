<?php

// Memulai session jika belum aktif
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Mencegah halaman terlindungi ditampilkan dari cache browser
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Ambil alamat dasar dari URL halaman yang sedang dibuka agar project tetap
// bekerja di alias Apache mana pun (misalnya /sehatin atau /ProjectPemwebKelompok3).
$scriptPath = str_replace("\\", "/", $_SERVER["SCRIPT_NAME"] ?? "");
$baseUrl = "";

foreach (["/user/", "/admin/"] as $areaPath) {
    $areaPosition = strpos($scriptPath, $areaPath);

    if ($areaPosition !== false) {
        $baseUrl = substr($scriptPath, 0, $areaPosition);
        break;
    }
}

// Memeriksa apakah pengguna sudah login
if (empty($_SESSION["user_id"])) {
    header("Location: " . $baseUrl . "/login.php");
    exit;
}

// Memeriksa role halaman
$required_role = $required_role ?? null;

if (
    $required_role !== null &&
    ($_SESSION["role"] ?? null) !== $required_role
) {
    if (($_SESSION["role"] ?? null) === "admin") {
        header("Location: " . $baseUrl . "/admin/dashboard.php");
    } else {
        header("Location: " . $baseUrl . "/user/dashboard.php");
    }

    exit;
}
