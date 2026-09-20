<?php

namespace App\Models;

use App\Services\AvailabilityService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory;

    /** Status reservasi: value ⇒ label Indonesia. */
    public const STATUSES = [
        'menunggu' => 'Menunggu',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',
        'dibatalkan' => 'Dibatalkan',
    ];

    /**
     * user_id, status, dan semua kolom pemrosesan diisi eksplisit di controller (D-06).
     *
     * @var list<string>
     */
    protected $fillable = [
        'facility_id',
        'reservation_date',
        'start_time',
        'end_time',
        'purpose',
    ];

    /**
     * Kolom TIME sengaja TIDAK di-cast; dibaca/ditulis sebagai string 'H:i' (D-05).
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reservation_date' => 'date',
            'cancelled_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return BelongsTo<Facility, $this> */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /** Petugas yang menyetujui/menolak. @return BelongsTo<User, $this> */
    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /** Pihak yang membatalkan. @return BelongsTo<User, $this> */
    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * @param  Builder<Reservation>  $query
     * @return Builder<Reservation>
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'disetujui');
    }

    /**
     * @param  Builder<Reservation>  $query
     * @return Builder<Reservation>
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /** Rentang jam siap tampil, mis. '09.00–10.30'. */
    public function timeRange(): string
    {
        $service = app(AvailabilityService::class);

        return str_replace(':', '.', $service->normalizeTime($this->start_time))
            .'–'
            .str_replace(':', '.', $service->normalizeTime($this->end_time));
    }

    /** Waktu mulai (tanggal + jam) dalam zona WIB. */
    public function startsAt(): Carbon
    {
        $service = app(AvailabilityService::class);

        return Carbon::createFromFormat(
            'Y-m-d H:i',
            $this->reservation_date->format('Y-m-d').' '.$service->normalizeTime($this->start_time),
            'Asia/Jakarta'
        );
    }
}
