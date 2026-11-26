# SEHATIN

Platform web untuk membaca informasi kesehatan, menemukan fasilitas kesehatan, dan menyampaikan keluhan.

Proyek Akhir (Responsi 2) Praktikum Pemrograman Web I, Informatika, Universitas Jenderal Soedirman, 2026 (Kelompok 3).

## Deskripsi Proyek

SEHATIN adalah aplikasi web berbasis PHP dengan dua peran, yaitu **User** dan **Admin**. User dapat membaca artikel kesehatan, mencari fasilitas kesehatan, mengirim keluhan, dan melihat riwayat keluhannya. Admin mengelola data dan memantau statistik lewat dashboard.

## Latar Belakang dan Tujuan

**Latar belakang.** Akses terhadap informasi dan layanan kesehatan belum merata. Informasi kesehatan tersebar di banyak sumber, fasilitas kesehatan sulit ditemukan di satu tempat, dan keluhan sering disampaikan lewat jalur yang tidak jelas sehingga sulit dipantau.

**Tujuan.**
- Menyediakan informasi kesehatan yang mudah diakses masyarakat.
- Mempermudah pencarian fasilitas kesehatan.
- Menyediakan wadah keluhan yang tercatat dan dapat dipantau statusnya.

Proyek ini mendukung **SDG 3: Kehidupan Sehat dan Sejahtera**.

## Daftar Fitur / Modul

| No | Modul | Keterangan |
|----|-------|------------|
| 1 | Login & Register | Pendaftaran, masuk, dan keluar akun |
| 2 | Artikel Kesehatan | Daftar artikel dengan filter topik |
| 3 | Detail Artikel | Halaman baca lengkap tiap artikel |
| 4 | Direktori Fasilitas | Daftar fasilitas kesehatan yang bisa dicari dan disaring |
| 5 | Detail Fasilitas | Informasi lengkap tiap fasilitas kesehatan |
| 6 | Form Keluhan | Pengiriman keluhan dengan validasi input |
| 7 | Riwayat Keluhan | Daftar keluhan yang pernah dikirim beserta statusnya |
| 8 | Dashboard User | Ringkasan aktivitas dan keluhan pengguna |
| 9 | Dashboard Admin | Statistik dan pengelolaan data |

## Teknologi yang Digunakan

- PHP
- MySQL / MariaDB
- HTML dan CSS
- XAMPP (Apache + MySQL) sebagai server lokal
- Figma (desain UI/UX)
- Git dan GitHub

## Panduan Instalasi dan Konfigurasi

**Prasyarat:** XAMPP (Apache, PHP, MySQL) dan browser modern.

1. Clone atau unduh repository ini ke folder `htdocs`:
   ```bash
   cd C:/xampp/htdocs
   git clone https://github.com/wafiarrafai/ProjectPemwebKelompok3.git
   ```
2. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin`, lalu buat database baru bernama `[nama_database]`.
4. Pilih database tersebut, buka tab **Import**, lalu pilih berkas SQL di folder `database/` (`[nama_file.sql]`) dan klik **Go**.
5. Buka berkas konfigurasi di folder `config/` (`[nama_file_config.php]`), lalu sesuaikan dengan pengaturan MySQL kamu:
   ```php
   $host = "localhost";
   $user = "root";
   $pass = "";
   $db   = "[nama_database]";
   ```

## Panduan Menjalankan Aplikasi

1. Pastikan Apache dan MySQL sudah berjalan.
2. Buka browser dan akses `http://localhost/ProjectPemwebKelompok3/`.
3. Daftar akun lewat halaman **Register**, lalu masuk lewat **Login**.
4. Untuk mencoba fitur admin, gunakan akun berikut:

   | Peran | Email / Username | Password |
   |-------|------------------|----------|
   | Admin | `[admin@gmail].com` | `admin1234` |
   | User  | `[user@gmail.com]` | `[user1234]` |

## Tim Pengembang (Kelompok 3)

| Nama | NIM |
|------|-----|
| `Aqil Fachri Al Habsyi` | `[H1D025053]` |
| `[Awal Ramadhan Depa]` | `[H1D025057]` |
| `[Wafi Abdul Wahid Ar Rafai]` | `[H1D025072]` |
