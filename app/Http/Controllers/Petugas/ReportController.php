<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Pemrosesan laporan oleh petugas (SRS-008, US 8, 11, 12).
 */
class ReportController extends Controller
{
    /**
     * Whitelist transisi status laporan (aturan bisnis 7).
     * baru → diproses, baru → ditolak, diproses → selesai, diproses → ditolak.
     */
    private const ALLOWED_TRANSITIONS = [
        'baru'     => ['diproses', 'ditolak'],
        'diproses' => ['selesai', 'ditolak'],
    ];

    /**
     * US 8 — Daftar semua laporan dengan tab status.
     */
    public function index(Request $request): View
    {
        $tab = $request->query('status', 'baru');

        $query = Report::with(['user', 'facility', 'processor'])->latest();

        if ($tab !== 'semua' && array_key_exists($tab, Report::STATUSES)) {
            $query->status($tab);
        }

        $reports = $query->paginate(15)->withQueryString();

        return view('petugas.reports.index', [
            'reports'  => $reports,
            'statuses' => Report::STATUSES,
            'tab'      => $tab,
        ]);
    }

    /**
     * US 11 — Detail laporan + panel aksi.
     */
    public function show(Report $report): View
    {
        $report->load(['user', 'facility', 'processor']);

        // Tentukan transisi yang diperbolehkan dari status saat ini.
        $allowedStatuses = self::ALLOWED_TRANSITIONS[$report->status] ?? [];

        return view('petugas.reports.show', [
            'report'          => $report,
            'allowedStatuses' => $allowedStatuses,
        ]);
    }

    /**
     * US 11 — Ubah status laporan dengan whitelist transisi.
     */
    public function status(Request $request, Report $report): RedirectResponse
    {
        // Cek transisi yang diperbolehkan.
        $allowed = self::ALLOWED_TRANSITIONS[$report->status] ?? [];

        if (empty($allowed)) {
            return back()->with('error', 'Laporan ini sudah dalam status akhir dan tidak bisa diubah.');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in($allowed)],
            'resolution_note' => [
                Rule::requiredIf(in_array($request->status, ['selesai', 'ditolak'])),
                'nullable',
                'string',
                'min:5',
                'max:1000',
            ],
            'restore_facility' => ['nullable', 'boolean'],
        ], [
            'status.required'          => 'Status wajib dipilih.',
            'status.in'                => 'Transisi status tidak diperbolehkan.',
            'resolution_note.required' => 'Catatan resolusi wajib diisi saat menutup laporan.',
            'resolution_note.min'      => 'Catatan resolusi minimal 5 karakter.',
            'resolution_note.max'      => 'Catatan resolusi maksimal 1000 karakter.',
        ]);

        // Update status laporan — kolom non-fillable diisi eksplisit (D-06).
        $report->status = $validated['status'];
        $report->processed_by = $request->user()->id;
        $report->processed_at = now();

        if (in_array($validated['status'], ['selesai', 'ditolak'])) {
            $report->resolution_note = $validated['resolution_note'];
        }

        $report->save();

        // Opsional: kembalikan status fasilitas ke aktif saat laporan ditutup.
        if ($request->boolean('restore_facility') && $report->facility->status === 'dalam_perbaikan') {
            $facility = $report->facility;
            $facility->status = 'aktif';
            $facility->save();
        }

        $label = Report::STATUSES[$validated['status']] ?? $validated['status'];

        return redirect()->route('petugas.reports.show', $report)
            ->with('success', "Status laporan berhasil diubah menjadi \"{$label}\".");
    }

    /**
     * US 12 — Ubah status fasilitas: aktif ↔ dalam_perbaikan.
     * Petugas TIDAK boleh menyentuh status 'nonaktif' (wewenang Admin).
     */
    public function facilityStatus(Request $request, Report $report): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['repair', 'restore'])],
        ], [
            'action.required' => 'Aksi wajib dipilih.',
            'action.in'       => 'Aksi tidak valid.',
        ]);

        $facility = $report->facility;

        // Whitelist transisi fasilitas.
        if ($validated['action'] === 'repair') {
            if ($facility->status !== 'aktif') {
                return back()->with('error', 'Hanya fasilitas berstatus aktif yang bisa ditandai dalam perbaikan.');
            }
            $facility->status = 'dalam_perbaikan';
        } elseif ($validated['action'] === 'restore') {
            if ($facility->status !== 'dalam_perbaikan') {
                return back()->with('error', 'Hanya fasilitas berstatus dalam perbaikan yang bisa dikembalikan ke aktif.');
            }
            $facility->status = 'aktif';
        }

        $facility->save();

        $label = Facility::STATUSES[$facility->status] ?? $facility->status;

        return redirect()->route('petugas.reports.show', $report)
            ->with('success', "Status fasilitas \"{$facility->name}\" berhasil diubah menjadi \"{$label}\".");
    }
}
