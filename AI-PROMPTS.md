# 🤖 AI-PROMPTS — Reservus (Revisi 2)
### Prompt untuk Claude Code · Antigravity · OpenCode · Hermes

> Pasangan dokumen: `README-Workflow-Reservus.md` (spesifikasi lengkap) dan
> `docs/workflow/SRS-00X-TEMPLATE.md` (template laporan per SRS). Ketiganya wajib ada di root repo
> **sebelum** PM menjalankan prompt baseline.

---

## Cara Pakai

1. **PM** menyalin `README-Workflow-Reservus.md`, `AI-PROMPTS.md`, dan `docs/workflow/SRS-00X-TEMPLATE.md`
   ke folder proyek, lalu menjalankan **Prompt 1 (SRS-001)** + menempelkan **Bagian 0 (Master Prompt)**
   di bawahnya. Baseline menyimpan Bagian 0 sebagai **`AGENTS.md`** di root repo.
2. Setelah baseline di-push, tiap anggota: `git pull` → buat branch → buka agent di **root repo** →
   kirim **prompt role** miliknya. Baris pertama setiap prompt role sudah memerintahkan agent
   membaca `AGENTS.md`, jadi konteks termuat di tool mana pun.
3. Untuk AI chat tanpa akses repo (ChatGPT, Gemini web, dll.): kirim **isi `AGENTS.md` + prompt role**
   dalam satu pesan dan minta keluaran berupa path + isi file lengkap.
4. Verifikasi konteks termuat: tanyakan ke agent *"Sebutkan 3 aturan scope dan pemilik file
   `routes/web.php` menurut AGENTS.md."* Jawaban salah → minta agent membaca ulang `AGENTS.md`.

### Satu File Konteks: `AGENTS.md` (D-10)

Proyek ini memakai **satu file konteks saja**, yaitu `AGENTS.md` di root repo. Tidak ada `CLAUDE.md`,
`GEMINI.md`, atau `.agents/rules/`, sehingga tidak ada risiko isinya berbeda satu sama lain.

| Agent | Cara `AGENTS.md` termuat |
|---|---|
| OpenCode | dibaca otomatis |
| Antigravity | CLI membaca otomatis; di IDE Antigravity 2.0 termuat lewat baris pertama prompt role |
| Hermes | dibaca sebagai context file; baris pertama prompt role sebagai jaminan |
| Claude Code | termuat lewat baris pertama prompt role ("Baca AGENTS.md …") |

Nama `AGENTS.md` dipertahankan (bukan nama bebas seperti `PROJECT.md`) karena ini standar lintas-tool:
sebagian agent membacanya otomatis, dan agent lain tetap membacanya karena diperintah di prompt.
Nama bebas justru tidak dibaca otomatis oleh tool mana pun.
Hanya PM yang boleh mengubah `AGENTS.md`; perubahannya di-commit ke `main` sebagai `docs(baseline): …`
dan anggota menarik `main` ke branch-nya.

---

## 0. MASTER PROMPT (= isi AGENTS.md)

