<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use App\Models\Reservation;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

/**
 * Master fasilitas (US-16): CRUD + toggle status.
 *
 * Status diisi eksplisit (tidak di $fillable, D-06). Tidak ada penghapusan
 * fisik karena data reservasi/laporan historis wajib dipertahankan (A10).
 */
class FacilityController extends Controller
{
    private const ZONA = 'Asia/Jakarta';

    /** Daftar fasilitas + saringan nama/lokasi dan status. */
    public function index(Request $request): Renderable
    {
        $keyword = trim((string) $request->query('q'));
        $filterStatus = $request->query('status');

        $facilities = Facility::query()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $pola = addcslashes($keyword, '\\%_');
                $query->where(function ($saring) use ($pola) {
                    $saring->where('name', 'like', "%{$pola}%")
                        ->orWhere('location', 'like', "%{$pola}%");
                });
            })
            ->when(
                in_array($filterStatus, array_keys(Facility::STATUSES), true),
                fn ($query) => $query->where('status', $filterStatus)
            )
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        // Jumlah reservasi 'disetujui' mulai hari ini per fasilitas → peringatan A10.
        $peringatan = Reservation::query()
            ->approved()
            ->where('reservation_date', '>=', Carbon::now(self::ZONA)->toDateString())
            ->selectRaw('facility_id, count(*) as jumlah')
            ->groupBy('facility_id')
            ->pluck('jumlah', 'facility_id');

        return view('admin.facilities.index', [
            'facilities' => $facilities,
            'keyword' => $keyword,
            'filterStatus' => $filterStatus,
            'peringatan' => $peringatan,
        ]);
    }

    /** Formulir pembuatan fasilitas baru. */
    public function create(): Renderable
    {
        return view('admin.facilities.create');
    }

    /** Menyimpan fasilitas baru berstatus 'aktif' (diisi eksplisit, D-06). */
    public function store(Request $request): RedirectResponse
    {
        $data = self::validateFacility($request);

        $facility = new Facility($data);
        $facility->status = 'aktif';
        $facility->save();

        return redirect('/admin/facilities')->with(
            'success',
            'Fasilitas '.$facility->name.' berhasil ditambahkan dan berstatus aktif.'
        );
    }

    /** Formulir perubahan fasilitas (status diubah hanya lewat toggle). */
    public function edit(Facility $facility): Renderable
    {
        return view('admin.facilities.edit', ['facility' => $facility]);
    }

    /** Menyimpan perubahan master fasilitas; status tidak ikut berubah. */
    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $data = self::validateFacility($request);
        $facility->fill($data);
        $facility->save();

        return redirect('/admin/facilities')->with(
            'success',
            'Perubahan fasilitas '.$facility->name.' berhasil disimpan.'
        );
    }

    /**
     * Toggle status (whitelist transisi, A10):
     * aktif/dalam_perbaikan → nonaktif, nonaktif → aktif.
     * Reservasi disetujui mendatang TETAP ada (tidak dibatalkan); peringatan
     * hanya ditampilkan di halaman index sebelum konfirmasi.
     */
    public function toggle(Facility $facility): RedirectResponse
    {
        $target = $facility->status === 'nonaktif' ? 'aktif' : 'nonaktif';
        $fasilitasTerjangka = $facility->name;

        $facility->status = $target;
        $facility->save();

        $pesan = $target === 'nonaktif'
            ? "Fasilitas {$fasilitasTerjangka} dinonaktifkan. Reservasi disetujui yang sudah ada tetap berlaku; data historis tetap dipertahankan."
            : "Fasilitas {$fasilitasTerjangka} diaktifkan kembali.";

        return redirect('/admin/facilities')->with('success', $pesan);
    }

    /** Aturan validasi bersama form create/update. */
    private static function validateFacility(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', Rule::in(array_keys(Facility::TYPES))],
            'location' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}