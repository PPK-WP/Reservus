<?php

namespace App\Services;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Sumber kebenaran ketersediaan slot & validasi waktu reservasi.
 *
 * Semua perhitungan waktu memakai zona WIB (Asia/Jakarta, D-04) dan kolom TIME
 * diperlakukan sebagai string 'H:i' (D-05).
 */
class AvailabilityService
{
    /** Jam buka fasilitas. */
    public const OPEN = '07:00';

    /** Jam tutup fasilitas. */
    public const CLOSE = '20:00';

    /** Panjang satu slot dalam menit. */
    public const SLOT_MINUTES = 30;

    /** Batas pembatalan mandiri oleh pengguna: 120 menit sebelum mulai. */
    public const CANCEL_DEADLINE_MINUTES = 120;

    /** Reservasi paling jauh 30 hari ke depan. */
    public const MAX_DAYS_AHEAD = 30;

    /** Zona waktu aplikasi. */
    private const TZ = 'Asia/Jakarta';

    /**
     * 26 slot tetap 07.00–20.00.
     *
     * @return array<int, array{start: string, end: string, label: string}>
     */
    public function slots(): array
    {
        $slots = [];
        $cursor = $this->toMinutes(self::OPEN);
        $close = $this->toMinutes(self::CLOSE);

        while ($cursor + self::SLOT_MINUTES <= $close) {
            $start = $this->toTime($cursor);
            $end = $this->toTime($cursor + self::SLOT_MINUTES);

            $slots[] = [
                'start' => $start,
                'end' => $end,
                'label' => str_replace(':', '.', $start).'–'.str_replace(':', '.', $end),
            ];

            $cursor += self::SLOT_MINUTES;
        }

        return $slots;
    }

    /**
     * Pilihan jam mulai: '07:00' … '19:30' (26 nilai).
     *
     * @return array<int, string>
     */
    public function startOptions(): array
    {
        return array_column($this->slots(), 'start');
    }

    /**
     * Pilihan jam selesai: '07:30' … '20:00' (26 nilai).
     *
     * @return array<int, string>
     */
    public function endOptions(): array
    {
        return array_column($this->slots(), 'end');
    }

    /**
     * Status tiap slot pada satu fasilitas & tanggal.
     * Fasilitas yang tidak 'aktif' → seluruh slot 'tidak_tersedia'.
     * Reservasi 'menunggu' TIDAK mengunci slot (aturan bisnis 3).
     * Tidak pernah mengembalikan nama pemohon/tujuan (privasi, US 1).
     *
     * @return array<int, array{start: string, end: string, label: string, status: string}>
     */
    public function slotStatuses(Facility $facility, string $date): array
    {
        $slots = $this->slots();

        if (! $facility->isReservable()) {
            return array_map(
                fn (array $slot): array => $slot + ['status' => 'tidak_tersedia'],
                $slots
            );
        }

        $approved = Reservation::query()
            ->approved()
            ->where('facility_id', $facility->id)
            ->whereDate('reservation_date', $date)
            ->get(['start_time', 'end_time'])
            ->map(fn (Reservation $r): array => [
                'start' => $this->toMinutes($this->normalizeTime($r->start_time)),
                'end' => $this->toMinutes($this->normalizeTime($r->end_time)),
            ])
            ->all();

        return array_map(function (array $slot) use ($approved): array {
            $slotStart = $this->toMinutes($slot['start']);
            $slotEnd = $this->toMinutes($slot['end']);

            foreach ($approved as $booked) {
                // Tumpang tindih: mulai_lama < selesai_baru AND selesai_lama > mulai_baru.
                if ($booked['start'] < $slotEnd && $booked['end'] > $slotStart) {
                    return $slot + ['status' => 'tidak_tersedia'];
                }
            }

            return $slot + ['status' => 'tersedia'];
        }, $slots);
    }

    /** Jumlah slot tersedia pada satu fasilitas & tanggal (ringkasan kartu katalog). */
    public function availableCount(Facility $facility, string $date): int
    {
        return count(array_filter(
            $this->slotStatuses($facility, $date),
            fn (array $slot): bool => $slot['status'] === 'tersedia'
        ));
    }

