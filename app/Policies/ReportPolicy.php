<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

/**
 * Policy untuk laporan pengguna (SRS-007).
 * Laravel auto-discovery: App\Policies\ReportPolicy ↔ App\Models\Report.
 */
class ReportPolicy
{
    /**
     * Hanya pelapor yang boleh melihat detail laporannya sendiri.
     */
    public function view(User $user, Report $report): bool
    {
        return $user->id === $report->user_id;
    }
}
