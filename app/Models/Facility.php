<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    /** @use HasFactory<\Database\Factories\FacilityFactory> */
    use HasFactory;

    /** Jenis fasilitas: value ⇒ label Indonesia. */
    public const TYPES = [
        'ruang_kelas' => 'Ruang Kelas',
        'aula' => 'Aula',
        'laboratorium' => 'Laboratorium',
        'alat' => 'Alat',
        'lapangan' => 'Lapangan',
    ];

    /** Status fasilitas: value ⇒ label Indonesia. */
    public const STATUSES = [
        'aktif' => 'Aktif',
        'dalam_perbaikan' => 'Dalam Perbaikan',
        'nonaktif' => 'Nonaktif',
    ];

    /**
     * status TIDAK boleh diisi massal (D-06).
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'type',
        'location',
        'capacity',
        'description',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }

    /** Hanya fasilitas 'aktif' yang boleh direservasi (aturan bisnis 5). */
    public function isReservable(): bool
    {
        return $this->status === 'aktif';
    }

    /**
     * Fasilitas yang tampil di katalog publik: aktif + dalam perbaikan.
     *
     * @param  Builder<Facility>  $query
     * @return Builder<Facility>
     */
    public function scopePublicCatalog(Builder $query): Builder
    {
        return $query->whereIn('status', ['aktif', 'dalam_perbaikan']);
    }

    /** @return HasMany<Reservation, $this> */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /** @return HasMany<Report, $this> */
    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
