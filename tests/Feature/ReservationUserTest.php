<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReservationUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
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

    public function test_pengguna_dapat_mengajukan_reservasi_menunggu(): void
    {
        $user = $this->pengguna();
        $facility = Facility::factory()->create();

        $response = $this->actingAs($user)->post('/reservations', [
            'facility_id' => $facility->id,
            'reservation_date' => $this->besok(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'purpose' => 'Kegiatan belajar kelompok mahasiswa.',
        ]);

        $response->assertRedirect('/reservations');
        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'status' => 'menunggu',
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);
    }

    public function test_pengajuan_menolak_rentang_tidak_valid_dan_bentrok(): void
    {
        $user = $this->pengguna();
        $facility = Facility::factory()->create();

        $invalid = $this->actingAs($user)->from('/reservations/create')->post('/reservations', [
            'facility_id' => $facility->id,
            'reservation_date' => $this->besok(),
            'start_time' => '07:15',
            'end_time' => '08:00',
            'purpose' => 'Kegiatan belajar kelompok mahasiswa.',
        ]);
        $invalid->assertRedirect('/reservations/create')->assertSessionHasErrors('reservation');

        Reservation::factory()->approved()->create([
            'facility_id' => $facility->id,
            'reservation_date' => $this->besok(),
            'start_time' => '09:00',
            'end_time' => '10:00',
        ]);

        $conflict = $this->actingAs($user)->from('/reservations/create')->post('/reservations', [
            'facility_id' => $facility->id,
            'reservation_date' => $this->besok(),
            'start_time' => '09:30',
            'end_time' => '10:30',
            'purpose' => 'Kegiatan belajar kelompok mahasiswa.',
        ]);
        $conflict->assertRedirect('/reservations/create')->assertSessionHasErrors('reservation');
    }

    public function test_pengguna_tidak_dapat_melihat_reservasi_milik_orang_lain(): void
    {
        $owner = $this->pengguna();
        $other = $this->pengguna();
        $reservation = Reservation::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->get('/reservations/'.$reservation->id)
            ->assertForbidden();
    }

    public function test_pengguna_dapat_membatalkan_reservasi_setidaknya_dua_jam_sebelum_mulai(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-20 08:00', 'Asia/Jakarta'));
        $user = $this->pengguna();
        $facility = Facility::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'reservation_date' => '2026-05-20',
            'start_time' => '13:00',
            'end_time' => '14:00',
            'status' => 'menunggu',
        ]);

        $this->actingAs($user)
            ->patch('/reservations/'.$reservation->id.'/cancel')
            ->assertRedirect('/reservations/'.$reservation->id);
        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'status' => 'dibatalkan',
            'cancelled_by' => $user->id,
        ]);

        $lateReservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'facility_id' => $facility->id,
            'reservation_date' => '2026-05-20',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'status' => 'menunggu',
        ]);

        $this->actingAs($user)
            ->patch('/reservations/'.$lateReservation->id.'/cancel')
            ->assertRedirect()
            ->assertSessionHasErrors('reservation');
        $this->assertDatabaseHas('reservations', [
            'id' => $lateReservation->id,
            'status' => 'menunggu',
        ]);

        Carbon::setTestNow();
    }
}
