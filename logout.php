<?php

// Memulai session jika belum aktif
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Menghapus seluruh data session
$_SESSION = [];

// Menghapus cookie session dari browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Menghancurkan session di server
session_destroy();

// Mencegah halaman logout tersimpan di cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Kembali ke halaman login
header("Location: login.php");
exit;
