<?php
session_start();

// Data artikel sementara
$articles = [
    1 => [
        'category' => 'Kesehatan umum',
        'title' => 'Kenali sinyal tubuh dengan lebih tenang',
        'description' => 'Mengenali perubahan pada tubuh merupakan langkah awal untuk menjaga kesehatan.',
        'reading_time' => 4,
        'image_type' => 'health',
        'content' => [
            'Tubuh kita selalu memberikan tanda ketika ada sesuatu yang berubah. Terkadang tanda tersebut muncul dalam bentuk rasa lelah, perubahan pola tidur, atau kondisi tubuh yang terasa berbeda dari biasanya.',
            'Mengenali sinyal tubuh merupakan bagian penting dari menjaga kesehatan. Cobalah untuk memperhatikan perubahan yang terjadi dan jangan mengabaikan keluhan yang berlangsung terus-menerus.',
            'Jika mengalami keluhan yang mengganggu atau tidak kunjung membaik, jangan ragu untuk berkonsultasi dengan tenaga kesehatan agar mendapatkan pemeriksaan dan penanganan yang tepat.'
        ]
    ],
    2 => [
        'category' => 'Pola hidup',
        'title' => 'Rutinitas kecil untuk waktu istirahat',
        'description' => 'Kebiasaan sederhana dapat membantu menciptakan waktu istirahat yang lebih berkualitas.',
        'reading_time' => 3,
        'image_type' => 'lifestyle',
        'content' => [
            'Istirahat yang cukup merupakan salah satu bagian penting dalam menjaga kesehatan. Tubuh membutuhkan waktu untuk memulihkan energi setelah menjalani aktivitas sehari-hari.',
            'Mulailah dengan membuat jadwal tidur yang teratur, mengurangi penggunaan perangkat elektronik sebelum tidur, dan menciptakan suasana kamar yang nyaman.',
            'Kebiasaan kecil yang dilakukan secara konsisten dapat membantu meningkatkan kualitas istirahat dan membuat tubuh terasa lebih segar.'
        ]
    ],
    3 => [
        'category' => 'Keluarga',
        'title' => 'Menyimpan informasi kesehatan keluarga',
        'description' => 'Pentingnya menyimpan informasi kesehatan keluarga agar lebih mudah diakses.',
        'reading_time' => 5,
        'image_type' => 'family',
        'content' => [
            'Informasi kesehatan keluarga merupakan hal yang penting untuk diperhatikan. Catatan mengenai riwayat penyakit, alergi, dan obat-obatan dapat membantu ketika anggota keluarga membutuhkan pelayanan kesehatan.',
            'Simpan informasi tersebut di tempat yang aman dan mudah dijangkau oleh anggota keluarga yang membutuhkan.',
            'Dengan informasi yang tersusun dengan baik, keluarga dapat lebih siap ketika harus berkonsultasi dengan tenaga kesehatan.'
        ]
    ],
    4 => [
        'category' => 'Kesehatan umum',
        'title' => 'Pentingnya menjaga kesehatan sehari-hari',
        'description' => 'Langkah sederhana untuk menjaga kondisi tubuh tetap sehat.',
        'reading_time' => 4,
        'image_type' => 'health',
        'content' => [
            'Menjaga kesehatan dapat dimulai dari kebiasaan sederhana dalam kehidupan sehari-hari.',
            'Konsumsi makanan bergizi, minum air putih yang cukup, berolahraga, dan tidur teratur merupakan beberapa kebiasaan yang dapat membantu menjaga kondisi tubuh.',
            'Lakukan kebiasaan tersebut secara konsisten dan sesuaikan dengan kebutuhan tubuh.'
        ]
    ],
    5 => [
        'category' => 'Pola hidup',
        'title' => 'Membangun kebiasaan hidup aktif',
        'description' => 'Mulai bergerak dan jadikan aktivitas fisik sebagai bagian dari rutinitas.',
        'reading_time' => 3,
        'image_type' => 'lifestyle',
        'content' => [
            'Aktivitas fisik memiliki banyak manfaat bagi kesehatan tubuh. Tidak harus selalu melakukan olahraga berat untuk memulai hidup aktif.',
            'Berjalan kaki, melakukan peregangan, atau memilih menggunakan tangga dapat menjadi langkah awal yang sederhana.',
            'Pilih aktivitas yang disukai agar lebih mudah menjadikannya sebagai kebiasaan.'
        ]
    ],
    6 => [
        'category' => 'Keluarga',
        'title' => 'Membangun kebiasaan sehat bersama keluarga',
        'description' => 'Ajak seluruh anggota keluarga menerapkan pola hidup sehat bersama.',
        'reading_time' => 5,
        'image_type' => 'family',
        'content' => [
            'Keluarga dapat menjadi lingkungan yang mendukung terbentuknya kebiasaan hidup sehat.',
            'Mulailah dengan melakukan kegiatan sederhana bersama, seperti menyiapkan makanan bergizi, berolahraga, dan menjaga kebersihan lingkungan rumah.',
            'Dukungan antaranggota keluarga dapat membantu setiap orang lebih konsisten dalam menjaga kesehatan.'
        ]
    ]
];

