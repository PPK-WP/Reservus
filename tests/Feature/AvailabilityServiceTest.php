<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use App\Services\AvailabilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * Uji kontrak AvailabilityService (E1) — fondasi aturan bisnis 1–4 & 6.
 */
class AvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    private AvailabilityService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AvailabilityService::class);
    }

    private function besok(): string
    {
        return Carbon::now('Asia/Jakarta')->addDay()->format('Y-m-d');
    }

    public function test_slots_menghasilkan_26_slot_tetap(): void
    {
        $slots = $this->service->slots();

        $this->assertCount(26, $slots);
        $this->assertSame(['start' => '07:00', 'end' => '07:30', 'label' => '07.00–07.30'], $slots[0]);
        $this->assertSame(['start' => '19:30', 'end' => '20:00', 'label' => '19.30–20.00'], $slots[25]);
    }

    public function test_start_options_dan_end_options_masing_masing_26_nilai(): void
    {
        $start = $this->service->startOptions();
        $end = $this->service->endOptions();

        $this->assertCount(26, $start);
        $this->assertCount(26, $end);
        $this->assertSame('07:00', $start[0]);
        $this->assertSame('19:30', $start[25]);
        $this->assertSame('07:30', $end[0]);
        $this->assertSame('20:00', $end[25]);
    }

    public function test_normalize_time_menyeragamkan_format(): void
    {
        $this->assertSame('09:00', $this->service->normalizeTime('09:00:00'));
        $this->assertSame('09:00', $this->service->normalizeTime('9:00'));
        $this->assertSame('19:30', $this->service->normalizeTime('19:30'));
    }

    public function test_jam_bukan_kelipatan_30_menit_ditolak(): void
    {
        $errors = $this->service->validateRange($this->besok(), '07:15', '08:00');

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('kelipatan', implode(' ', $errors));
    }

    public function test_jam_di_luar_jam_operasional_ditolak(): void
    {
        $errors = $this->service->validateRange($this->besok(), '19:30', '20:30');

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('jam operasional', implode(' ', $errors));
    }

    public function test_jam_mulai_lebih_besar_atau_sama_dengan_jam_selesai_ditolak(): void
    {
        $this->assertNotEmpty($this->service->validateRange($this->besok(), '10:00', '10:00'));
        $this->assertNotEmpty($this->service->validateRange($this->besok(), '11:00', '10:00'));
    }

    public function test_tanggal_lampau_dan_lebih_dari_30_hari_ditolak(): void
    {
        $kemarin = Carbon::now('Asia/Jakarta')->subDay()->format('Y-m-d');
        $jauh = Carbon::now('Asia/Jakarta')->addDays(31)->format('Y-m-d');

        $this->assertStringContainsString(
            'masa lalu',
            implode(' ', $this->service->validateRange($kemarin, '09:00', '10:00'))
        );
        $this->assertStringContainsString(
            '30 hari',
            implode(' ', $this->service->validateRange($jauh, '09:00', '10:00'))
        );
    }

    public function test_rentang_valid_tidak_menghasilkan_error(): void
    {
        $this->assertSame([], $this->service->validateRange($this->besok(), '09:00', '10:30'));
    }

    public function test_hari_ini_dengan_jam_mulai_yang_sudah_lewat_ditolak(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-20 12:00', 'Asia/Jakarta'));

        $errors = $this->service->validateRange('2026-05-20', '09:00', '10:00');
        $this->assertStringContainsString('setelah waktu sekarang', implode(' ', $errors));

        $this->assertSame([], $this->service->validateRange('2026-05-20', '14:00', '15:00'));

        Carbon::setTestNow();
    }

    public function test_reservasi_bersinggungan_bukan_bentrok(): void
    {
        $facility = Facility::factory()->create();
        Reservation::factory()->approved()->create([
            'facility_id' => $facility->id,
            'reservation_date' => $this->besok(),
            'start_time' => '09:00',
            'end_time' => '10:30',
        ]);

        $this->assertFalse(
            $this->service->hasConflict($facility->id, $this->besok(), '10:30', '11:00')
        );
    }

    public function test_reservasi_tumpang_tindih_terdeteksi_bentrok(): void
    {
        $facility = Facility::factory()->create();
        Reservation::factory()->approved()->create([
            'facility_id' => $facility->id,
            'reservation_date' => $this->besok(),
            'start_time' => '09:00',
            'end_time' => '10:30',
        ]);

        $this->assertTrue(
            $this->service->hasConflict($facility->id, $this->besok(), '10:00', '11:00')
        );
    }

    public function test_reservasi_menunggu_tidak_dihitung_bentrok(): void
    {
        $facility = Facility::factory()->create();
        Reservation::factory()->create([
            'facility_id' => $facility->id,
            'reservation_date' => $this->besok(),
            'start_time' => '09:00',
            'end_time' => '10:30',
            'status' => 'menunggu',
        ]);

        $this->assertFalse(
            $this->service->hasConflict($facility->id, $this->besok(), '09:00', '10:30')
        );
        $this->assertSame(26, $this->service->availableCount($facility, $this->besok()));
    }

    public function test_ignore_id_mengabaikan_reservasi_yang_sedang_diproses(): void
    {
        $facility = Facility::factory()->create();
        $reservation = Reservation::factory()->approved()->create([
            'facility_id' => $facility->id,
            'reservation_date' => $this->besok(),
            'start_time' => '09:00',
            'end_time' => '10:30',
        ]);

        $this->assertTrue($this->service->hasConflict($facility->id, $this->besok(), '09:00', '10:30'));
        $this->assertFalse(
            $this->service->hasConflict($facility->id, $this->besok(), '09:00', '10:30', $reservation->id)
        );
    }

    public function test_slot_statuses_menandai_slot_terpakai_sebagai_tidak_tersedia(): void
    {
        $facility = Facility::factory()->create();
        Reservation::factory()->approved()->create([
            'facility_id' => $facility->id,
            'reservation_date' => $this->besok(),
            'start_time' => '09:00',
            'end_time' => '10:30',
        ]);

        $slots = collect($this->service->slotStatuses($facility, $this->besok()));

        $this->assertCount(26, $slots);
        $this->assertSame('tidak_tersedia', $slots->firstWhere('start', '09:00')['status']);
        $this->assertSame('tidak_tersedia', $slots->firstWhere('start', '10:00')['status']);
        $this->assertSame('tersedia', $slots->firstWhere('start', '10:30')['status']);
        $this->assertSame(23, $this->service->availableCount($facility, $this->besok()));
    }

    public function test_fasilitas_dalam_perbaikan_semua_slot_tidak_tersedia(): void
    {
        $facility = Facility::factory()->dalamPerbaikan()->create();

        $slots = $this->service->slotStatuses($facility, $this->besok());

        $this->assertCount(26, $slots);
        foreach ($slots as $slot) {
            $this->assertSame('tidak_tersedia', $slot['status']);
        }
        $this->assertSame(0, $this->service->availableCount($facility, $this->besok()));
    }

    public function test_can_user_cancel_hanya_untuk_pemilik_dan_lebih_dari_dua_jam(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-20 08:00', 'Asia/Jakarta'));

        $pemilik = User::factory()->create();
        $orangLain = User::factory()->create();
        $facility = Facility::factory()->create();

        $reservation = Reservation::factory()->create([
            'user_id' => $pemilik->id,
            'facility_id' => $facility->id,
            'reservation_date' => '2026-05-20',
            'start_time' => '13:00',
            'end_time' => '14:00',
            'status' => 'menunggu',
        ]);

        // Pemilik, 5 jam sebelum mulai → boleh.
        $this->assertTrue($this->service->canUserCancel($reservation, $pemilik));

        // Bukan pemilik → tidak boleh (IDOR).
        $this->assertFalse($this->service->canUserCancel($reservation, $orangLain));

        // Kurang dari 2 jam sebelum mulai → tidak boleh.
        Carbon::setTestNow(Carbon::parse('2026-05-20 11:30', 'Asia/Jakarta'));
        $this->assertFalse($this->service->canUserCancel($reservation->fresh(), $pemilik));

        // Status selain menunggu/disetujui → tidak boleh.
        Carbon::setTestNow(Carbon::parse('2026-05-20 08:00', 'Asia/Jakarta'));
        $reservation->status = 'ditolak';
        $reservation->save();
        $this->assertFalse($this->service->canUserCancel($reservation->fresh(), $pemilik));

        Carbon::setTestNow();
    }
}
