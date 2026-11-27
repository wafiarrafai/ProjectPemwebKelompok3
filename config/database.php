<?php

$databaseConfig = [
    "host" => getenv("DB_HOST") ?: "localhost",
    "username" => getenv("DB_USER") ?: "root",
    "password" => getenv("DB_PASSWORD") ?: "",
    "database" => getenv("DB_NAME") ?: "sehatin_db",
];

// Hosting shared seperti InfinityFree biasanya memakai kredensial DB khusus.
// Simpan nilainya di database.local.php agar tidak ikut masuk ke Git.
$localConfigPath = __DIR__ . "/database.local.php";

if (is_file($localConfigPath)) {
    $localConfig = require $localConfigPath;

    if (is_array($localConfig)) {
        $databaseConfig = array_replace($databaseConfig, $localConfig);
    }
}

// Membuat koneksi ke MySQL
$conn = new mysqli(
    $databaseConfig["host"],
    $databaseConfig["username"],
    $databaseConfig["password"],
    $databaseConfig["database"]
);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal.");
}

// Mengatur charset
$conn->set_charset("utf8mb4");
