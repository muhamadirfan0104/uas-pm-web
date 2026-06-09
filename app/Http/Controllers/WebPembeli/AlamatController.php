<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AlamatController extends Controller
{
    public function index(): View
    {
        $alamat = Alamat::query()
            ->where('user_id', Auth::id())
            ->orderByDesc('utama')
            ->latest()
            ->get();

        return view('pembeli.alamat.index', compact('alamat'));
    }

    public function create(): View
    {
        $alamat = new Alamat();

        return view('pembeli.alamat.form', compact('alamat'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasiAlamat($request);

        DB::transaction(function () use ($data) {
            $jumlahAlamat = Alamat::query()
                ->where('user_id', Auth::id())
                ->count();

            $jadikanUtama = (bool) ($data['utama'] ?? false);

            if ($jumlahAlamat === 0) {
                $jadikanUtama = true;
            }

            if ($jadikanUtama) {
                Alamat::query()
                    ->where('user_id', Auth::id())
                    ->update(['utama' => false]);
            }

            Alamat::create([
                'user_id' => Auth::id(),
                'nama_penerima' => $data['nama_penerima'],
                'telepon' => $data['telepon'],
                'email_penerima' => $data['email_penerima'] ?? null,
                'alamat_lengkap' => $data['alamat_lengkap'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'utama' => $jadikanUtama,
            ]);
        });

        return $this->redirectSetelahSimpan($request, 'Alamat berhasil ditambahkan.');
    }

    public function edit(Alamat $alamat): View
    {
        $this->pastikanAlamatMilikPembeli($alamat);

        return view('pembeli.alamat.form', compact('alamat'));
    }

    public function update(Request $request, Alamat $alamat): RedirectResponse
    {
        $this->pastikanAlamatMilikPembeli($alamat);

        $data = $this->validasiAlamat($request);

        DB::transaction(function () use ($alamat, $data) {
            $jadikanUtama = (bool) ($data['utama'] ?? false);

            if ($jadikanUtama) {
                Alamat::query()
                    ->where('user_id', Auth::id())
                    ->where('id', '!=', $alamat->id)
                    ->update(['utama' => false]);
            }

            $alamat->update([
                'nama_penerima' => $data['nama_penerima'],
                'telepon' => $data['telepon'],
                'email_penerima' => $data['email_penerima'] ?? null,
                'alamat_lengkap' => $data['alamat_lengkap'],
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'utama' => $jadikanUtama || $alamat->utama,
            ]);
        });

        return $this->redirectSetelahSimpan($request, 'Alamat berhasil diperbarui.');
    }

    public function destroy(Alamat $alamat): RedirectResponse
    {
        $this->pastikanAlamatMilikPembeli($alamat);

        DB::transaction(function () use ($alamat) {
            $wasUtama = $alamat->utama;

            $alamat->delete();

            if ($wasUtama) {
                $alamatPengganti = Alamat::query()
                    ->where('user_id', Auth::id())
                    ->latest()
                    ->first();

                if ($alamatPengganti) {
                    $alamatPengganti->update([
                        'utama' => true,
                    ]);
                }
            }
        });

        return redirect()
            ->route('pembeli-web.alamat.index')
            ->with('success', 'Alamat berhasil dihapus.');
    }

    public function setUtama(Alamat $alamat): RedirectResponse
    {
        $this->pastikanAlamatMilikPembeli($alamat);

        DB::transaction(function () use ($alamat) {
            Alamat::query()
                ->where('user_id', Auth::id())
                ->update(['utama' => false]);

            $alamat->update([
                'utama' => true,
            ]);
        });

        return redirect()
            ->route('pembeli-web.alamat.index')
            ->with('success', 'Alamat utama berhasil diubah.');
    }

    private function redirectSetelahSimpan(Request $request, string $message): RedirectResponse
    {
        if ($request->input('redirect') === 'checkout') {
            return redirect()
                ->route('pembeli-web.checkout.index')
                ->with('success', $message);
        }

        return redirect()
            ->route('pembeli-web.alamat.index')
            ->with('success', $message);
    }

    private function validasiAlamat(Request $request): array
    {
        return $request->validate([
            'nama_penerima' => ['required', 'string', 'max:150'],
            'telepon' => ['required', 'string', 'max:30'],
            'email_penerima' => ['required', 'email', 'max:150'],
            'alamat_lengkap' => ['required', 'string', 'max:1000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'utama' => ['nullable', 'boolean'],
        ], [
            'nama_penerima.required' => 'Nama penerima wajib diisi.',
            'nama_penerima.max' => 'Nama penerima maksimal 150 karakter.',
            'telepon.required' => 'Nomor telepon wajib diisi.',
            'telepon.max' => 'Nomor telepon maksimal 30 karakter.',
            'email_penerima.required' => 'Email penerima wajib diisi.',
            'email_penerima.email' => 'Email penerima harus valid.',
            'email_penerima.max' => 'Email penerima maksimal 150 karakter.',
            'alamat_lengkap.required' => 'Alamat lengkap wajib diisi.',
            'alamat_lengkap.max' => 'Alamat lengkap maksimal 1000 karakter.',
            'latitude.numeric' => 'Latitude harus berupa angka.',
            'latitude.between' => 'Latitude harus berada di antara -90 sampai 90.',
            'longitude.numeric' => 'Longitude harus berupa angka.',
            'longitude.between' => 'Longitude harus berada di antara -180 sampai 180.',
        ]);
    }

    private function pastikanAlamatMilikPembeli(Alamat $alamat): void
    {
        abort_unless((int) $alamat->user_id === (int) Auth::id(), 403);
    }
}