## SRS & Ringkasan

<!-- Contoh: SRS-005 Reservasi Pengguna (US 3, 4, 5) -->
**SRS:** SRS-00X — <nama>
**User story:** US-...
**Branch:** feature/...

Ringkasan singkat apa yang dikerjakan:

-

## Checklist PR (README §11)

- [ ] Hanya file milik SRS ini yang berubah (`git diff --name-only origin/main...HEAD`)
- [ ] Tidak ada migration/model/service/layout yang diubah
- [ ] `php artisan migrate:fresh --seed` + seluruh skenario acceptance lulus
- [ ] `docs/workflow/SRS-00X-<slug>.md` terisi lengkap
- [ ] Pesan commit mengikuti konvensi `<tipe>(<domain>): <ringkasan> (SRS-00X, US-n)`

## Keamanan (README §12)

- [ ] Route non-publik memakai `role:` dan otorisasi pemilik data (Policy + `Gate::authorize`)
- [ ] Tidak ada `$request->all()`; kolom role/status/proses diisi eksplisit
- [ ] `@csrf` di semua form; Blade memakai `{{ }}` (atau `{!! nl2br(e($x)) !!}`)
- [ ] Enum divalidasi dengan `Rule::in(array_keys(Model::KONSTANTA))`

## Hasil Uji

| Skenario | Hasil |
|---|---|
|  |  |

## Handoff untuk PM (bila ada)

- Kebutuhan:
- File terkait:
- Alasan:
- Dampak bila tidak ada:
