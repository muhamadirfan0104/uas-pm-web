<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanToko;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengaturanTokoController extends Controller
{
    public function edit(): View
    {
        $pengaturan = PengaturanToko::utama();

        return view('admin.pengaturan.edit', compact('pengaturan'));
    }

    public function update(Request $request): RedirectResponse
    {
        $pengaturan = PengaturanToko::utama();

        $data = $request->validate([
            'nama' => ['nullable', 'string', 'max:100'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
            'jam_buka' => ['nullable', 'string', 'max:50'],
            'jam_tutup' => ['nullable', 'string', 'max:50'],
            'alamat' => ['nullable', 'string'],
            'latitude_toko' => ['nullable', 'numeric'],
            'longitude_toko' => ['nullable', 'numeric'],
            'tarif_per_km' => ['nullable', 'numeric', 'min:0'],
            'biaya_minimum_pengiriman' => ['nullable', 'numeric', 'min:0'],
            'radius_maksimal_km' => ['nullable', 'numeric', 'min:0'],
            'area_pengiriman' => ['nullable', 'string', 'max:255'],
            'info_pembayaran' => ['nullable', 'string'],
            'tentang' => ['nullable', 'string'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logo')) {
            if ($pengaturan->logo_url) {
                Storage::disk('public')->delete($pengaturan->logo_url);
            }
            $data['logo_url'] = $request->file('logo')->store('toko', 'public');
        }

        unset($data['logo']);
        $pengaturan->update($data);

        return back()->with('success', 'Pengaturan toko berhasil diperbarui.');
    }
}
