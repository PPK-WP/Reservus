<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReservationPetugasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function petugas(): User
    {
        $user = User::factory()->create();
        $user->role = 'petugas';
        $user->status = 'aktif';
        $user->save();

        return $user;
    }

    private function pengguna(): User
    {
        $user = User::factory()->create();
        $user->role = 'pengguna';
        $user->status = 'aktif';
        $user->save();

        return $user;
    }

    private function besok(): string
    {
        return Carbon::now('Asia/Jakarta')->addDay()->format('Y-m-d');
    }

    public function test_petugas_dapat_melihat_antrian_dan_menyetujui_reservasi(): void
    {
        $petugas = $this->petugas();
        $reservation = Reservation::factory()->create([
            'user_id' => $this->pengguna()->id,
            'facility_id' => Facility::factory()->create()->id,
            'reservation_date' => $this->besok(),
            'status' => 'menunggu',
        ]);

        $this->actingAs($petugas)
            ->get('/petugas/reservations')
            ->assertOk()
            ->assertSee('Antrian Reservasi');

        $this->actingAs($petugas)
            ->patch('/petugas/reservations/'.$reservation->id.'/approve')
            ->assertRedirect('/petugas/reservations/'.$reservation->id);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'disetujui',
            'processed_by' => $petugas->id,
        ]);
    }

    public function test_approval_menolak_otomatis_reservasi_yang_bentrok(): void
    {
        $petugas = $this->petugas();
        $facility = Facility::factory()->create();
        Reservation::factory()->approved()->create([
            'facility_id' => $facility->id,
            'reservation_date' => $this->besok(),
            'start_time' => '09:00',
            'end_time' => '10:30',
        ]);
        $waiting = Reservation::factory()->create([
            'user_id' => $this->pengguna()->id,
            'facility_id' => $facility->id,
            'reservation_date' => $this->besok(),
            'start_time' => '10:00',
            'end_time' => '11:00',
            'status' => 'menunggu',
        ]);

        $this->actingAs($petugas)
            ->patch('/petugas/reservations/'.$waiting->id.'/approve')
            ->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'id' => $waiting->id,
            'status' => 'ditolak',
            'rejection_reason' => 'Ditolak otomatis karena bentrok dengan reservasi yang disetujui.',
            'processed_by' => $petugas->id,
        ]);
    }

    public function test_penolakan_wajib_memiliki_alasan(): void
    {
        $petugas = $this->petugas();
        $reservation = Reservation::factory()->create([
            'user_id' => $this->pengguna()->id,
            'facility_id' => Facility::factory()->create()->id,
        ]);

        $this->actingAs($petugas)
            ->from('/petugas/reservations/'.$reservation->id)
            ->patch('/petugas/reservations/'.$reservation->id.'/reject', ['rejection_reason' => ''])
            ->assertRedirect('/petugas/reservations/'.$reservation->id)
            ->assertSessionHasErrors('rejection_reason');

        $this->actingAs($petugas)
            ->patch('/petugas/reservations/'.$reservation->id.'/reject', ['rejection_reason' => 'Jadwal fasilitas penuh.'])
            ->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'ditolak',
            'rejection_reason' => 'Jadwal fasilitas penuh.',
        ]);
    }

    public function test_approval_menolak_otomatis_fasilitas_yang_tidak_aktif(): void
    {
        $petugas = $this->petugas();
        $facility = Facility::factory()->dalamPerbaikan()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $this->pengguna()->id,
            'facility_id' => $facility->id,
            'reservation_date' => $this->besok(),
            'status' => 'menunggu',
        ]);

        $this->actingAs($petugas)
            ->patch('/petugas/reservations/'.$reservation->id.'/approve')
            ->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'ditolak',
            'rejection_reason' => 'Fasilitas tidak aktif saat reservasi diproses.',
        ]);
    }

    public function test_pembatalan_darurat_wajib_alasan_dan_hanya_untuk_reservasi_mendatang(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-20 08:00', 'Asia/Jakarta'));
        $petugas = $this->petugas();
        $facility = Facility::factory()->create();
        $future = Reservation::factory()->approved()->create([
            'user_id' => $this->pengguna()->id,
            'facility_id' => $facility->id,
            'reservation_date' => '2026-05-20',
            'start_time' => '13:00',
            'end_time' => '14:00',
        ]);

        $this->actingAs($petugas)
            ->patch('/petugas/reservations/'.$future->id.'/cancel', ['cancel_reason' => ''])
            ->assertSessionHasErrors('cancel_reason');

        $this->actingAs($petugas)
            ->patch('/petugas/reservations/'.$future->id.'/cancel', ['cancel_reason' => 'Fasilitas harus dikosongkan.'])
            ->assertRedirect();
        $this->assertDatabaseHas('reservations', [
            'id' => $future->id,
            'status' => 'dibatalkan',
            'cancelled_by' => $petugas->id,
        ]);

        $past = Reservation::factory()->approved()->create([
            'user_id' => $this->pengguna()->id,
            'facility_id' => $facility->id,
            'reservation_date' => '2026-05-20',
            'start_time' => '07:00',
            'end_time' => '08:00',
        ]);

        $this->actingAs($petugas)
            ->patch('/petugas/reservations/'.$past->id.'/cancel', ['cancel_reason' => 'Fasilitas ditutup sementara.'])
            ->assertRedirect();
        $this->assertDatabaseHas('reservations', [
            'id' => $past->id,
            'status' => 'disetujui',
        ]);

        Carbon::setTestNow();
    }

    public function test_pengguna_tidak_boleh_membuka_antrian_petugas(): void
    {
        $this->actingAs($this->pengguna())
            ->get('/petugas/reservations')
            ->assertForbidden();
    }
}
