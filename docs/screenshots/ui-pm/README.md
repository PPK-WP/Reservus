# Tangkapan layar polish UI PM

Hasil branch `polish/ui-pm` (commit `554d0e4`) dibandingkan dengan `main` (commit `9bb34e5`).
Diambil 5 Oktober 2026 dengan Chromium tanpa layar, login sebagai masing-masing peran.

| Folder | Isi |
|---|---|
| `perbandingan/` | **Mulai dari sini.** Sebelum (kiri, `main`) dan sesudah (kanan, `polish/ui-pm`) berdampingan, lebar desktop 1280px dan HP 375px |
| `sesudah/` | Hasil `polish/ui-pm` ukuran asli, lebar 1280 / 768 / 375 px |
| `sebelum/` | Kondisi `main` ukuran asli, sebagai pembanding |

Kedua putaran memakai database terpisah `reservus_polish` yang di-seed segar dengan
`php artisan migrate:fresh --seed`, sehingga datanya identik dan sesuai fixture README §7.7.
Database kerja `reservus` tidak disentuh.

## Daftar halaman

| No | Berkas | Halaman / keadaan | Yang berubah |
|---|---|---|---|
| 01 | `01-login-*` | Halaman masuk | Dua kolom dengan foto kampus Tembalang, logo Undip 72px, label di atas isian, tombol tampilkan kata sandi, tautan ke pendaftaran |
| 02 | `02-login-galat-1280` | Kata sandi salah | Pesan galat dengan ikon, isian ditandai merah |
| 03 | `03-register-*` | Halaman daftar | Kerangka sama dengan login, penanda alur "Isi data → Diperiksa admin → Masuk", isian berpasangan agar form lebih pendek |
| 04 | `04-register-galat-email-terpakai-1280` | Email sudah dipakai | Ringkasan galat di atas + keterangan di bawah isian yang salah |
| 05 | `05-register-berhasil-1280` | Setelah berhasil mendaftar | Pesan sukses berikon di halaman masuk |
| 06 | `06-beranda-pengguna-*` | Beranda pengguna (budi) | Satu aksi utama "Ajukan reservasi", ringkasan angka, reservasi terdekat, laporan terakhir, pintasan berikon, aturan singkat |
| 07 | `07-akses-ditolak-403-*` | budi membuka `/petugas` | Ikon, penjelasan cara pulih, tombol kembali ke beranda |
| 08 | `08-beranda-admin-*` | Beranda admin | Ringkasan angka (kartu verifikasi disorot bila ada pendaftar), daftar pendaftar terlama, status fasilitas, menu berikon |
| 09 | `09-verifikasi-*` | Verifikasi akun | Jumlah menunggu di judul, kolom dirapikan; **di HP tombol Setujui/Tolak kini terlihat tanpa menggeser tabel** |
| 10 | `10-verifikasi-modal-tolak-1280` | Modal tolak | Judul berupa pertanyaan, ringkasan pendaftar, tombol batal bergaya tautan |
| 11 | `11-verifikasi-pesan-sukses-1280` | Setelah menyetujui | Pesan sukses berikon |
| 12 | `12-verifikasi-kosong-*` | Tidak ada pendaftar | Keadaan kosong yang mengarahkan ke Kelola User |
| 13 | `13-dashboard-petugas-*` | Dashboard petugas | Empat kartu angka antrean; kolom antrean mengikuti tinggi isinya |

Elemen bersama yang terlihat di semua halaman: pita kobalt di atas navbar, logo Undip di brand,
penanda menu aktif, badge peran putih bergaris, font Plus Jakarta Sans, dan footer dengan keterangan
"bukan sistem resmi Universitas Diponegoro".

## Catatan saat memeriksa

- Laporan "Proyektor P-01" tertulis *"6 jam dari sekarang"*: seeder baseline memasang jam laporan
  hari ini pukul 08.30, sedangkan seed dijalankan sekitar pukul 02.00 WIB. Hanya terjadi bila seed
  dijalankan sebelum 08.30; bukan bagian dari perubahan polish.
- Di daftar laporan dashboard petugas ada kotak kecil pengganti emoji 📷 dari partial milik P3.
  Muncul karena Chromium yang dipakai memotret tidak punya font emoji; di laptop biasa tampil normal.
- Waktu "x detik yang lalu" pada pendaftar terjadi karena database baru saja di-seed.
