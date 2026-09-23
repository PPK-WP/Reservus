<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Laporan pengguna: buat, lihat riwayat, detail (SRS-007, US 6 & 7).
 */
class ReportController extends Controller
{
    /**
     * US 7 — Riwayat laporan milik pengguna yang sedang login.
     */
    public function index(Request $request): View
    {
        $query = $request->user()->reports()->with('facility')->latest();

        // Filter status (tab/pills).
        if ($request->filled('status') && array_key_exists($request->status, Report::STATUSES)) {
            $query->status($request->status);
        }

        $reports = $query->paginate(10)->withQueryString();

        return view('reports.index', [
            'reports'       => $reports,
            'statuses'      => Report::STATUSES,
            'currentStatus' => $request->status,
        ]);
    }

    /**
     * US 6 — Form membuat laporan baru.
     */
    public function create(Request $request): View
    {
        // Prefill dari query string ?facility=
        $selectedFacility = null;
        if ($request->filled('facility')) {
            $selectedFacility = Facility::find($request->facility);
        }

        return view('reports.create', [
            'facilities'       => Facility::orderBy('name')->get(),
            'categories'       => Report::CATEGORIES,
            'selectedFacility' => $selectedFacility,
        ]);
    }

    /**
     * US 6 — Simpan laporan baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'facility_id' => ['required', 'exists:facilities,id'],
            'category'    => ['required', Rule::in(array_keys(Report::CATEGORIES))],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'photo'       => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'facility_id.required' => 'Fasilitas wajib dipilih.',
            'facility_id.exists'   => 'Fasilitas tidak ditemukan.',
            'category.required'    => 'Kategori wajib dipilih.',
            'category.in'          => 'Kategori tidak valid.',
            'description.required' => 'Deskripsi wajib diisi.',
            'description.min'      => 'Deskripsi minimal 10 karakter.',
            'description.max'      => 'Deskripsi maksimal 2000 karakter.',
            'photo.image'          => 'File harus berupa gambar.',
            'photo.mimes'          => 'Format foto harus JPG, JPEG, atau PNG.',
            'photo.max'            => 'Ukuran foto maksimal 2 MB.',
        ]);

        // Simpan foto jika ada (nama acak, disk public).
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('reports', 'public');
        }

        // Simpan via relasi — user_id diisi otomatis oleh relasi (D-06).
        // Status default 'baru' dari database.
        $request->user()->reports()->create([
            'facility_id' => $validated['facility_id'],
            'category'    => $validated['category'],
            'description' => $validated['description'],
            'photo'       => $photoPath,
        ]);

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dikirim.');
    }

    /**
     * US 7 — Detail laporan milik pengguna.
     */
    public function show(Report $report): View
    {
        Gate::authorize('view', $report);

        $report->load(['facility', 'processor']);

        return view('reports.show', compact('report'));
    }
}