// Ambil ID artikel dari URL
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Cari artikel
$article = $articles[$id] ?? null;

// Fungsi keamanan output
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $article ? e($article['title']) : 'Artikel Tidak Ditemukan' ?> - SEHATIN
    </title>

    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/articles-detail.css">
</head>

<body class="article-detail-page">

    <?php if (!$article): ?>

    <main class="article-not-found">
        <div class="not-found-icon">📄</div>
        <h1>Artikel tidak ditemukan</h1>
        <p>Maaf, artikel yang kamu cari tidak tersedia.</p>
        <a href="articles.php" class="back-button">
            Kembali ke Artikel
        </a>
    </main>

    <?php else: ?>

    <main class="article-detail-container">

        <!-- Breadcrumb -->
        <nav class="article-breadcrumb">
            <a href="index.php">Beranda</a>
            <span>/</span>
            <a href="articles.php">Artikel</a>
            <span>/</span>
            <span>Detail Artikel</span>
        </nav>

        <!-- Header Artikel -->
        <header class="article-header">

            <a href="articles.php" class="article-back-link">
                ← Kembali ke Artikel
            </a>

            <span class="article-category">
                <?= e($article['category']) ?>
            </span>

            <h1><?= e($article['title']) ?></h1>

            <p class="article-description">
                <?= e($article['description']) ?>
            </p>

            <div class="article-meta">
                <span>✦ SEHATIN</span>
                <span>•</span>
                <span><?= e($article['reading_time']) ?> menit baca</span>
            </div>

        </header>

        <!-- Ilustrasi Artikel -->
        <section class="article-cover">
            <?php if ($article['image_type'] === 'health'): ?>
            <div class="cover-illustration health-illustration">
                <span>🩺</span>
                <span>💚</span>
                <span>🩹</span>
            </div>

            <?php elseif ($article['image_type'] === 'lifestyle'): ?>
            <div class="cover-illustration lifestyle-illustration">
                <span>🌱</span>
                <span>☀️</span>
                <span>💧</span>
            </div>

            <?php else: ?>
            <div class="cover-illustration family-illustration">
                <span>👨‍👩‍👧‍👦</span>
                <span>💚</span>
            </div>
            <?php endif; ?>
        </section>

        <!-- Isi Artikel -->
        <article class="article-content">

            <div class="article-content-heading">
                <span class="heading-line"></span>
                <h2>Isi Artikel</h2>
            </div>

            <?php foreach ($article['content'] as $paragraph): ?>
            <p><?= e($paragraph) ?></p>
            <?php endforeach; ?>

            <div class="article-note">
                <span class="note-icon">💡</span>
                <div>
                    <strong>Informasi Kesehatan</strong>
                    <p>
                        Artikel ini bertujuan memberikan informasi umum.
                        Untuk keluhan kesehatan tertentu, konsultasikan
                        dengan tenaga kesehatan profesional.
                    </p>
                </div>
            </div>

        </article>

        <!-- Footer Artikel -->
        <footer class="article-footer">
            <div>
                <h3>Jaga kesehatan bersama SEHATIN</h3>
                <p>Mulai dari informasi, untuk hidup yang lebih sehat.</p>
            </div>

            <a href="articles.php" class="back-button">
                Lihat Artikel Lainnya
            </a>
        </footer>

    </main>

    <?php endif; ?>

</body>

</html>