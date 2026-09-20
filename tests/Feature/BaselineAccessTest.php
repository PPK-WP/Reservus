<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Uji gerbang akses baseline: redirect publik, middleware role, dan halaman auth.
 */
class BaselineAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function buatUser(string $role, string $status = 'aktif'): User
    {
        $user = User::factory()->create();
        $user->role = $role;
        $user->status = $status;
        $user->save();

        return $user;
    }

    public function test_halaman_depan_diarahkan_ke_katalog_fasilitas(): void
    {
        $this->get('/')->assertRedirect('/facilities');
    }

    public function test_tamu_yang_membuka_home_diarahkan_ke_login(): void
    {
        $this->get('/home')->assertRedirect('/login');
    }

    public function test_halaman_login_dan_register_dapat_diakses_tamu(): void
    {
        $this->get('/login')->assertOk()->assertSee('Masuk');
        $this->get('/register')->assertOk()->assertSee('Daftar');
    }

    public function test_petugas_melihat_dashboard_petugas(): void
    {
        $this->actingAs($this->buatUser('petugas'))
            ->get('/petugas')
            ->assertOk()
            ->assertSee('Dashboard Petugas');
    }

    public function test_pengguna_tidak_boleh_membuka_dashboard_petugas(): void
    {
        $this->actingAs($this->buatUser('pengguna'))
            ->get('/petugas')
            ->assertForbidden();
    }

    public function test_home_mengarahkan_petugas_ke_dashboardnya(): void
    {
        $this->actingAs($this->buatUser('petugas'))
            ->get('/home')
            ->assertRedirect('/petugas');
    }

    public function test_home_menampilkan_beranda_sesuai_peran(): void
    {
        $this->actingAs($this->buatUser('admin'))->get('/home')->assertOk()->assertSee('Beranda Admin');
        $this->actingAs($this->buatUser('pengguna'))->get('/home')->assertOk()->assertSee('Cari Fasilitas');
    }

    public function test_akun_belum_aktif_dikeluarkan_oleh_middleware_role(): void
    {
        $this->actingAs($this->buatUser('petugas', 'pending'))
            ->get('/petugas')
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_logout_mengakhiri_sesi(): void
    {
        $this->actingAs($this->buatUser('pengguna'))
            ->post('/logout')
            ->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_route_reset_password_dan_verifikasi_email_dimatikan(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('password.request'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('verification.notice'));
    }
}
