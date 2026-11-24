
<?php

$required_role = "admin";
require_once __DIR__ . "/../../includes/auth-check.php";
require_once __DIR__ . "/../../config/database.php";

$adminName = $_SESSION["name"]
    ?? $_SESSION["username"]
    ?? "Admin";

// MEMBUAT TOKEN KEAMANAN
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$csrfToken = $_SESSION["csrf_token"];

// MENGAMBIL ID KELUHAN
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header("Location: index.php");
    exit;
}

$success = "";
$error = "";

// MEMPROSES PERUBAHAN DATA
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $postedToken = $_POST["csrf_token"] ?? "";

    if (!hash_equals($csrfToken, $postedToken)) {
        $error = "Permintaan tidak valid. Silakan coba lagi.";
    } else {

        $status = $_POST["status"] ?? "";
        $adminNote = trim($_POST["admin_note"] ?? "");

        $allowedStatuses = [
            "Menunggu",
            "Diproses",
            "Selesai"
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            $error = "Status keluhan tidak valid.";
        } else {

            $sqlUpdate = "UPDATE complaints
                          SET status = ?, admin_note = ?
                          WHERE id = ?";

            $stmtUpdate = $conn->prepare($sqlUpdate);

            if ($stmtUpdate) {

                $stmtUpdate->bind_param(
                    "ssi",
                    $status,
                    $adminNote,
                    $id
                );

                if ($stmtUpdate->execute()) {
                    $stmtUpdate->close();

                    header(
                        "Location: detail.php?id=" . $id . "&updated=1"
                    );
                    exit;

                } else {
                    $error = "Gagal menyimpan perubahan keluhan.";
                    $stmtUpdate->close();
                }

            } else {
                $error = "Terjadi kesalahan pada sistem.";
            }
        }
    }
}

// PESAN SETELAH BERHASIL UPDATE
if (isset($_GET["updated"])) {
    $success = "Perubahan keluhan berhasil disimpan.";
}

// MENGAMBIL DETAIL KELUHAN
$complaint = null;

$sql = "SELECT
            c.id,
            c.title,
            c.description,
            c.status,
            c.admin_note,
            DATE_FORMAT(c.created_at, '%d %b %Y %H:%i') AS date,
            u.name AS user_name,
            u.email AS user_email,
            cc.name AS category
        FROM complaints c
        JOIN users u ON c.user_id = u.id
        JOIN complaint_categories cc ON c.category_id = cc.id
        WHERE c.id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);

if ($stmt) {

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt->bind_result(
        $complaintId,
        $complaintTitle,
        $complaintDescription,
        $complaintStatus,
        $complaintNote,
        $complaintDate,
        $userName,
        $userEmail,
        $complaintCategory
    );

    if ($stmt->fetch()) {

        $complaint = [
            "id" => $complaintId,
            "title" => $complaintTitle,
            "description" => $complaintDescription,
            "status" => $complaintStatus,
            "admin_note" => $complaintNote,
            "date" => $complaintDate,
            "user_name" => $userName,
            "user_email" => $userEmail,
            "category" => $complaintCategory
        ];
    }

    $stmt->close();
}

if (!$complaint) {
    http_response_code(404);
}

// FUNGSI KEAMANAN OUTPUT
function e($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Keluhan - SEHATIN</title>

    <link rel="stylesheet" href="../../assets/css/reset.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

    <main style="max-width: 850px; margin: 40px auto; padding: 20px;">

        <a href="index.php">← Kembali ke daftar keluhan</a>

        <h1>Detail Keluhan</h1>

        <?php if ($success): ?>
            <p style="color: green;">
                <?= e($success) ?>
            </p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p style="color: red;">
                <?= e($error) ?>
            </p>
        <?php endif; ?>

        <?php if (!$complaint): ?>

            <h2>Keluhan tidak ditemukan.</h2>

        <?php else: ?>

            <section style="margin-top: 25px;">

                <h2><?= e($complaint["title"]) ?></h2>

                <p>
                    <strong>Nama pengguna:</strong>
                    <?= e($complaint["user_name"]) ?>
                </p>

                <p>
                    <strong>Email:</strong>
                    <?= e($complaint["user_email"]) ?>
                </p>

                <p>
                    <strong>Kategori:</strong>
                    <?= e($complaint["category"]) ?>
                </p>

                <p>
                    <strong>Tanggal:</strong>
                    <?= e($complaint["date"]) ?>
                </p>

                <p>
                    <strong>Deskripsi keluhan:</strong>
                </p>

                <p><?= nl2br(e($complaint["description"])) ?></p>

            </section>

            <hr style="margin: 25px 0;">

            <section>

                <h2>Tindak Lanjut Admin</h2>

                <form method="POST">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($csrfToken) ?>"
                    >

                    <div style="margin: 15px 0;">

                        <label for="status">
                            Status Keluhan
                        </label>

                        <select
                            name="status"
                            id="status"
                            required
                        >

                            <?php
                            $statuses = [
                                "Menunggu",
                                "Diproses",
                                "Selesai"
                            ];
                            ?>

                            <?php foreach ($statuses as $status): ?>

                                <option
                                    value="<?= e($status) ?>"
                                    <?= $complaint["status"] === $status
                                        ? "selected"
                                        : "" ?>
                                >
                                    <?= e($status) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div style="margin: 15px 0;">

                        <label for="admin_note">
                            Catatan Tindak Lanjut
                        </label>

                        <textarea
                            name="admin_note"
                            id="admin_note"
                            rows="5"
                            style="width: 100%;"
                            placeholder="Tuliskan catatan tindak lanjut..."
                        ><?= e($complaint["admin_note"] ?? "") ?></textarea>

                    </div>

                    <button type="submit">
                        Simpan Perubahan
                    </button>

                </form>

            </section>

        <?php endif; ?>

    </main>

</body>
</html>
