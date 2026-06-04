<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PembeliController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::where('role', 'pembeli')
            ->withCount('pesanan')
            ->withSum('pesanan as total_belanja', 'total_bayar')
            ->latest();

        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->q . '%')
                    ->orWhere('email', 'like', '%' . $request->q . '%')
                    ->orWhere('telepon', 'like', '%' . $request->q . '%');
            });
        }

        $pembeli = $query->paginate(12)->withQueryString();

        return view('admin.pembeli.index', compact('pembeli'));
    }

    public function show(User $pembeli): View
    {
        abort_unless($pembeli->role === 'pembeli', 404);

        $pembeli->load(['alamat', 'pesanan' => fn ($q) => $q->latest('tanggal_pesanan')->limit(10)]);

        return view('admin.pembeli.show', compact('pembeli'));
    }

    public function toggle(User $pembeli): RedirectResponse
    {
        abort_unless($pembeli->role === 'pembeli', 404);

        $pembeli->update(['aktif' => ! $pembeli->aktif]);

        return back()->with('success', 'Status akun pembeli berhasil diperbarui.');
    }
}
