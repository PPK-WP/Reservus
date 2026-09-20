<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Beranda setelah login — menyalurkan pengguna ke ruang kerja sesuai peran.
 */
class HomeController extends Controller
{
    public function __invoke(Request $request): Renderable|RedirectResponse
    {
        $user = $request->user();

        if ($user->isPetugas()) {
            return redirect('/petugas');
        }

        if ($user->isAdmin()) {
            return view('home.admin', [
                'pendingCount' => User::query()->where('status', 'pending')->count(),
            ]);
        }

        return view('home.pengguna');
    }
}
