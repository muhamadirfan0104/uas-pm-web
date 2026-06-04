<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class UlasanController extends Controller
{
    public function index(Request $request): View
    {
        $query = Ulasan::with(['user', 'produk', 'pesanan'])->latest();

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('status')) {
            $query->where('ditampilkan', $request->status === 'tampil');
        }

        $ulasan = $query->paginate(12)->withQueryString();

        $stats = [
            'rata_rata' => round((float) Ulasan::avg('rating'), 1),
            'total' => Ulasan::count(),
            'foto' => Ulasan::whereNotNull('foto_ulasan')->count(),
            'disembunyikan' => Ulasan::where('ditampilkan', false)->count(),
        ];

        return view('admin.ulasan.index', compact('ulasan', 'stats'));
    }

    public function toggle(Ulasan $ulasan): RedirectResponse
    {
        $ulasan->update(['ditampilkan' => ! $ulasan->ditampilkan]);

        return back()->with('success', 'Status ulasan berhasil diubah.');
    }

    public function destroy(Ulasan $ulasan): RedirectResponse
    {
        if ($ulasan->foto_ulasan) {
            Storage::disk('public')->delete($ulasan->foto_ulasan);
        }
        $ulasan->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