> Salin **isi** blok di bawah (tanpa pagar ```` ```text ````).

```text
# Reservus — KONTEKS PROYEK UNTUK AGENTIC AI
# Satu-satunya file konteks agent proyek ini (D-10). Hanya PM yang boleh mengubah.
# Spesifikasi lengkap: README-Workflow-Reservus.md (bagian 2, 6, 7, 9, 12). Prompt per SRS: AI-PROMPTS.md.

## A. PERAN & CARA KERJA
Kamu adalah senior full-stack developer Laravel 13 + MySQL Server 9.5 (PHP 8.3+) yang bekerja
LANGSUNG di repository ini sebagai agent: membaca kode, mengedit file, menjalankan perintah,
menguji, dan commit. Tim: 4 mahasiswa, deadline ketat. Prioritas: fitur berfungsi & aman >
sempurna. UI rapi = nilai tambah.
- Konvensi Laravel 11+ (struktur ramping): tanpa Kernel.php; middleware & alias di
  bootstrap/app.php; casts() sebagai method; policy auto-discovery (app/Policies/<Model>Policy).
- Base Controller Laravel 11+ TIDAK punya middleware()/authorize(): gunakan middleware di route
  dan Gate::authorize('ability', $model) untuk otorisasi.
- Tampilan: Blade + Bootstrap 5 (dari laravel/ui, via Vite) + JavaScript vanilla ringan.
  DILARANG Livewire, Inertia, React, Vue, Tailwind, atau library CSS/JS baru.
- Referensi UX: Skedda/Robin (grid ketersediaan: chip hijau/abu), Calendly/Google Calendar
  (pilih tanggal → pilih slot 30 menit), Booking.com (filter bar, kartu, badge status berwarna).
- Bahasa UI, pesan validasi, dan pesan flash: Bahasa Indonesia.

## B. PROYEK & ATURAN BISNIS
Reservus — Sistem Reservasi & Pelaporan Fasilitas Kampus. Aktor: Pengunjung (tanpa login),
Pengguna (mahasiswa/dosen/staf), Petugas (dibuat Admin), Admin.
1. Jam operasional 07:00–20:00, 26 slot tetap @30 menit. start/end wajib dalam jam operasional &
   kelipatan 30 menit — VALIDASI SERVER (otoritatif), client hanya UX.
2. Bentrok = tumpang tindih (start_lama < end_baru AND end_lama > start_baru) dengan reservasi
   'disetujui' pada fasilitas & tanggal sama. Bersinggungan (10:30 dengan 10:30) bukan bentrok.
   Dicek saat PENGAJUAN dan DIULANG saat APPROVAL dalam DB::transaction + lockForUpdate().
3. Reservasi 'menunggu' tidak mengunci slot (publik tetap melihat 'tersedia').
4. Tanggal reservasi: hari ini s.d. +30 hari; hari ini → jam mulai harus setelah sekarang.
5. Hanya fasilitas 'aktif' yang bisa direservasi — dicek di server, bukan hanya dropdown.
6. Pengguna batal: miliknya, status menunggu/disetujui, paling lambat 2 jam sebelum mulai (WIB).
   Petugas batal mendesak: hanya 'disetujui', alasan WAJIB. Petugas tolak: alasan WAJIB.
7. Laporan: baru → diproses → selesai/ditolak; baru → ditolak. Menutup (selesai/ditolak) WAJIB
   catatan resolusi. Foto opsional jpg/jpeg/png maks 2 MB.
8. Petugas hanya mengubah status fasilitas aktif ↔ dalam_perbaikan. 'nonaktif' hanya Admin.
9. Registrasi mandiri: role 'pengguna', status 'pending', TANPA login otomatis; Admin
   menyetujui (aktif) atau menolak (ditolak + catatan). Petugas TIDAK PERNAH registrasi mandiri.
10. Login hanya untuk status 'aktif'.
11. Pengunjung melihat ketersediaan TANPA nama pemohon/tujuan (privasi US 1).
12. Semua waktu WIB (APP_TIMEZONE=Asia/Jakarta).

## C. KEPUTUSAN TERKUNCI
D-01 Starter kit laravel/ui (ui bootstrap --auth). Bukan Breeze.
D-02 Folder tampilan = /views di root (config/view.php → base_path('views')). JANGAN menulis ke resources/views.
D-03 Satu file route per SRS di routes/, di-require dari routes/web.php.
D-04 APP_TIMEZONE=Asia/Jakarta.
D-05 Kolom TIME dibaca/ditulis sebagai string 'H:i' (tanpa cast datetime); pakai
     AvailabilityService::normalizeTime().
D-06 role, status, dan kolom pemrosesan TIDAK di $fillable — diisi eksplisit di controller.
D-07 Reset password & verifikasi email dimatikan.
D-08 Setiap SRS wajib menghasilkan docs/workflow/SRS-00X-<slug>.md dari template.
D-09 PR di-merge dengan merge commit (bukan squash).
D-10 Satu file konteks agent: AGENTS.md. Jangan membuat CLAUDE.md, GEMINI.md, atau .agents/rules/.

## D. SKEMA DATABASE (sudah ada — DILARANG membuat/mengubah migration & model)
users(id, name, email unik, password, role ENUM('admin','petugas','pengguna') def 'pengguna',
  status ENUM('aktif','pending','ditolak') def 'aktif', user_type ENUM('mahasiswa','dosen','staf') null,
  identity_number varchar(30) null, verification_note text null, verified_at timestamp null)
facilities(id, name, type ENUM('ruang_kelas','aula','laboratorium','alat','lapangan'),
  location varchar(100), capacity unsigned int, description text null,
  status ENUM('aktif','dalam_perbaikan','nonaktif') def 'aktif')
reservations(id, user_id FK, facility_id FK restrict, reservation_date date, start_time time,
  end_time time, purpose text, status ENUM('menunggu','disetujui','ditolak','dibatalkan')
  def 'menunggu', rejection_reason text null, cancel_reason text null, cancelled_by FK users null,
  cancelled_at null, processed_by FK users null, processed_at null;
  index(facility_id, reservation_date, status))
reports(id, user_id FK, facility_id FK restrict, category ENUM('kerusakan','kebersihan',
  'peralatan','lainnya'), description text, photo varchar null, status ENUM('baru','diproses',
  'selesai','ditolak') def 'baru', resolution_note text null, processed_by FK users null,
  processed_at null; index(facility_id, status))

## E. KONTRAK INTERFACE (sudah ada di baseline — PAKAI, jangan ubah/tulis ulang)
E1. App\Services\AvailabilityService
    OPEN='07:00' CLOSE='20:00' SLOT_MINUTES=30 CANCEL_DEADLINE_MINUTES=120 MAX_DAYS_AHEAD=30
    slots(): 26 × ['start'=>'07:00','end'=>'07:30','label'=>'07.00–07.30']
    startOptions(): '07:00'..'19:30'   endOptions(): '07:30'..'20:00'
    slotStatuses(Facility, $date): 26 slot + 'status' => 'tersedia'|'tidak_tersedia'
    availableCount(Facility, $date): int
    validateRange($date, $start, $end): array pesan error ([] = valid)
    hasConflict($facilityId, $date, $start, $end, $ignoreId = null): bool
    canUserCancel(Reservation, User): bool
    normalizeTime($time): 'H:i'
E2. Model
    User: ROLES, USER_TYPES; isAdmin(), isPetugas(), isPengguna(), isActive(); reservations(), reports()
    Facility: TYPES, STATUSES; isReservable(); scopePublicCatalog() (aktif + dalam_perbaikan);
      reservations(), reports()
    Reservation: STATUSES; user(), facility(), processor(), canceller(); scopeApproved(),
      scopeStatus($s); timeRange() → '09.00–10.30'; startsAt() → Carbon WIB; casts reservation_date → date
    Report: CATEGORIES, STATUSES; user(), facility(), processor(); scopeStatus($s); photoUrl()
    Konstanta = array value ⇒ label Indonesia. Pakai untuk dropdown & validasi Rule::in(array_keys(...)).
E3. Route: tulis HANYA di file route milik SRS-mu. Nama & URL sudah disepakati:
    auth-akun.php        (SRS-002) admin.users.* , admin.verifications.*
    fasilitas-katalog.php(SRS-003) facilities.index, facilities.show
    fasilitas-admin.php  (SRS-004) admin.facilities.* , admin.recap.index, admin.recap.export
    reservasi-user.php   (SRS-005) reservations.index/create/store/show/cancel
    reservasi-petugas.php(SRS-006) petugas.reservations.index/approve/reject/cancel
    laporan-user.php     (SRS-007) reports.index/create/store/show
    laporan-petugas.php  (SRS-008) petugas.reports.index/show/status/facility-status
    Detail URL & method: README §7.3.
E4. Tampilan
    @extends('layouts.app') + @section('content'). Komponen: <x-status-badge :status="..."/>,
    <x-flash/> (sudah di layout). Partial dashboard petugas (dipanggil shell via @includeIf):
    petugas.partials.reservation-queue (SRS-006) · petugas.partials.report-queue (SRS-008).
    Partial boleh query sendiri (Eloquent ringan, eager load, maks 10 baris).
E5. Middleware alias 'role': role:admin | role:petugas | role:pengguna | role:admin,petugas.
E6. Navigasi & tautan antar-fitur memakai URL LITERAL, bukan route() milik SRS lain:
    /facilities · /reservations · /reservations/create?facility={id}&date={Y-m-d} · /reports ·
    /reports/create?facility={id} · /petugas · /petugas/reservations · /petugas/reports ·
    /admin/users · /admin/verifications · /admin/facilities · /admin/recap · /home
E7. Seeder fixture (README §7.7): akun admin@, petugas@, budi@, citra@, dimas@, pending@,
    ditolak@ (@kampus.test, password 'password'); 8 fasilitas; reservasi RSV-1..9 + RSV-H1..H6;
    laporan LPR-1..6 + LPR-H1..H3. Uji fiturmu dengan data ini; tambah data lewat UI fiturmu
    sendiri bila perlu. JANGAN mengubah seeder.

## F. KEPEMILIKAN FILE
PM (baseline, beku): database/*, app/Models/*, app/Services/*, app/Http/Middleware/*,
  bootstrap/app.php, routes/web.php, views/layouts/*, views/components/*, views/home/*,
  views/errors/*, views/petugas/dashboard.blade.php, HomeController, Petugas/DashboardController,
  package.json, vite.config.js, resources/*, config/*, .env.example, AGENTS.md, .github/*,
  docs/workflow/SRS-00X-TEMPLATE.md.
PM (SRS-002): routes/auth-akun.php, app/Http/Controllers/Auth/*, Admin/UserController,
  Admin/VerificationController, views/auth/*, views/admin/users/**, views/admin/verifications/**.
P1 (SRS-003): routes/fasilitas-katalog.php, FacilityCatalogController, views/facilities/**.
P1 (SRS-004): routes/fasilitas-admin.php, Admin/FacilityController, Admin/RecapController,
  app/Exports/*, views/admin/facilities/**, views/admin/recap/**, composer.json, composer.lock,
  config/excel.php & config/dompdf.php (bila dipublish).
P2 (SRS-005): routes/reservasi-user.php, ReservationController, app/Policies/ReservationPolicy.php,
  views/reservations/**.
P2 (SRS-006): routes/reservasi-petugas.php, Petugas/ReservationController,
  views/petugas/reservations/**, views/petugas/partials/reservation-queue.blade.php.
P3 (SRS-007): routes/laporan-user.php, ReportController, app/Policies/ReportPolicy.php,
  views/reports/**.
P3 (SRS-008): routes/laporan-petugas.php, Petugas/ReportController, views/petugas/reports/**,
  views/petugas/partials/report-queue.blade.php.
Setiap SRS: docs/workflow/SRS-00X-<slug>.md dan docs/screenshots/SRS-00X-*.png miliknya sendiri.

## G. ATURAN SCOPE
1. Kerjakan HANYA SRS yang disebut di prompt. Sentuh HANYA file milik SRS itu (bagian F).
2. DILARANG membuat/mengubah migration, model, seeder, service, middleware, layout, web.php,
   package.json, AGENTS.md, atau file milik SRS lain.
3. Kebutuhan di luar scope → JANGAN diimplementasikan. Tulis di laporan akhir dan dokumen workflow:
   ### HANDOFF UNTUK PM
   - Kebutuhan: ...  - File terkait: ...  - Alasan: ...  - Dampak bila tidak ada: ...
4. Jangan menambah dependensi (composer/npm) kecuali SRS-004 untuk paket ekspor.
5. Validasi form penting SELALU dua sisi: server (Validator/Rule/service) + client (HTML5 + JS ringan).
6. Sebelum menulis kode, tampilkan rencana singkat (daftar file yang akan dibuat/diubah).

## H. KEAMANAN WAJIB (diaudit sebelum H-7)
- Otorisasi pemilik data via Policy + Gate::authorize() (IDOR). Semua route non-publik memakai role:.
- Jangan pernah create/update dengan $request->all(); pakai $request->validated() + pengisian
  eksplisit untuk role/status/kolom proses/user_id.
- Rule::in(array_keys(Model::KONSTANTA)) untuk semua enum.
- Blade: selalu {{ }}; teks multibaris {!! nl2br(e($teks)) !!}; tidak ada {!! $input !!}.
- @csrf di semua form; @method('PATCH'/'PUT') untuk method spoofing.
- Upload: image|mimes:jpg,jpeg,png|max:2048, simpan dengan ->store() (nama acak), disk public.
- Aksi pengubah status memakai whitelist transisi di server.
- Halaman publik tidak pernah memuat nama pemohon/tujuan.
- Ekspor CSV/XLSX: escape sel yang diawali = + - @ (tambahkan ' di depan).

## I. PROTOKOL KERJA SETIAP SRS (WAJIB diikuti berurutan)
1. git checkout main && git pull origin main && git checkout -b <branch-SRS>
   (jika branch sudah ada: git checkout <branch> && git merge origin/main).
2. Baca SRS di AI-PROMPTS.md dan bagian terkait README. Tampilkan rencana singkat.
3. Implementasi hanya di file milik SRS.
4. Uji: php artisan migrate:fresh --seed, lalu jalankan SETIAP skenario acceptance (manual lewat
   php artisan serve dan/atau feature test). Catat hasil aktualnya.
5. Commit kecil per langkah: <tipe>(<domain>): <ringkasan> (SRS-00X, US-n).
   tipe: feat|fix|docs|style|refactor|test|chore · domain: baseline|akun|fasilitas|reservasi|laporan.
6. DOKUMEN WORKFLOW (WAJIB): salin docs/workflow/SRS-00X-TEMPLATE.md menjadi
   docs/workflow/SRS-00X-<slug>.md dan isi SEMUA bagian: ringkasan, cakupan US, file yang
   dibuat/diubah (git diff --name-status origin/main...HEAD), route (php artisan route:list),
   flowchart Mermaid alur yang BENAR-BENAR diimplementasikan, validasi server/client, keamanan,
   tabel uji dengan hasil aktual, placeholder screenshot, kendala & solusi, HANDOFF, riwayat
   commit, poin presentasi. Commit: docs(<domain>): workflow SRS-00X.
7. Laporan akhir ke anggota: ringkasan, daftar file, hasil uji (lulus/gagal per skenario),
   HANDOFF bila ada, dan perintah push + membuka PR.
8. JANGAN push ke main dan JANGAN merge sendiri (kecuali PM untuk SRS-001).
```

---

## 1. PROMPT PM — SRS-001 BASELINE KRITIS (Fase 1 · langsung di `main`)

> Kirim prompt ini **diikuti Bagian 0** dalam satu pesan (`AGENTS.md` belum ada saat mulai).

```text
PERANKU: PM · SRS-001 Baseline Kritis & Fondasi · branch main.
Kamu membangun fondasi yang membuat 7 SRS lain bisa dikerjakan PARALEL tanpa menunggu siapa pun.
Kerjakan langsung di repo, jalankan perintah, verifikasi, dan commit per langkah.
Konteks lengkap: MASTER PROMPT di bawah + README-Workflow-Reservus.md (§0, §2, §6, §7, §9.1).

LANGKAH
0. Pastikan README-Workflow-Reservus.md, AI-PROMPTS.md, dan docs/workflow/SRS-00X-TEMPLATE.md
   sudah ada di root proyek (jangan diubah isinya).
1. PROYEK & DATABASE
   composer create-project laravel/laravel reservus → pastikan Laravel 13 & PHP ≥ 8.3.
   .env: APP_NAME=Reservus, APP_TIMEZONE=Asia/Jakarta, APP_LOCALE=id, DB_CONNECTION=mysql,
   DB_HOST=127.0.0.1, DB_PORT=3306, DB_DATABASE=reservus, DB_USERNAME=root, DB_PASSWORD=
   Samakan .env.example (tanpa secret). Pastikan config/app.php membaca APP_TIMEZONE.
   CREATE DATABASE reservus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
2. STARTER KIT (D-01)
   composer require laravel/ui → php artisan ui bootstrap --auth → npm install → npm run build.
   routes/web.php: Auth::routes(['reset' => false, 'verify' => false, 'confirm' => false]).
   Controller Auth: $redirectTo = '/home'.
   Jika muncul "Call to undefined method ...::middleware()" (base Controller Laravel 11+ tidak
   punya method middleware), perbaiki controller hasil laravel/ui dengan interface
   Illuminate\Routing\Controllers\HasMiddleware + static middleware(), atau pindahkan
   middleware ke definisi route. Pastikan throttle login bawaan tetap aktif.
   Hapus welcome.blade.php & route welcome.
3. FOLDER VIEWS (D-02)
   php artisan config:publish view → 'paths' => [base_path('views')].
   Pindahkan seluruh isi resources/views ke /views, hapus resources/views, php artisan view:clear.
   Pastikan /login dan /register tampil.
4. MIGRATION — persis MASTER bagian D. Kolom tambahan users lewat migration baru
   (add_role_status_to_users_table). facility_id restrictOnDelete; user_id cascadeOnDelete;
   processed_by/cancelled_by nullOnDelete; index sesuai skema.
5. MODEL — persis MASTER bagian E2 & D-05/D-06:
   konstanta value ⇒ label Indonesia (ROLES, USER_TYPES, TYPES, STATUSES, CATEGORIES),
   relasi, helper, scope, casts() (reservation_date → 'date'; password → 'hashed';
   verified_at → 'datetime'; kolom TIME TIDAK di-cast), $fillable sesuai README §7.2.
   Reservation::startsAt() menggabungkan reservation_date + start_time dalam Asia/Jakarta.
   Report::photoUrl() → Storage::url($this->photo) atau null.
6. SERVICE — app/Services/AvailabilityService.php persis MASTER E1 (docblock tiap method).
   Tambah tests/Feature/AvailabilityServiceTest.php (RefreshDatabase):
   26 slot; startOptions/endOptions 26 nilai; 07:15 invalid; 19:30–20:30 invalid; start ≥ end
   invalid; tanggal lampau & > 30 hari invalid; 09:00–10:30 vs 10:30–11:00 TIDAK bentrok;
   09:00–10:30 vs 10:00–11:00 bentrok; reservasi 'menunggu' tidak dihitung bentrok; ignoreId
   bekerja; fasilitas dalam_perbaikan → 26 'tidak_tersedia'; canUserCancel benar untuk
   pemilik/bukan pemilik/kurang dari 2 jam.
   Bila sqlite in-memory tidak kompatibel dengan ENUM/TIME, arahkan phpunit.xml ke MySQL
   database reservus_test.
7. MIDDLEWARE — app/Http/Middleware/CheckRole.php: handle($request, $next, string ...$roles):
   tamu → redirect('/login'); status ≠ aktif → Auth::logout() + invalidate session + redirect
   login dengan pesan; role tidak termasuk → abort(403). Daftarkan alias 'role' di
   bootstrap/app.php (withMiddleware).
8. ROUTES (D-03) — routes/web.php:
   Route::redirect('/', '/facilities'); Auth::routes([...]);
   GET /home (auth) → HomeController (invokable) name 'home';
   GET /petugas (auth, role:petugas) → Petugas\DashboardController name 'petugas.dashboard';
   lalu require __DIR__.'/<file>.php' untuk 7 file: auth-akun, fasilitas-katalog,
   fasilitas-admin, reservasi-user, reservasi-petugas, laporan-user, laporan-petugas.
   Setiap file dibuat dengan header:
   <?php
   // PEMILIK: <peran> · SRS-00X · branch <branch> — HANYA pemilik yang boleh mengedit file ini.
   use Illuminate\Support\Facades\Route;
   (belum berisi route).
9. TAMPILAN BERSAMA (folder /views)
   - layouts/app.blade.php (dari laravel/ui): navbar Bootstrap responsif dengan URL LITERAL per
     role persis README §7.6; dropdown nama + Logout (form POST /logout + @csrf); <x-flash/>
     di atas @yield('content'); footer kecil. Semua view memakai @extends('layouts.app').
   - components/status-badge.blade.php: memetakan SEMUA status ke warna & label (README §7.4).
   - components/flash.blade.php: session('success'), session('error'), ringkasan $errors.
   - HomeController: petugas → redirect('/petugas'); admin → view home.admin (kartu tautan cepat:
     Verifikasi + jumlah akun pending, Kelola User, Kelola Fasilitas, Rekap); pengguna → view
     home.pengguna (kartu: Cari Fasilitas, Ajukan Reservasi, Laporkan Kerusakan, Reservasi Saya,
     Laporan Saya).
   - Petugas\DashboardController + petugas/dashboard.blade.php: judul "Dashboard Petugas",
     dua kolom (Reservasi Menunggu | Laporan Masuk). Tiap kolom:
     @if(view()->exists('petugas.partials.reservation-queue'))
         @include('petugas.partials.reservation-queue')
     @else <div class="alert alert-light">Modul belum terpasang.</div> @endif
     (idem untuk report-queue).
   - errors/403.blade.php ramah pengguna (tombol kembali ke /home).
10. SEEDER — persis README §7.7 (akun, fasilitas, RSV-1..9, RSV-H1..H6, LPR-1..6, LPR-H1..H3).
    Tanggal DINAMIS dari now('Asia/Jakarta'). RSV-7: mulai = kelipatan 30 menit terdekat setelah
    now()+60 menit, durasi 30 menit, tidak melewati tengah malam (boleh di luar jam operasional —
    ini fixture). Data historis: created_at/updated_at disesuaikan tanggalnya. processed_by =
    petugas untuk data yang sudah diproses; cancelled_by = budi untuk RSV-9.
    DatabaseSeeder memanggil: UserSeeder, FacilitySeeder, ReservationSeeder, ReportSeeder.
11. php artisan storage:link.
12. FILE KONTEKS & REPO
    - AGENTS.md di root → isi = MASTER PROMPT (tanpa pagar ```text). HANYA file ini — jangan
      membuat CLAUDE.md, GEMINI.md, atau .agents/rules/ (D-10).
    - .github/pull_request_template.md: checklist PR README §11.
    - Folder docs/workflow/ (sudah berisi template) dan docs/screenshots/.gitkeep.
    - .gitignore standar Laravel (vendor, node_modules, .env, public/storage, public/build).
13. VERIFIKASI (catat hasilnya di dokumen workflow)
    migrate:fresh --seed sukses dan jumlah data sesuai fixture · php artisan test hijau ·
    login admin/petugas/budi berhasil, navbar sesuai role · budi → /petugas = 403 ·
    tamu → /home = redirect login · /login & /register ber-Bootstrap · logout berfungsi ·
    php artisan route:list tanpa error · /facilities 404 (WAJAR — milik SRS-003).
14. GIT — commit bertahap: chore(baseline)/feat(baseline) ... (SRS-001). Push main.
    Undang 3 anggota. Aktifkan proteksi branch main (wajib PR) bila memungkinkan.
15. OUTPUT WAJIB: docs/workflow/SRS-001-baseline.md dari template (uji = acceptance README §9.1).

JANGAN membuat fitur katalog, reservasi, laporan, atau halaman admin user/fasilitas/rekap —
itu milik SRS lain.
```

---

## 2. PROMPT PM — SRS-002 AUTENTIKASI & MANAJEMEN AKUN (`feature/auth-akun`)

```text
LANGKAH PERTAMA: baca AGENTS.md di root repo sampai habis dan patuhi seluruh isinya.
PERANKU: PM · SRS-002 Autentikasi & Manajemen Akun · branch feature/auth-akun · US 13, 14, 15.
Ikuti AGENTS.md (terutama G, H, I). Acuan: README §9.2.
FILE MILIKKU: routes/auth-akun.php · app/Http/Controllers/Auth/* · app/Http/Controllers/Admin/
UserController.php · Admin/VerificationController.php · views/auth/* · views/admin/users/** ·
views/admin/verifications/** · docs/workflow/SRS-002-auth-akun.md

LANGKAH
1. REGISTRASI MANDIRI: tambah field user_type (select dari User::USER_TYPES, wajib) dan
   identity_number (NIM/NIP, opsional, maks 30) di views/auth/register.blade.php + validator
   RegisterController (client: required, type=email, minlength, maxlength).
   create(): isi name/email/password/user_type/identity_number, lalu set role='pengguna' dan
   status='pending' SECARA EKSPLISIT — jangan pernah membaca role/status dari request.
   Override register(): JANGAN login otomatis → redirect('/login') dengan pesan
   "Pendaftaran berhasil. Akun Anda menunggu verifikasi admin."
2. LOGIN GATING di LoginController: kredensial valid tetapi status 'pending' → logout + kembali
   ke login dengan error "Akun Anda menunggu verifikasi admin."; 'ditolak' → "Pendaftaran Anda
   ditolak." + verification_note bila ada. Throttle & regenerasi session bawaan tetap aktif.
3. ROUTES di routes/auth-akun.php — group middleware ['auth','role:admin'], prefix 'admin',
   name 'admin.':
   GET  /admin/users                          users.index   (tabel akun + filter role/status + badge)
   GET  /admin/users/create                   users.create
   POST /admin/users                          users.store
        validasi: name wajib; email unik; password confirmed min 8; role Rule::in(['petugas',
        'pengguna']) — 'admin' ditolak walau disisipkan; user_type required_if role=pengguna;
        identity_number opsional. Simpan status 'aktif', verified_at=now().
   GET  /admin/verifications                  verifications.index (pending, terlama dulu)
   PATCH /admin/verifications/{user}/approve  verifications.approve → aktif, verified_at,
        verification_note=null
   PATCH /admin/verifications/{user}/reject   verifications.reject → ditolak +
        verification_note wajib (maks 500)
   Approve/reject hanya untuk user berstatus 'pending'; selain itu kembali dengan error.
4. VIEWS Bootstrap: tabel, <x-status-badge>, confirm() untuk aksi, modal catatan penolakan,
   empty state "Tidak ada akun menunggu verifikasi".

ACCEPTANCE (uji semua, catat hasil aktual di dokumen workflow)
1. Daftar akun baru → tidak login otomatis → pesan menunggu verifikasi; di DB: pengguna/pending.
2. Login akun baru & pending@ → ditolak "menunggu verifikasi"; ditolak@ → pesan ditolak + catatan.
3. Admin setujui akun baru → login sukses → /home pengguna.
4. Admin tolak pending@ dengan catatan → login menampilkan catatan.
5. Admin buat petugas baru → login → diarahkan ke /petugas.
6. Sisipkan input role=admin & status=aktif di form register (DevTools) → tetap pengguna/pending.
7. POST /admin/users dengan role=admin → ditolak validasi.
8. budi → /admin/users = 403; tamu → redirect login. Tidak ada jalur publik membuat petugas.
OUTPUT WAJIB: kode + commit + docs/workflow/SRS-002-auth-akun.md.
Catatan PM: perbaikan file baseline dilakukan terpisah di main sebagai fix(baseline), bukan di branch ini.
```

---

## 3. PROMPT P1 — SRS-003 KATALOG & KETERSEDIAAN (`feature/fasilitas-katalog`)

```text
LANGKAH PERTAMA: baca AGENTS.md di root repo sampai habis dan patuhi seluruh isinya.
PERANKU: Programmer 1 · SRS-003 Katalog & Ketersediaan Fasilitas · branch
feature/fasilitas-katalog · US 1, 2. Ikuti AGENTS.md (G, H, I). Acuan: README §9.3.
Arah UX: Booking.com (filter bar + kartu) dan Skedda (grid ketersediaan).
FILE MILIKKU: routes/fasilitas-katalog.php · app/Http/Controllers/FacilityCatalogController.php ·
views/facilities/** · docs/workflow/SRS-003-fasilitas-katalog.md

LANGKAH
1. ROUTES publik (TANPA auth): GET /facilities → facilities.index; GET /facilities/{facility}
   → facilities.show.
2. index: Facility::publicCatalog() (nonaktif TIDAK tampil) + filter dengan when():
   type (Rule::in TYPES), location (select dari daftar lokasi distinct), min_capacity (integer ≥ 1),
   q (kata kunci nama, escape wildcard % dan _). Urut nama, paginate 12 + withQueryString().
   Kartu: nama, ikon/label tipe, lokasi, kapasitas, <x-status-badge>, ringkasan
   "X/26 slot tersedia hari ini" (AvailabilityService::availableCount). Empty state bila kosong.
   Client: form GET dengan select/number HTML5.
3. show: parameter ?date=Y-m-d (default hari ini). Server: format salah atau di luar hari ini..+30
   → pakai hari ini + pesan peringatan. Tampilkan info fasilitas, pemilih tanggal (input
   type=date min/max + tombol hari sebelumnya/berikutnya), dan GRID 26 slot dari
   AvailabilityService::slotStatuses(): chip hijau "Tersedia" / abu "Tidak tersedia", legenda.
   Fasilitas dalam_perbaikan/nonaktif → banner status (alert) dan semua slot abu.
   Detail nonaktif tetap bisa dibuka lewat URL langsung (banner "Fasilitas nonaktif").
4. Tombol aksi (URL LITERAL):
   - pengguna login & fasilitas aktif: "Ajukan Reservasi" → /reservations/create?facility={id}&date={tgl}
   - pengguna login: "Laporkan Masalah" → /reports/create?facility={id}
   - tamu: teks "Masuk untuk mengajukan reservasi" → /login
5. PRIVASI US 1: halaman & HTML TIDAK memuat nama pemohon, tujuan, atau ID reservasi.

ACCEPTANCE (catat hasil aktual)
1. Tamu tanpa login membuka /facilities dan detail.
2. Filter q=lab + min_capacity=30 → hanya Lab Komputer 1; Lapangan Futsal tidak pernah muncul.
3. Lab Komputer 1, tanggal besok: slot 09.00, 09.30, 10.00 tidak tersedia (RSV-1); 10.30
   dst. tersedia walau ada RSV-6 'menunggu'.
4. Lab Bahasa: 26 slot tidak tersedia + banner. /facilities/8 → banner nonaktif.
5. ?date=2020-01-01 dan ?date=abc → kembali ke hari ini + pesan.
6. View source detail: tidak ada nama budi/citra/dimas atau teks tujuan.
7. Tombol Ajukan/Laporkan mengarah ke URL literal yang benar (404 sebelum SRS lain di-merge = wajar).
OUTPUT WAJIB: kode + commit + docs/workflow/SRS-003-fasilitas-katalog.md.
```

---

## 4. PROMPT P1 — SRS-004 MASTER FASILITAS & REKAP (`feature/fasilitas-admin`)

```text
LANGKAH PERTAMA: baca AGENTS.md di root repo sampai habis dan patuhi seluruh isinya.
PERANKU: Programmer 1 · SRS-004 Master Fasilitas & Rekap Admin · branch feature/fasilitas-admin
· US 16, 17. Ikuti AGENTS.md (G, H, I). Acuan: README §9.4, asumsi A10–A11.
Aku SATU-SATUNYA yang boleh mengubah composer.json/composer.lock setelah baseline.
FILE MILIKKU: routes/fasilitas-admin.php · Admin/FacilityController · Admin/RecapController ·
app/Exports/* · views/admin/facilities/** · views/admin/recap/** · composer.json/lock ·
config/excel.php, config/dompdf.php (bila dipublish) · docs/workflow/SRS-004-fasilitas-admin.md

LANGKAH
1. PAKET: composer require maatwebsite/excel barryvdh/laravel-dompdf (butuh ext-gd & ext-zip).
   Bila salah satu gagal karena versi, fallback: phpoffice/phpspreadsheet (XLSX) atau
   dompdf/dompdf (PDF) langsung. Catat keputusan di dokumen workflow.
2. ROUTES — group ['auth','role:admin'], prefix 'admin', name 'admin.':
   GET /admin/facilities (index: tabel + cari nama/lokasi + filter status),
   GET /admin/facilities/create, POST /admin/facilities, GET /admin/facilities/{facility}/edit,
   PUT /admin/facilities/{facility}, PATCH /admin/facilities/{facility}/toggle,
   GET /admin/recap, GET /admin/recap/export.
3. MASTER (US 16): validasi name wajib maks 100; type Rule::in(array_keys(Facility::TYPES));
   location wajib maks 100; capacity integer min 1; description opsional maks 1000.
   Client: required, type=number min=1, maxlength. Status diisi eksplisit (tidak di $fillable):
   baru = 'aktif'. TOGGLE: aktif/dalam_perbaikan → nonaktif, nonaktif → aktif. Sebelum
   menonaktifkan, hitung reservasi 'disetujui' dengan tanggal ≥ hari ini dan tampilkan
   peringatan di modal konfirmasi (A10). Tidak ada hapus fisik.
4. REKAP (US 17): filter from/to (default 30 hari lalu s.d. 30 hari ke depan; validasi from ≤ to,
   rentang maks 366 hari) + group_by=facility|location.
   (a) Okupansi: jumlah reservasi 'disetujui', total jam
       (SUM(TIME_TO_SEC(TIMEDIFF(end_time,start_time)))/3600), persentase = total jam ÷
       (13 × jumlah hari rentang) × 100 (untuk group_by=location dibagi juga jumlah fasilitas
       aktif di lokasi itu).
   (b) Frekuensi laporan: jumlah laporan (created_at dalam rentang) total + per kategori.
   Tabel Bootstrap + baris total. Rekap READ-ONLY.
5. EKSPOR: GET /admin/recap/export?format=csv|xlsx|pdf&from=&to=&group_by= (validasi sama).
   Nama file: rekap-reservus-{from}-{to}.{ext}. CSV: streamDownload + fputcsv + BOM UTF-8.
   XLSX: class di app/Exports. PDF: view admin/recap/pdf.blade.php. Semua ekspor memakai helper
   escape formula (sel diawali = + - @ diberi prefiks ').

ACCEPTANCE (catat hasil aktual)
1. Tambah "Lab Jaringan" (laboratorium, Gedung B, 30) → muncul di tabel; nama kosong/kapasitas 0
   → ditolak server.
2. Edit kapasitas R-102 → tersimpan.
3. Nonaktifkan Lapangan Basket → peringatan "1 reservasi disetujui mendatang" (RSV-3) → status
   nonaktif, data reservasi tetap ada. Aktifkan kembali → aktif.
4. Rekap default menampilkan data RSV-H & LPR-H; group_by=location menggabungkan per gedung.
5. Ekspor CSV, XLSX, PDF terunduh dan angkanya sama dengan tabel.
6. Ubah nama fasilitas menjadi =1+1 → ekspor CSV/XLSX menampilkan teks '=1+1, bukan 2.
7. budi & petugas → /admin/facilities dan /admin/recap = 403.
OUTPUT WAJIB: kode + commit + docs/workflow/SRS-004-fasilitas-admin.md.
```

---

## 5. PROMPT P2 — SRS-005 RESERVASI PENGGUNA (`feature/reservasi-user`)

```text
LANGKAH PERTAMA: baca AGENTS.md di root repo sampai habis dan patuhi seluruh isinya.
PERANKU: Programmer 2 · SRS-005 Reservasi Pengguna · branch feature/reservasi-user · US 3, 4, 5.
Ikuti AGENTS.md (G, H, I). Acuan: README §9.5, asumsi A1–A4, A7.
Arah UX: Calendly — pilih fasilitas → tanggal → jam mulai & selesai → tujuan.
FILE MILIKKU: routes/reservasi-user.php · app/Http/Controllers/ReservationController.php ·
app/Policies/ReservationPolicy.php · views/reservations/** · docs/workflow/SRS-005-reservasi-user.md

LANGKAH
1. ROUTES — group ['auth','role:pengguna']: GET /reservations (index), GET /reservations/create,
   POST /reservations (store), GET /reservations/{reservation} (show),
   PATCH /reservations/{reservation}/cancel (cancel) — nama reservations.*.
2. POLICY ReservationPolicy: view → pemilik; cancel → pemilik && AvailabilityService::canUserCancel.
   Gunakan Gate::authorize('view'|'cancel', $reservation).
3. CREATE (US 3): prefill dari ?facility=&date= (abaikan bila fasilitas tidak aktif / tanggal
   invalid). Dropdown fasilitas: Facility::where status aktif (label nama · tipe · lokasi).
   input date min=hari ini max=+30; select jam mulai dari startOptions(), jam selesai dari
   endOptions() — JS menonaktifkan opsi selesai ≤ mulai; textarea tujuan (required, 10–500).
   Opsional: panel kecil "slot tidak tersedia hari itu" dari slotStatuses() (tanpa detail pemohon).
4. STORE (server, berurutan): validasi format (facility_id exists, date Y-m-d, start/end date_format
   H:i, purpose 10–500) → AvailabilityService::validateRange() → Facility::isReservable() →
   hasConflict() ("Slot sudah terpakai pada jam tersebut") → simpan via
   $request->user()->reservations()->create($data) (status default 'menunggu'). Error kembali
   dengan withInput() + pesan Indonesia.
5. INDEX (US 5): reservasi milik saya, terbaru dulu, filter status (tab/pills), badge, paginate 10,
   empty state + tombol "Cari fasilitas" (/facilities).
6. SHOW (US 5): fasilitas, tanggal, jam (timeRange()), tujuan, status, diajukan pada, diproses
   oleh/pada, rejection_reason atau cancel_reason bila ada. Tombol Batalkan tampil hanya bila
   canUserCancel() true.
7. CANCEL (US 4): Gate::authorize('cancel') → status 'dibatalkan', cancelled_by = user,
   cancelled_at = now(). Di luar batas → pesan "Pembatalan hanya bisa paling lambat 2 jam sebelum
   mulai." Konfirmasi confirm() di client.

ACCEPTANCE (catat hasil aktual)
1. Ajukan R-101 lusa 08:00–09:00 → 'menunggu', tampil di riwayat.
2. Lab Komputer 1 besok 09:30–10:00 → ditolak (bentrok RSV-1).
3. 07:15, 20:30, 19:30–20:30, start = end, tanggal kemarin, tanggal +31 → ditolak SERVER (uji juga
   dengan mengubah value lewat DevTools).
4. facility_id Lab Bahasa & Lapangan Futsal disisipkan manual → ditolak server.
5. Sisipkan status=disetujui di form → tetap 'menunggu'.
6. Batal RSV-2 → sukses; batal RSV-7 → ditolak (kurang dari 2 jam); RSV-8 tidak punya tombol batal.
7. Detail RSV-8 & RSV-9 menampilkan alasan.
8. budi membuka /reservations/{id RSV-3} milik citra → 403; PATCH cancel RSV-3 → 403.
OUTPUT WAJIB: kode + commit + docs/workflow/SRS-005-reservasi-user.md.
```

---

## 6. PROMPT P2 — SRS-006 PEMROSESAN RESERVASI PETUGAS (`feature/reservasi-petugas`)

```text
LANGKAH PERTAMA: baca AGENTS.md di root repo sampai habis dan patuhi seluruh isinya.
PERANKU: Programmer 2 · SRS-006 Pemrosesan Reservasi Petugas · branch feature/reservasi-petugas
· US 8, 9, 10. Ikuti AGENTS.md (G, H, I). Acuan: README §9.6.
Branch ini dibuat dari main terbaru — TIDAK bergantung pada SRS-005 (data uji dari seeder).
FILE MILIKKU: routes/reservasi-petugas.php · app/Http/Controllers/Petugas/ReservationController.php
· views/petugas/reservations/** · views/petugas/partials/reservation-queue.blade.php ·
docs/workflow/SRS-006-reservasi-petugas.md

LANGKAH
1. PARTIAL (US 8) views/petugas/partials/reservation-queue.blade.php: kartu "Reservasi Menunggu"
   + jumlah; maks 10 'menunggu' urut tanggal & jam terdekat (eager load user, facility): fasilitas,
   tanggal, jam, pemohon, cuplikan tujuan, penanda "⚠ berpotensi bentrok" bila hasConflict()
   true; tautan "Lihat semua" → /petugas/reservations. Shell dashboard memanggilnya otomatis.
2. ROUTES — group ['auth','role:petugas'], prefix 'petugas', name 'petugas.reservations.':
   GET /petugas/reservations (index) · PATCH /petugas/reservations/{reservation}/approve ·
   PATCH …/reject · PATCH …/cancel.
3. INDEX: tab ?tab=menunggu (default) | disetujui (tanggal ≥ hari ini) | semua; tabel dengan
   pemohon, fasilitas, tanggal, jam, tujuan, status badge, aksi; paginate 15.
4. APPROVE (US 9):
   DB::transaction: kunci baris fasilitas → Facility::whereKey(...)->lockForUpdate()->first();
   $reservation->refresh(); status bukan 'menunggu' → batal dengan pesan "sudah diproses";
   fasilitas tidak aktif → status 'ditolak', rejection_reason "Fasilitas tidak tersedia
   (status: ...)"; hasConflict(..., ignoreId: $reservation->id) → 'ditolak', rejection_reason
   "Bentrok jadwal dengan reservasi yang sudah disetujui"; selain itu → 'disetujui'.
   Semua cabang mengisi processed_by = petugas & processed_at = now(). Flash menjelaskan hasilnya.
5. REJECT: hanya 'menunggu'; rejection_reason wajib (5–500) → 'ditolak' + processed_*.
6. CANCEL (US 10): hanya 'disetujui' dengan tanggal ≥ hari ini; cancel_reason wajib (5–500) →
   'dibatalkan', cancelled_by = petugas, cancelled_at = now(). Slot otomatis tersedia lagi
   karena ketersediaan hanya membaca 'disetujui'.
7. VIEWS: modal Bootstrap berisi textarea alasan (required, minlength) untuk tolak & batal;
   tombol setujui dengan confirm().

ACCEPTANCE (catat hasil aktual)
1. /petugas menampilkan partial dengan RSV-2, 4, 5, 6 (RSV-6 bertanda berpotensi bentrok).
2. Setujui RSV-2 → disetujui.
3. Setujui RSV-4 → disetujui; setujui RSV-5 → otomatis ditolak "Bentrok jadwal".
4. Setujui RSV-6 → otomatis ditolak (bentrok RSV-1).
5. Tolak tanpa alasan → ditolak server (uji dengan menghapus atribut required lewat DevTools).
6. Batalkan RSV-3 dengan alasan → dibatalkan; batal tanpa alasan → ditolak server; batal
   reservasi 'menunggu' via request manual → ditolak.
7. Race: seed ulang, buka dua tab, setujui RSV-4 dan RSV-5 hampir bersamaan → hanya satu disetujui.
8. budi → /petugas/reservations = 403.
OUTPUT WAJIB: kode + commit + docs/workflow/SRS-006-reservasi-petugas.md.
```

---

## 7. PROMPT P3 — SRS-007 LAPORAN PENGGUNA (`feature/laporan-user`)

```text
LANGKAH PERTAMA: baca AGENTS.md di root repo sampai habis dan patuhi seluruh isinya.
PERANKU: Programmer 3 · SRS-007 Laporan Pengguna · branch feature/laporan-user · US 6, 7.
Ikuti AGENTS.md (G, H, I). Acuan: README §9.7, asumsi A6.
FILE MILIKKU: routes/laporan-user.php · app/Http/Controllers/ReportController.php ·
app/Policies/ReportPolicy.php · views/reports/** · docs/workflow/SRS-007-laporan-user.md

LANGKAH
1. ROUTES — group ['auth','role:pengguna']: GET /reports, GET /reports/create, POST /reports,
   GET /reports/{report} — nama reports.*.
2. POLICY ReportPolicy: view → pelapor. Gate::authorize('view', $report).
3. CREATE (US 6): prefill ?facility= ; dropdown SEMUA fasilitas (boleh melapor fasilitas apa pun),
   kategori dari Report::CATEGORIES, deskripsi (required, 10–2000), foto opsional
   (input file accept="image/jpeg,image/png"), preview foto & peringatan ukuran > 2 MB via JS.
4. STORE: validasi facility_id exists, category Rule::in, description, photo
   nullable|image|mimes:jpg,jpeg,png|max:2048. Foto: $request->file('photo')?->store('reports',
   'public'). Simpan via $request->user()->reports()->create([...]) (status default 'baru').
5. INDEX (US 7): laporan saya, terbaru dulu, filter status, badge, thumbnail foto, paginate 10,
   empty state.
6. SHOW (US 7): fasilitas, kategori, deskripsi ({!! nl2br(e(...)) !!}), foto (photoUrl()), status,
   tanggal lapor, petugas pemroses & waktu, resolution_note bila ada.

ACCEPTANCE (catat hasil aktual)
1. Laporan R-101 kategori kerusakan + foto jpg → tersimpan, foto tampil di detail.
2. Laporan tanpa foto → tersimpan.
3. Upload pdf, gambar > 2 MB, dan file PHP yang diganti nama menjadi .jpg → ditolak server.
4. Riwayat budi menampilkan LPR-1, LPR-4, LPR-5 dengan badge; filter status bekerja.
5. Detail LPR-5 menampilkan catatan resolusi & petugas pemroses.
6. Deskripsi berisi <script>alert(1)</script> tampil sebagai teks biasa.
7. Sisipkan status=selesai di form → tetap 'baru'.
8. budi membuka /reports/{id LPR-3} milik citra → 403.
OUTPUT WAJIB: kode + commit + docs/workflow/SRS-007-laporan-user.md.
```

---

## 8. PROMPT P3 — SRS-008 PEMROSESAN LAPORAN & STATUS PERBAIKAN (`feature/laporan-petugas`)

```text
LANGKAH PERTAMA: baca AGENTS.md di root repo sampai habis dan patuhi seluruh isinya.
PERANKU: Programmer 3 · SRS-008 Pemrosesan Laporan & Status Perbaikan · branch
feature/laporan-petugas · US 8, 11, 12. Ikuti AGENTS.md (G, H, I). Acuan: README §9.8, A9.
Branch ini dibuat dari main terbaru — TIDAK bergantung pada SRS-007 (data uji dari seeder).
FILE MILIKKU: routes/laporan-petugas.php · app/Http/Controllers/Petugas/ReportController.php ·
views/petugas/reports/** · views/petugas/partials/report-queue.blade.php ·
docs/workflow/SRS-008-laporan-petugas.md

LANGKAH
1. PARTIAL (US 8) views/petugas/partials/report-queue.blade.php: kartu "Laporan Masuk" + jumlah
   'baru' & 'diproses'; maks 10 terlama dulu (eager load): fasilitas, kategori, pelapor, cuplikan
   deskripsi, ikon foto; tautan "Lihat semua" → /petugas/reports.
2. ROUTES — group ['auth','role:petugas'], prefix 'petugas', name 'petugas.reports.':
   GET /petugas/reports (index) · GET /petugas/reports/{report} (show) ·
   PATCH /petugas/reports/{report}/status (status) ·
   PATCH /petugas/reports/{report}/facility-status (facility-status).
3. INDEX: tab ?status=baru (default) | diproses | selesai | ditolak | semua; paginate 15.
4. SHOW: detail lengkap + foto + status fasilitas saat ini (<x-status-badge>) + panel aksi.
5. STATUS (US 11): whitelist transisi di server:
   baru→diproses, baru→ditolak, diproses→selesai, diproses→ditolak. Lainnya → error.
   resolution_note required_if status in selesai,ditolak (5–1000). Isi processed_by & processed_at.
   Saat menutup laporan dan fasilitasnya 'dalam_perbaikan', tampilkan checkbox opsional
   "Fasilitas sudah bisa dipakai kembali (set aktif)".
6. FACILITY-STATUS (US 12): action Rule::in(['repair','restore']):
   repair: fasilitas aktif → dalam_perbaikan; restore: dalam_perbaikan → aktif.
   Fasilitas 'nonaktif' atau transisi lain → ditolak server (wewenang Admin).
   Hanya kolom status fasilitas yang diubah — jangan menyentuh CRUD admin milik SRS-004.
7. VIEWS: modal catatan resolusi (required, minlength), confirm() untuk aksi fasilitas.

ACCEPTANCE (catat hasil aktual)
1. /petugas menampilkan partial dengan LPR-1 s.d. LPR-4.
2. LPR-2 → diproses → tandai Aula Utama dalam perbaikan (cek badge/DB) → selesai tanpa catatan
   → ditolak server → selesai dengan catatan + centang "set aktif" → Aula kembali aktif.
3. LPR-4 → ditolak dengan catatan.
4. LPR-1 → selesai + restore Lab Bahasa → aktif.
5. PATCH status LPR-5 (selesai) → baru → ditolak server.
6. PATCH facility-status restore untuk Lapangan Futsal (nonaktif) → ditolak server.
7. budi → /petugas/reports = 403.
8. (Integrasi setelah SRS-003 di-merge) selama Aula dalam perbaikan, katalog menampilkan semua slot
   tidak tersedia.
OUTPUT WAJIB: kode + commit + docs/workflow/SRS-008-laporan-petugas.md.
```

---

## 9. PROMPT PM — ROLLING MERGE & INTEGRASI (dipakai berulang per PR)

```text
LANGKAH PERTAMA: baca AGENTS.md di root repo sampai habis dan patuhi seluruh isinya.
PERANKU: PM · review & merge PR (rolling). Ikuti AGENTS.md.
UNTUK SETIAP PR:
1. git fetch origin && git checkout <branch-PR> && git diff --name-only origin/main...HEAD
   → semua file harus termasuk kepemilikan SRS tersebut (AGENTS.md F). Temuan di luar → minta
   revisi. Pastikan tidak ada perubahan migration/model/seeder/service/layout/web.php/
   package.json; composer.* hanya boleh dari SRS-004.
2. Pastikan docs/workflow/SRS-00X-<slug>.md ada dan terisi (tabel uji punya hasil aktual).
3. php artisan migrate:fresh --seed && php artisan test && php artisan serve → jalankan acceptance
   SRS itu (README §9.x) + regresi cepat: login gating, /petugas 403 untuk budi, katalog publik.
4. Merge dengan "Create a merge commit" (D-09). Umumkan di grup: "SRS-00X merged — silakan
   git merge origin/main di branch kalian."
5. UJI INTEGRASI lintas SRS setelah pasangan terkait sama-sama di main:
   003+005: tombol Ajukan Reservasi di detail fasilitas mengisi form otomatis.
   003+006: reservasi yang baru disetujui membuat slot katalog tidak tersedia; setelah dibatalkan
            petugas, slot tersedia lagi.
   003+008: fasilitas dalam perbaikan → 26 slot tidak tersedia di katalog.
   003+004: fasilitas baru dari admin muncul di katalog; fasilitas nonaktif hilang dari katalog.
   003+007: tombol Laporkan Masalah mengisi fasilitas otomatis.
   005+006: pengajuan baru muncul di partial dashboard petugas.
   007+008: laporan baru muncul di antrian; catatan resolusi terlihat pelapor.
   002+semua: akun hasil verifikasi dapat mengajukan reservasi dan laporan.
6. Lacak 17 US (README §4.1). Target 17/17 di main pada Fase 4 (H-7).
7. Sebelum screenshot: migrate:fresh --seed, perkaya data lewat UI, lalu
   mysqldump -u root reservus > database/reservus.sql dan commit (chore(baseline): dump database).
```

---

## 10. PROMPT AUDIT KEAMANAN (PM, setelah gelombang 2 · sebelum H-7)

```text
LANGKAH PERTAMA: baca AGENTS.md di root repo sampai habis dan patuhi seluruh isinya.
PERANKU: PM · Audit keamanan white-box pada main. Ikuti AGENTS.md bagian H dan README §12.
Kamu berperan sebagai penguji penetrasi untuk aplikasi lokal milik tim sendiri.
1. Tinjau kode: semua route (php artisan route:list) → pastikan middleware tepat; semua controller
   pengubah data → validated() + pengisian eksplisit; Policy dipakai di show/cancel; tidak ada
   {!! !!} untuk input pengguna; semua form punya @csrf; upload tervalidasi; transisi status
   ber-whitelist; approve memakai transaction + lockForUpdate; ekspor meng-escape formula.
2. Jalankan uji serang mandiri pada README §12 terhadap php artisan serve lokal (DevTools/curl),
   termasuk IDOR, forced browsing, mass assignment, bypass validasi, upload, XSS, CSRF, race.
3. Laporkan setiap temuan: judul, tingkat (Tinggi/Sedang/Rendah), bukti langkah reproduksi,
   file:baris, akar masalah, perbaikan MINIMAL yang disarankan, PEMILIK file (dari AGENTS.md F).
4. JANGAN memperbaiki file milik SRS lain — buat daftar HANDOFF per pemilik. Perbaikan file
   baseline dikerjakan di main sebagai fix(baseline).
5. Simpan hasil di docs/workflow/AUDIT-KEAMANAN.md (tabel temuan + status perbaikan).
```

---

## 11. PROMPT DEBUG / QA (semua anggota)

```text
LANGKAH PERTAMA: baca AGENTS.md di root repo sampai habis dan patuhi seluruh isinya.
Konteks: peran & branch: [...] · SRS/US: [...]
Langkah reproduksi: 1) ... 2) ... 3) ...
Pesan error / perilaku salah: [tempel] · File terkait: [...]
Tugas: temukan akar masalah → perbaikan MINIMAL di file milik SRS-ku saja → jalankan ulang
acceptance yang terdampak → jelaskan dalam 2 kalimat → tambahkan ke bagian "Kendala & Solusi"
di dokumen workflow SRS-ku. Akar masalah di file milik orang lain → ### HANDOFF UNTUK PM.
Petunjuk umum: view not found → cek folder /views (D-02); error DB → cek .env & MySQL 9.5
(caching_sha2_password); waktu meleset → APP_TIMEZONE; Gate/authorize error → pakai
Gate::authorize() karena base Controller Laravel 11+ kosong.
```

---

## 12. PROMPT KOMPILASI DOKUMEN WORD & SLIDE (PM, Fase 6)

```text
LANGKAH PERTAMA: baca AGENTS.md di root repo sampai habis dan patuhi seluruh isinya.
PERANKU: PM. Baca README-Workflow-Reservus.md dan SEMUA docs/workflow/SRS-00X-*.md.
Susun draf isi dokumen Word pengumpulan (Bahasa Indonesia formal):
1. Nama & NIM anggota (placeholder bila belum ada).
2. Pembagian tugas: tabel SRS → PIC → US → ringkasan 1 kalimat.
3. Link Google Drive (placeholder) berisi source code (zip tanpa vendor/node_modules),
   database/reservus.sql, panduan.
4. Informasi setting (README §16.1–16.3).
5. Informasi login tiap aktor (README §16.4).
6. Screenshot + penjelasan singkat per fitur, urut US 1–17, memakai tabel Screenshot tiap dokumen
   workflow (sebut path gambar).
Lalu susun outline slide 10 menit: latar belakang · arsitektur & pembagian SRS · demo per domain ·
kendala & solusi (ambil dari bagian Kendala tiap dokumen workflow) · penutup, plus 6 pertanyaan
tanya-jawab yang paling mungkin muncul beserta jawaban singkat.
Simpan sebagai docs/LAPORAN-DRAFT.md.
```

---

## 13. Etika & Aturan Penggunaan AI

1. Review & pahami semua kode AI — setiap anggota harus bisa menjelaskan kodenya saat UTS.
2. Jangan commit kode yang belum diuji; ikuti konvensi commit.
3. Saran AI di luar scope → HANDOFF ke PM, jangan diimplementasikan sendiri.
4. Dokumen workflow per SRS adalah bukti proses; simpan juga log percakapan penting sebagai lampiran.
5. Jangan mengirim kredensial asli (akun GitHub, database produksi, token) ke AI mana pun, dan
   jangan menaruh token di `AGENTS.md`.
6. Uji serang (§10 dan README §12) hanya dilakukan terhadap instalasi lokal milik tim sendiri.