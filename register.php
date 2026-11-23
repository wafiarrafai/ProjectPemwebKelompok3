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
        <title>Registrasi SEHATIN</title>
    </head>
    <body>
        <h2>Registrasi Pengguna</h2>
        <?php if (!empty($message)): ?>
            <p>
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <form method="POST" action="">
            <label>Nama</label><br>
            <input type="text" name="name" required>
            <br><br>

            <label>Email</label><br>
            <input type="email" name="email" required>
            <br><br>

            <label>Password</label><br>
            <input type="password" name="password" required>
            <br><br>

            <label>Konfirmasi Password</label><br>
            <input type="password" name="confirm_password" required>
            <br><br>

            <button type="submit">Daftar</button>
        </form>
        <p>
            Sudah punya akun?
            <a href="login.php">Login</a>
        </p>
    </body>
</html>
