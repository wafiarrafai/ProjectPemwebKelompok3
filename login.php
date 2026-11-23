<?php
session_start();
require_once "config/database.php";
$message = "";

// Mengecek apakah registrasi berhasil
if (isset($_GET["registered"])) {
    $message = "Registrasi berhasil. Silakan login.";
}

// Memproses form login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if (empty($email) || empty($password)) {
        $message = "Email dan password wajib diisi.";

    } else {
        // Mengambil data pengguna berdasarkan email
        $sql = "SELECT id, name, email, password, role
                FROM users
                WHERE email = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Memeriksa password
        if ($user && password_verify($password, $user["password"])) {

            // Membuat session baru
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            // Mengarahkan berdasarkan role
            if ($user["role"] === "admin") {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit;
        } else {
            $message = "Email atau password salah.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Login SEHATIN</title>
    </head>
    <body>
        <h2>Login Pengguna</h2>
        <?php if (!empty($message)): ?>
            <p><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label>Email</label><br>
            <input type="email" name="email" required>
            <br><br>
            <label>Password</label><br>
            <input type="password" name="password" required>
            <br><br>
            <button type="submit">Login</button>
        </form>
        <p>
            Belum punya akun?
            <a href="register.php">Daftar</a>
        </p>
    </body>
</html>