    /**
     * Validasi rentang waktu reservasi (otoritatif di server).
     *
     * @return array<int, string> daftar pesan error; array kosong berarti valid
     */
    public function validateRange(string $date, string $start, string $end): array
    {
        $errors = [];

        $day = $this->parseDate($date);
        if (! $day) {
            return ['Format tanggal tidak valid.'];
        }

        $start = $this->normalizeTime($start);
        $end = $this->normalizeTime($end);

        if (! $this->isValidTime($start) || ! $this->isValidTime($end)) {
            return ['Format jam tidak valid.'];
        }

        $today = Carbon::now(self::TZ)->startOfDay();

        if ($day->lt($today)) {
            $errors[] = 'Tanggal reservasi tidak boleh di masa lalu.';
        } elseif ($day->gt($today->copy()->addDays(self::MAX_DAYS_AHEAD))) {
            $errors[] = 'Tanggal reservasi paling jauh '.self::MAX_DAYS_AHEAD.' hari dari hari ini.';
        }

        $startMinutes = $this->toMinutes($start);
        $endMinutes = $this->toMinutes($end);
        $open = $this->toMinutes(self::OPEN);
        $close = $this->toMinutes(self::CLOSE);

        if ($startMinutes < $open || $startMinutes > $close || $endMinutes < $open || $endMinutes > $close) {
            $errors[] = 'Jam reservasi harus berada dalam jam operasional '
                .str_replace(':', '.', self::OPEN).'–'.str_replace(':', '.', self::CLOSE).'.';
        }

        if ($startMinutes % self::SLOT_MINUTES !== 0 || $endMinutes % self::SLOT_MINUTES !== 0) {
            $errors[] = 'Jam mulai dan selesai harus kelipatan '.self::SLOT_MINUTES.' menit.';
        }

        if ($startMinutes >= $endMinutes) {
            $errors[] = 'Jam selesai harus setelah jam mulai.';
        }

        if ($day->isSameDay($today)) {
            $now = Carbon::now(self::TZ);
            $startsAt = $day->copy()->addMinutes($startMinutes);

            if ($startsAt->lte($now)) {
                $errors[] = 'Untuk hari ini, jam mulai harus setelah waktu sekarang.';
            }
        }

        return $errors;
    }

    /**
     * Apakah rentang bentrok dengan reservasi 'disetujui' lain pada fasilitas & tanggal sama?
     * Bersinggungan (10.30 dengan 10.30) BUKAN bentrok.
     *
     * @param  int|null  $ignoreId  id reservasi yang diabaikan (mis. saat approval ulang)
     */
    public function hasConflict(int $facilityId, string $date, string $start, string $end, ?int $ignoreId = null): bool
    {
        $start = $this->normalizeTime($start);
        $end = $this->normalizeTime($end);

        return Reservation::query()
            ->approved()
            ->where('facility_id', $facilityId)
            ->whereDate('reservation_date', $date)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->exists();
    }

    /**
     * Pengguna boleh membatalkan reservasinya sendiri bila statusnya masih
     * 'menunggu'/'disetujui' dan sekarang ≤ jam mulai − 120 menit (WIB).
     */
    public function canUserCancel(Reservation $reservation, User $user): bool
    {
        if ($reservation->user_id !== $user->id) {
            return false;
        }

        if (! in_array($reservation->status, ['menunggu', 'disetujui'], true)) {
            return false;
        }

        $deadline = $reservation->startsAt()->subMinutes(self::CANCEL_DEADLINE_MINUTES);

        return Carbon::now(self::TZ)->lte($deadline);
    }

    /** '09:00:00' / '9:00' → '09:00'. */
    public function normalizeTime(string $time): string
    {
        $parts = explode(':', trim($time));
        $hour = (int) ($parts[0] ?? 0);
        $minute = (int) ($parts[1] ?? 0);

        return sprintf('%02d:%02d', $hour, $minute);
    }

    /** Ubah 'H:i' menjadi jumlah menit sejak tengah malam. */
    private function toMinutes(string $time): int
    {
        [$hour, $minute] = array_pad(explode(':', $this->normalizeTime($time)), 2, '0');

        return ((int) $hour) * 60 + (int) $minute;
    }

    /** Ubah jumlah menit sejak tengah malam menjadi 'H:i'. */
    private function toTime(int $minutes): string
    {
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    /** Apakah string jam berformat H:i yang masuk akal? */
    private function isValidTime(string $time): bool
    {
        return (bool) preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time);
    }

    /** Parse 'Y-m-d' menjadi Carbon WIB awal hari, null bila tidak valid. */
    private function parseDate(string $date): ?Carbon
    {
        try {
            $parsed = Carbon::createFromFormat('Y-m-d', trim($date), self::TZ);
        } catch (\Throwable) {
            return null;
        }

        if (! $parsed || $parsed->format('Y-m-d') !== trim($date)) {
            return null;
        }

        return $parsed->startOfDay();
    }
}
