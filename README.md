# Reservus

Sistem Reservasi & Pelaporan Fasilitas Kampus. Pengguna mengecek ketersediaan fasilitas,
mengajukan reservasi, dan melaporkan kerusakan. Petugas memproses keduanya, admin mengelola
akun, fasilitas, dan rekap.

Dibangun dengan Laravel 13, MySQL, dan Bootstrap 5.

> Dokumen lain: alur kerja tim ada di `README-Workflow-Reservus.md`, laporan tiap bagian ada di
> folder `docs/workflow/`. Konteks untuk AI agent (`AGENTS.md`) dibagikan terpisah oleh PM,
> tidak ikut di repositori ini.

---

## 1. Yang perlu dipasang lebih dulu

| Kebutuhan | Versi minimal | Cara cek |
|---|---|---|
| PHP | 8.3 | `php -v` |
| Composer | 2.x | `composer -V` |
| Node.js + npm | 20 | `node -v` dan `npm -v` |
| MySQL | 8.x | `mysql --version` |
| Git | apa saja | `git --version` |

### Windows

Cara paling gampang: pasang **[Laragon](https://laragon.org/download/)** edisi Full. Di dalamnya
sudah ada PHP, MySQL, dan Composer sekaligus. Setelah dipasang:

1. Buka Laragon, klik **Start All** (Apache/Nginx dan MySQL menyala).
2. Pastikan PHP-nya 8.3 ke atas: menu **Menu → PHP → Version**. Kalau masih di bawah itu,
   unduh versi baru lewat **Menu → Tools → Quick add → PHP**.
3. Node.js dipasang terpisah dari [nodejs.org](https://nodejs.org) (pilih versi LTS).
4. Semua perintah di panduan ini dijalankan lewat **Terminal bawaan Laragon** (tombol *Terminal*),
   bukan CMD biasa, supaya `php` dan `composer` langsung dikenali.

Alternatif lain: XAMPP (PHP + MySQL) ditambah Composer dan Node.js yang dipasang sendiri.

### Linux (Ubuntu / Pop!_OS / Mint)

```bash
sudo apt update
sudo apt install -y php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-zip \
                    php8.3-curl php8.3-intl php8.3-gd unzip git mysql-server
sudo apt install -y nodejs npm
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo systemctl enable --now mysql
```

Kalau paket `php8.3` belum tersedia di distromu, tambahkan dulu repositori Ondřej:
`sudo add-apt-repository ppa:ondrej/php && sudo apt update`.

---

## 2. Menyiapkan proyek (sekali saja)

Langkah 1–7 sama persis di Windows maupun Linux.

**1. Ambil kodenya**

```bash
git clone https://github.com/PPK-WP/Reservus.git
cd Reservus
```

**2. Pasang kebutuhan PHP dan JavaScript**

```bash
composer install
npm install
```

**3. Siapkan berkas pengaturan**

Linux / macOS / Terminal Laragon:
```bash
cp .env.example .env
```

Windows (CMD):
```cmd
copy .env.example .env
```

**4. Buat kunci aplikasi**

```bash
php artisan key:generate
```

**5. Buat database bernama `reservus`**

Lewat terminal:
```bash
mysql -u root -e "CREATE DATABASE reservus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Di Windows dengan Laragon, cara termudah: klik kanan ikon Laragon → **MySQL → phpMyAdmin**,
lalu buat database baru bernama `reservus` dengan collation `utf8mb4_unicode_ci`.

Kalau MySQL-mu memakai kata sandi, sesuaikan baris ini di berkas `.env`:

```
DB_DATABASE=reservus
DB_USERNAME=root
DB_PASSWORD=
```

**6. Isi tabel dan data contoh**

```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

**7. Bangun tampilan**

```bash
npm run build
```

---

## 3. Menjalankan aplikasi

```bash
php artisan serve
```

Buka **http://localhost:8000/login** di browser.

> Halaman depan (`/`) mengarah ke daftar fasilitas yang **belum dikerjakan**, jadi untuk sekarang
> masih menampilkan 404. Itu normal sampai bagian katalog fasilitas selesai. Mulailah dari `/login`.

Kalau sedang mengubah tampilan dan ingin perubahannya langsung terlihat tanpa build ulang,
jalankan ini di terminal kedua:

```bash
npm run dev
```

### Akun untuk mencoba

Kata sandi semuanya: **`password`**

| Email | Peran | Berguna untuk mencoba |
|---|---|---|
| `admin@kampus.test` | Admin | verifikasi akun, kelola user, kelola fasilitas, rekap |
| `petugas@kampus.test` | Petugas | dashboard petugas, antrean reservasi & laporan |
| `budi@kampus.test` | Pengguna | pemilik sebagian besar data contoh |
| `citra@kampus.test` | Pengguna | data milik orang lain, untuk menguji pembatasan akses |
| `dimas@kampus.test` | Pengguna | pasangan jadwal bentrok |
| `pending@kampus.test` | Pengguna | akun yang menunggu persetujuan admin |
| `ditolak@kampus.test` | Pengguna | akun yang ditolak beserta catatannya |

Data contoh juga berisi 8 fasilitas, 15 reservasi, dan 9 laporan yang tanggalnya selalu
menyesuaikan hari kamu menjalankan `migrate:fresh --seed`.

---

## 4. Perintah yang sering dipakai

| Keperluan | Perintah |
|---|---|
| Kembalikan data ke kondisi awal | `php artisan migrate:fresh --seed` |
| Jalankan aplikasi | `php artisan serve` |
| Bangun tampilan | `npm run build` |
| Mode tampilan langsung berubah | `npm run dev` |
| Jalankan pengujian otomatis | `php artisan test` |
| Lihat semua alamat halaman | `php artisan route:list` |
| Bersihkan cache bila ada yang aneh | `php artisan optimize:clear` |

---

## 5. Kalau ada masalah

| Gejala | Penyebab yang paling sering | Cara mengatasi |
|---|---|---|
| `SQLSTATE[HY000] [1049] Unknown database 'reservus'` | database belum dibuat | ulangi langkah 5 di bagian penyiapan |
| `SQLSTATE[HY000] [1045] Access denied for user 'root'` | kata sandi MySQL tidak cocok | sesuaikan `DB_USERNAME` dan `DB_PASSWORD` di `.env` |
| `could not find driver` | ekstensi MySQL untuk PHP belum aktif | Windows: aktifkan `extension=pdo_mysql` di `php.ini` lalu restart Laragon · Linux: `sudo apt install php8.3-mysql` |
| Halaman tampil polos tanpa warna | tampilan belum dibangun | `npm run build` |
| `Vite manifest not found` | sama seperti di atas | `npm run build` |
| Halaman `/facilities`, `/reservations`, `/reports`, `/admin/facilities` menampilkan 404 | bagian itu memang belum dikerjakan | tunggu sampai bagian terkait selesai dan digabungkan |
| Perubahan kode tidak terasa | cache lama | `php artisan optimize:clear` |
| Port 8000 sudah dipakai | ada server lain berjalan | `php artisan serve --port=8001` |

---

## 6. Alur kerja tim secara singkat

```bash
# mulai bagian baru, selalu dari main terbaru
git checkout main && git pull origin main
git checkout -b feature/nama-bagian

# selama bekerja
git add <berkas>
git commit -m "Ringkasan singkat pekerjaan (SRS-00X)"

# sebelum membuka PR, ambil perubahan terbaru dan uji ulang
git fetch origin && git merge origin/main
php artisan migrate:fresh --seed

git push -u origin feature/nama-bagian   # lalu buka Pull Request ke main di GitHub
```

Aturan penting: **kerjakan hanya berkas milik bagianmu**. Jangan mengubah migration, model,
seeder, service, middleware, layout, atau `routes/web.php` — semuanya milik baseline. Kalau
butuh perubahan di sana, sampaikan ke PM. Rinciannya ada di `README-Workflow-Reservus.md`.
