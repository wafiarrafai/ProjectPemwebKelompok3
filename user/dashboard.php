<?php
$required_role = "user";
require_once __DIR__ . "/../includes/auth-check.php";
?>

<h2>Dashboard User</h2>
<p>
    Selamat datang,
    <?= htmlspecialchars($_SESSION["name"]) ?>
</p>
<a href="../logout.php">Logout</a>
