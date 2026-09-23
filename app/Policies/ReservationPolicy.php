<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

/**
 * Policy untuk detail dan pembatalan reservasi pengguna (SRS-005).
 */
class ReservationPolicy
{
    public function view(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id;
    }

    public function cancel(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id;
    }
}
