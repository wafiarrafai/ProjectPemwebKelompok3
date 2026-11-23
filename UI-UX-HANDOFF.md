# SEHATIN — UI/UX Polish Handoff

**Figma:** [SEHATIN UI/UX MVP](https://www.figma.com/design/cdSTDTyx86o0WxMDjjv3Mq/sehatin)  
**Status:** overlap form sudah dirapikan oleh Aqil. Polish lanjutan dicatat dari screenshot board; penulisan ke Figma masih terhalang batas panggilan Starter.

Dokumen ini menerjemahkan temuan overlap dan margin dari review awal menjadi aturan layout yang bisa diterapkan langsung pada frame Figma. Acuan warna, radius, dan skala spacing tetap memakai `docs/03-UI-UX-GUIDELINES.md`.

## Review screenshot board terbaru

- Beranda dan Artikel terlihat belum memakai header/footer publik yang sama, sedangkan Fasilitas sudah menampilkan navigasi dan footer. Terapkan satu shell publik pada tiga frame itu, lalu pastikan konten mulai di bawah header dengan gutter yang sama.
- Form keluhan desktop sekarang punya form dan panel informasi berdampingan; versi mobile menumpuknya dalam satu kolom. Pertahankan pola ini, lalu rapikan jarak antarfield, panel informasi, dan tombol agar ritmenya konsisten.
- Dashboard User dan halaman Admin sudah memakai pola sidebar yang seragam. Samakan posisi awal judul halaman dan aksi kanan di semua halaman pada masing-masing area.
- Screenshot berupa overview canvas, jadi ukuran font dan jarak detail perlu dicek di frame Figma pada zoom 100% sebelum handoff.

## Aturan layout bersama

- Siapkan frame review desktop **1440 px** dan mobile **390 px**. Untuk tablet, cek satu ukuran **768 px**.
- Konten desktop memakai lebar maksimum **1264 px** dengan margin otomatis di tengah. Gutter mobile **20 px** dan tablet **24 px**.
- Gunakan Auto Layout untuk header, section, card, form, dan kelompok tombol. Hindari posisi absolut untuk teks atau konten yang tingginya bisa berubah.
- Pertahankan jarak dari skala **8, 12, 16, 20, 24, 32, 40, 48 px**. Jarak antarseksi utama 40–48 px; jarak judul ke isi 20–24 px; jarak antarcard 24 px desktop dan 16 px mobile.
- Tinggi tombol utama **44–48 px**. Semua tombol dalam satu kelompok aksi memakai tinggi dan radius yang sama.
- Judul panjang boleh membungkus baris. Jangan mengecilkan font atau menumpuk tombol untuk memaksakan satu baris.

## Perbaikan prioritas

| Frame | Perbaikan yang harus diterapkan |
|---|---|
| Beranda `7:151` | Tambahkan shell publik yang sama dengan Fasilitas. Susun hero sebagai dua kolom dengan jarak 48–64 px; ubah jadi satu kolom di mobile. Pertahankan pemisahan kartu artwork yang sudah dirapikan. Heading section dan tautan “Lihat semua” turun baris saat ruang sempit. |
| Artikel `7:234` | Tambahkan shell publik yang sama dengan Beranda dan Fasilitas. Grid 3 kolom di desktop, 2 di tablet, 1 di mobile. Samakan tinggi area artwork tiap card dan beri ruang label dari bentuk dekoratif. Jaga jarak filter ke daftar dan antarbaris card. |
| Fasilitas `7:82` | Pertahankan shell publik sebagai acuan. Samakan gutter dan jarak judul ke pencarian/filter dengan halaman Artikel. Pastikan aksi di kartu rata dan tidak terdorong isi yang lebih panjang. |
| Form keluhan desktop `8:379` | Pertahankan form dan panel informasi berdampingan. Samakan jarak vertikal label, field, helper/error, dan tombol; cek bahwa panel informasi sejajar dengan awal form. |
| Form keluhan mobile `8:594` | Pertahankan satu kolom. Cek gutter 20 px dan jarak antarfield. Panel informasi mengikuti form; tombol aksi selebar kolom dan berada setelah isi form. |
| Dashboard admin `9:284` | Pertahankan sidebar dan grid statistik pada desktop. Samakan posisi judul halaman serta aksi kanan dengan halaman Admin lain. Di tablet/mobile, ringkas navigasi dan susun statistik menjadi dua kolom lalu satu kolom. |

## Detail yang perlu diperiksa di semua frame

- **Header/footer:** gunakan komponen yang sama untuk seluruh halaman publik; cek jarak logo, item navigasi, dan tombol Masuk/Daftar. Jangan biarkan komponen berdiri di luar frame halaman.
- **Artwork card:** bentuk dekoratif berada di layer belakang. Label tetap punya ruang baca yang bersih, dengan padding konsisten; tidak ada teks dekoratif yang saling menimpa.
- **Section heading:** area judul boleh mengisi ruang fleksibel, sedangkan tautan aksi punya lebar sesuai teks. Pada mobile, tautan aksi turun di bawah deskripsi dengan jarak 12–16 px.
- **Card:** isi memakai padding 20–24 px; seluruh card dalam satu grid punya garis atas dan baseline isi yang konsisten. Hindari tinggi tetap untuk card berisi teks.
- **Form:** jarak antarfield 16–20 px. Pesan validasi ditempatkan tepat di bawah field terkait; tombol tidak menutupi catatan/helper.
- **Status:** selalu tampilkan label teks di samping warna status.

## Catatan saat dicocokkan ke frontend

- Di `assets/css/home.css`, dua kartu dekoratif hero memakai posisi absolut. Saat menyusun ulang frame, cek bounding box pada tinggi hero desktop dan mobile supaya kartu tidak saling menimpa.
- Link aksi pada heading beranda memakai `white-space: nowrap`. Di Figma, tetapkan perilaku turun ke baris berikutnya saat judul/deskripsi butuh ruang; jangan paksa heading mengecil.
- Gutter mobile halaman publik sekarang berbeda: beranda sekitar **18 px**, artikel **16 px**, dan fasilitas **20 px**. Spesifikasi desain ini menyatukannya menjadi **20 px**.
- Breakpoint CSS beranda ada di **850/600 px**; siapkan frame tablet **768 px** dan mobile **390 px** untuk menangkap perubahan layout di kedua sisi.

## Kriteria selesai

- Tidak ada bounding box konten yang bertumpuk pada desktop, tablet, atau mobile.
- Tidak ada teks, tombol, atau field yang menyentuh tepi frame; gutter terasa sama pada seluruh halaman.
- Header, footer, tombol, card, dan field memakai komponen/token yang sama di semua frame terkait.
- Periksa ulang enam frame di atas pada ukuran desktop dan mobile sebelum handoff.
- Catat perbedaan font sebelum implementasi akhir: guideline Figma menetapkan **Inter**, sementara stylesheet halaman publik saat ini memakai **Arial**.
