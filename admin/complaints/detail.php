
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

$statusStyles = [
    "Menunggu" => "waiting",
    "Diproses" => "processing",
    "Selesai" => "completed",
];

$formStatus = $complaint["status"] ?? "Menunggu";
$formAdminNote = $complaint["admin_note"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && $error !== "") {
    $submittedStatus = $_POST["status"] ?? "";

    if (is_string($submittedStatus) && isset($statusStyles[$submittedStatus])) {
        $formStatus = $submittedStatus;
    }

    $formAdminNote = trim($_POST["admin_note"] ?? "");
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
    <link rel="stylesheet" href="../../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="../../assets/css/admin-complaints.css">
    <link rel="stylesheet" href="../../assets/css/admin-complaint-detail.css?v=<?= filemtime(__DIR__ . '/../../assets/css/admin-complaint-detail.css') ?>">
</head>

<body class="admin-dashboard-page">
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <a href="../dashboard.php" class="admin-brand">
                <span class="admin-brand-icon">S</span>
                <span>sehatin</span>
            </a>

            <div class="admin-menu">
                <p class="admin-menu-label">PENGELOLAAN</p>
                <nav class="admin-nav" aria-label="Navigasi admin">
                    <a href="../dashboard.php" class="admin-nav-link"><span>●</span>Dashboard</a>
                    <a href="../articles/index.php" class="admin-nav-link"><span>▤</span>Artikel</a>
                    <a href="../categories/index.php" class="admin-nav-link"><span>▦</span>Kategori</a>
                    <a href="../facilities/index.php" class="admin-nav-link"><span>＋</span>Fasilitas</a>
                    <a href="index.php" class="admin-nav-link active" aria-current="page"><span>▣</span>Keluhan</a>
                    <a href="../users/index.php" class="admin-nav-link"><span>♙</span>Pengguna</a>
                </nav>
            </div>

            <div class="admin-sidebar-bottom">
                <p><?= e($adminName) ?> · Admin</p>
                <a href="../../logout.php">Keluar dari akun</a>
            </div>
        </aside>

        <main class="admin-main admin-complaints-main complaint-detail-main">
            <header class="complaint-detail-header">
                <div>
                    <a href="index.php" class="complaint-back-link">← Kembali ke daftar keluhan</a>
                    <span class="complaint-detail-eyebrow">TINJAUAN KELUHAN</span>
                    <h1>Detail keluhan</h1>
                    <p>Periksa informasi yang dikirim pengguna dan catat tindak lanjutnya.</p>
                </div>
            </header>

            <?php if ($success): ?>
                <div class="complaint-feedback success" role="status"><?= e($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="complaint-feedback error" role="alert"><?= e($error) ?></div>
            <?php endif; ?>

            <?php if (!$complaint): ?>
                <section class="complaint-panel complaint-not-found">
                    <span class="complaint-not-found-icon" aria-hidden="true">!</span>
                    <h2>Keluhan tidak ditemukan</h2>
                    <p>Keluhan mungkin sudah dihapus atau tautannya tidak valid.</p>
                    <a href="index.php" class="complaint-primary-button">Kembali ke daftar keluhan</a>
                </section>
            <?php else: ?>
                <div class="complaint-detail-grid">
                    <section class="complaint-panel complaint-summary-panel" aria-labelledby="complaint-title">
                        <div class="complaint-summary-heading">
                            <div>
                                <span class="complaint-record-id">Keluhan #<?= (int) $complaint["id"] ?></span>
                                <h2 id="complaint-title"><?= e($complaint["title"]) ?></h2>
                            </div>
                            <span class="complaint-detail-status <?= e($statusStyles[$complaint["status"]] ?? "waiting") ?>">
                                <?= e($complaint["status"]) ?>
                            </span>
                        </div>

                        <dl class="complaint-meta-grid">
                            <div>
                                <dt>Kategori</dt>
                                <dd><?= e($complaint["category"]) ?></dd>
                            </div>
                            <div>
                                <dt>Dikirim pada</dt>
                                <dd><?= e($complaint["date"]) ?></dd>
                            </div>
                        </dl>

                        <div class="complaint-description-block">
                            <h3>Deskripsi keluhan</h3>
                            <p><?= nl2br(e($complaint["description"])) ?></p>
                        </div>
                    </section>

                    <aside class="complaint-panel complaint-reporter-panel">
                        <span class="complaint-panel-kicker">INFORMASI PELAPOR</span>
                        <div class="complaint-reporter-avatar" aria-hidden="true">P</div>
                        <h2><?= e($complaint["user_name"]) ?></h2>
                        <p>Pengguna SEHATIN</p>
                        <a href="mailto:<?= e($complaint["user_email"]) ?>" class="complaint-reporter-email">
                            <?= e($complaint["user_email"]) ?>
                        </a>
                    </aside>
                </div>

                <section class="complaint-panel complaint-followup-panel">
                    <div class="complaint-followup-heading">
                        <div>
                            <span class="complaint-panel-kicker">TINDAK LANJUT</span>
                            <h2>Tindak lanjut admin</h2>
                            <p>Perbarui status dan tambahkan catatan untuk riwayat keluhan ini.</p>
                        </div>
                    </div>

                    <form method="POST" class="complaint-followup-form">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                        <div class="complaint-form-field complaint-status-field">
                            <label for="status">Status keluhan</label>
                            <select name="status" id="status" required>
                                <?php foreach (["Menunggu", "Diproses", "Selesai"] as $status): ?>
                                    <option value="<?= e($status) ?>" <?= $formStatus === $status ? "selected" : "" ?>>
                                        <?= e($status) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="complaint-form-field">
                            <label for="admin_note">Catatan tindak lanjut <span>(opsional)</span></label>
                            <textarea name="admin_note" id="admin_note" rows="5" placeholder="Tuliskan catatan tindak lanjut..."><?= e($formAdminNote) ?></textarea>
                            <small>Hindari mencantumkan informasi sensitif yang tidak diperlukan.</small>
                        </div>

                        <div class="complaint-form-actions">
                            <a href="index.php" class="complaint-secondary-button">Batal</a>
                            <button type="submit" class="complaint-primary-button">Simpan perubahan</button>
                        </div>
                    </form>
                </section>
            <?php endif; ?>

            <footer class="admin-footer">
                <p>© 2026 SEHATIN. Admin Dashboard.</p>
            </footer>
        </main>
    </div>
</body>
</html>
