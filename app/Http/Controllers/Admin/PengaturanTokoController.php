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
            'nama' => ['required', 'string', 'max:150'],
            'logo_url' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'alamat' => ['required', 'string', 'max:1000'],
            'telepon' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'jam_buka' => ['nullable', 'string', 'max:150'],
            'jam_tutup' => ['nullable', 'string', 'max:150'],
            'latitude_toko' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude_toko' => ['nullable', 'numeric', 'between:-180,180'],
            'tarif_per_km' => ['nullable', 'numeric', 'min:0'],
            'biaya_minimum_pengiriman' => ['nullable', 'numeric', 'min:0'],
            'radius_maksimal_km' => ['nullable', 'numeric', 'min:0'],
            'area_pengiriman' => ['nullable', 'string', 'max:1000'],
            'info_pembayaran' => ['nullable', 'string', 'max:1500'],
            'bank_nama' => ['nullable', 'string', 'max:100'],
            'bank_nomor_rekening' => ['nullable', 'string', 'max:60'],
            'bank_atas_nama' => ['nullable', 'string', 'max:120'],
            'tentang' => ['nullable', 'string', 'max:2000'],
        ], [
            'nama.required' => 'Nama toko wajib diisi.',
            'nama.max' => 'Nama toko maksimal 150 karakter.',
            'logo_url.image' => 'Logo harus berupa gambar.',
            'logo_url.mimes' => 'Format logo harus jpg, jpeg, png, atau webp.',
            'logo_url.max' => 'Ukuran logo maksimal 4 MB.',
            'alamat.required' => 'Alamat toko wajib diisi.',
            'alamat.max' => 'Alamat maksimal 1000 karakter.',
            'email.email' => 'Format email tidak valid.',
            'latitude_toko.numeric' => 'Latitude harus berupa angka.',
            'latitude_toko.between' => 'Latitude harus berada di antara -90 sampai 90.',
            'longitude_toko.numeric' => 'Longitude harus berupa angka.',
            'longitude_toko.between' => 'Longitude harus berada di antara -180 sampai 180.',
            'tarif_per_km.numeric' => 'Tarif per km harus berupa angka.',
            'tarif_per_km.min' => 'Tarif per km tidak boleh minus.',
            'biaya_minimum_pengiriman.numeric' => 'Biaya minimum pengiriman harus berupa angka.',
            'biaya_minimum_pengiriman.min' => 'Biaya minimum pengiriman tidak boleh minus.',
            'radius_maksimal_km.numeric' => 'Radius maksimal harus berupa angka.',
            'radius_maksimal_km.min' => 'Radius maksimal tidak boleh minus.',
        ]);

        unset($data['logo_url']);

        if ($request->hasFile('logo_url')) {
            if ($pengaturan->logo_url) {
                Storage::disk('public')->delete($pengaturan->logo_url);
            }

            $data['logo_url'] = $request->file('logo_url')->store('pengaturan/logo', 'public');
        }

        $data['tarif_per_km'] = $data['tarif_per_km'] ?? 0;
        $data['biaya_minimum_pengiriman'] = $data['biaya_minimum_pengiriman'] ?? 0;
        $data['radius_maksimal_km'] = $data['radius_maksimal_km'] ?? 0;

        $pengaturan->update($data);

        return redirect()
            ->route('admin.pengaturan.edit')
            ->with('success', 'Pengaturan toko berhasil diperbarui.');
    }
}