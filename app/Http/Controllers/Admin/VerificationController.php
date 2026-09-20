<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Verifikasi pendaftaran mandiri oleh admin (US-15).
 *
 * Hanya akun berstatus 'pending' yang bisa disetujui atau ditolak, sehingga
 * status yang sudah final tidak bisa diubah lagi lewat permintaan berulang.
 */
class VerificationController extends Controller
{
    /** Antrean akun yang menunggu verifikasi, yang paling lama menunggu di atas. */
    public function index(): Renderable
    {
        return view('admin.verifications.index', [
            'users' => User::query()
                ->where('status', 'pending')
                ->orderBy('created_at')
                ->paginate(15),
        ]);
    }

    /** Menyetujui akun: status menjadi aktif dan catatan penolakan dikosongkan. */
    public function approve(User $user): RedirectResponse
    {
        if ($user->status !== 'pending') {
            return redirect('/admin/verifications')
                ->with('error', 'Akun '.$user->name.' sudah diproses sebelumnya.');
        }

        $user->status = 'aktif';
        $user->verification_note = null;
        $user->verified_at = Carbon::now('Asia/Jakarta');
        $user->save();

        return redirect('/admin/verifications')
            ->with('success', 'Akun '.$user->name.' disetujui dan sudah bisa masuk.');
    }

    /** Menolak akun disertai catatan yang akan dilihat pendaftar saat mencoba masuk. */
    public function reject(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'verification_note' => ['required', 'string', 'max:500'],
        ]);

        if ($user->status !== 'pending') {
            return redirect('/admin/verifications')
                ->with('error', 'Akun '.$user->name.' sudah diproses sebelumnya.');
        }

        $user->status = 'ditolak';
        $user->verification_note = $data['verification_note'];
        $user->verified_at = Carbon::now('Asia/Jakarta');
        $user->save();

        return redirect('/admin/verifications')
            ->with('success', 'Akun '.$user->name.' ditolak beserta catatannya.');
    }
}
