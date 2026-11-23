<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "sehatin_db";

// Membuat koneksi ke MySQL
$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal.");
}

// Mengatur charset
$conn->set_charset("utf8mb4");

?>
