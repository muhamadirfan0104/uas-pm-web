<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanToko;
use App\Models\RekeningToko;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PengaturanTokoController extends Controller
{
    public function edit(): View
    {
        $pengaturan = PengaturanToko::utama();
        $rekeningList = RekeningToko::query()
            ->orderByDesc('utama')
            ->orderBy('urutan')
            ->orderBy('id')
            ->get();

        return view('admin.pengaturan.edit', compact('pengaturan', 'rekeningList'));
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
            'rekening' => ['nullable', 'array'],
            'rekening.*.nama_bank' => ['nullable', 'string', 'max:100'],
            'rekening.*.nomor_rekening' => ['nullable', 'string', 'max:80'],
            'rekening.*.atas_nama' => ['nullable', 'string', 'max:150'],
            'rekening.*.aktif' => ['nullable', 'boolean'],
            'rekening_utama' => ['nullable', 'integer'],
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

        $rekeningInput = collect($data['rekening'] ?? [])
            ->map(function ($item, $index) {
                return [
                    'nama_bank' => trim((string) ($item['nama_bank'] ?? '')),
                    'nomor_rekening' => trim((string) ($item['nomor_rekening'] ?? '')),
                    'atas_nama' => trim((string) ($item['atas_nama'] ?? '')),
                    'aktif' => (bool) ($item['aktif'] ?? false),
                    'urutan' => $index + 1,
                ];
            })
            ->filter(fn ($item) => $item['nama_bank'] !== '' || $item['nomor_rekening'] !== '' || $item['atas_nama'] !== '')
            ->values();

        $utamaIndex = (int) $request->input('rekening_utama', 0);

        unset($data['logo_url'], $data['rekening'], $data['rekening_utama']);

        if ($request->hasFile('logo_url')) {
            if ($pengaturan->logo_url) {
                Storage::disk('public')->delete($pengaturan->logo_url);
            }

            $data['logo_url'] = $request->file('logo_url')->store('pengaturan/logo', 'public');
        }

        $data['tarif_per_km'] = $data['tarif_per_km'] ?? 0;
        $data['biaya_minimum_pengiriman'] = $data['biaya_minimum_pengiriman'] ?? 0;
        $data['radius_maksimal_km'] = $data['radius_maksimal_km'] ?? 0;

        DB::transaction(function () use ($pengaturan, $data, $rekeningInput, $utamaIndex) {
            $pengaturan->update($data);

            RekeningToko::query()->delete();

            $rekeningInput->each(function ($item, $index) use ($pengaturan, $utamaIndex) {
                RekeningToko::query()->create([
                    'nama_bank' => $item['nama_bank'] !== '' ? $item['nama_bank'] : 'Bank',
                    'nomor_rekening' => $item['nomor_rekening'] !== '' ? $item['nomor_rekening'] : '-',
                    'atas_nama' => $item['atas_nama'] !== '' ? $item['atas_nama'] : ($pengaturan->nama ?: 'SiTahu'),
                    'aktif' => $item['aktif'],
                    'utama' => $index === $utamaIndex,
                    'urutan' => $item['urutan'],
                ]);
            });

            $rekeningUtama = RekeningToko::query()
                ->orderByDesc('utama')
                ->orderBy('urutan')
                ->first();

            if ($rekeningUtama) {
                $pengaturan->update([
                    'bank_nama' => $rekeningUtama->nama_bank,
                    'bank_nomor_rekening' => $rekeningUtama->nomor_rekening,
                    'bank_atas_nama' => $rekeningUtama->atas_nama,
                ]);
            }
        });

        return redirect()
            ->route('admin.pengaturan.edit')
            ->with('success', 'Pengaturan toko berhasil diperbarui.');
    }
}