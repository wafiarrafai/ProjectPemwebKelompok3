<?php
$required_role = "admin";
require_once __DIR__ . "/../includes/auth-check.php";
?>

<h2>Dashboard Admin</h2>
<p>
    Selamat datang,
    <?= htmlspecialchars($_SESSION["name"]) ?>
</p>
<a href="../logout.php">Logout</a>