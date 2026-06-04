<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanToko;
use App\Models\Pengiriman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengirimanController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pengiriman::with(['pesanan.user'])->latest();

        if ($request->filled('metode')) {
            $query->where('metode', $request->metode);
        }

        if ($request->filled('status')) {
            $query->where('status_pengiriman', $request->status);
        }

        $pengiriman = $query->paginate(12)->withQueryString();
        $pengaturan = PengaturanToko::utama();

        return view('admin.pengiriman.index', compact('pengiriman', 'pengaturan'));
    }


    public function updatePengaturan(Request $request): RedirectResponse
    {
        $pengaturan = PengaturanToko::utama();

        $data = $request->validate([
            'alamat' => ['nullable', 'string'],
            'jam_buka' => ['nullable', 'string', 'max:50'],
            'jam_tutup' => ['nullable', 'string', 'max:50'],
            'latitude_toko' => ['nullable', 'numeric'],
            'longitude_toko' => ['nullable', 'numeric'],
            'tarif_per_km' => ['nullable', 'numeric', 'min:0'],
            'biaya_minimum_pengiriman' => ['nullable', 'numeric', 'min:0'],
            'radius_maksimal_km' => ['nullable', 'numeric', 'min:0'],
            'area_pengiriman' => ['nullable', 'string', 'max:255'],
        ]);

        $pengaturan->update($data);

        return back()->with('success', 'Pengaturan logistik toko berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Pengiriman $pengiriman): RedirectResponse
    {
        $data = $request->validate([
            'status_pengiriman' => ['required', 'in:siap_diambil,dalam_pengantaran,selesai'],
        ]);

        $pengiriman->update($data);

        $statusPesanan = match ($data['status_pengiriman']) {
            'siap_diambil' => 'siap_diambil',
            'dalam_pengantaran' => 'dalam_pengantaran',
            'selesai' => 'selesai',
        };

        $pengiriman->pesanan?->update(['status' => $statusPesanan]);

        return back()->with('success', 'Status pengiriman berhasil diperbarui.');
    }
}
