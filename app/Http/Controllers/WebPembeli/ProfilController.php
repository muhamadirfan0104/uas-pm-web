<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use App\Models\PengaturanToko;
use App\Models\Pesanan;
use App\Models\Ulasan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(): View
    {
        $pengaturan = PengaturanToko::utama();
        $user = Auth::user();

        $pesananQuery = Pesanan::query()
            ->where('user_id', $user->id);

        $statProfil = [
            'total_pesanan' => (clone $pesananQuery)->count(),
            'pesanan_aktif' => (clone $pesananQuery)
                ->whereNotIn('status', ['selesai', 'dibatalkan'])
                ->count(),
            'pesanan_selesai' => (clone $pesananQuery)
                ->where('status', 'selesai')
                ->count(),
            'total_belanja' => (clone $pesananQuery)
                ->where('status_pembayaran', 'dibayar')
                ->sum('total_bayar'),
            'alamat' => Alamat::query()
                ->where('user_id', $user->id)
                ->count(),
            'ulasan' => Ulasan::query()
                ->where('user_id', $user->id)
                ->count(),
        ];

        $pesananTerbaru = Pesanan::query()
            ->with(['item.produk.gambarUtama', 'pembayaran', 'pengiriman'])
            ->where('user_id', $user->id)
            ->latest('tanggal_pesanan')
            ->take(4)
            ->get();

        $alamatUtama = Alamat::query()
            ->where('user_id', $user->id)
            ->where('utama', true)
            ->first();

        $alamatList = Alamat::query()
            ->where('user_id', $user->id)
            ->orderByDesc('utama')
            ->latest()
            ->take(3)
            ->get();

        return view('pembeli.profil', compact(
            'pengaturan',
            'user',
            'statProfil',
            'pesananTerbaru',
            'alamatUtama',
            'alamatList'
        ));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'telepon' => ['required', 'string', 'max:30'],
        ], [
            'name.required' => 'Nama akun wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email belum sesuai.',
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'telepon.required' => 'Nomor HP wajib diisi.',
            'telepon.max' => 'Nomor HP maksimal 30 karakter.',
        ]);

        $telepon = $this->normalisasiTeleponUntukSimpan((string) $data['telepon']);

        $teleponDipakai = User::query()
            ->whereKeyNot($user->id)
            ->whereIn('telepon', $this->variasiTelepon($telepon))
            ->exists();

        if ($teleponDipakai) {
            return back()
                ->withErrors(['telepon' => 'Nomor HP ini sudah dipakai akun lain.'])
                ->withInput()
                ->with('profil_tab', 'akun');
        }

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'telepon' => $telepon,
        ]);

        return back()
            ->with('success', 'Profil akun berhasil diperbarui.')
            ->with('profil_tab', 'akun');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'password_lama' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'password_lama.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak sama.',
        ]);

        if (! Hash::check($data['password_lama'], $user->password)) {
            return back()
                ->withErrors(['password_lama' => 'Password saat ini tidak sesuai.'])
                ->with('profil_tab', 'keamanan');
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return back()
            ->with('success', 'Password berhasil diperbarui.')
            ->with('profil_tab', 'keamanan');
    }

    private function variasiTelepon(string $telepon): array
    {
        $asli = trim($telepon);
        $digits = preg_replace('/[^0-9]/', '', $asli) ?: '';
        $candidates = [$asli, $digits];

        if (str_starts_with($digits, '62')) {
            $candidates[] = '0' . substr($digits, 2);
            $candidates[] = '+' . $digits;
        }

        if (str_starts_with($digits, '0')) {
            $candidates[] = '62' . substr($digits, 1);
            $candidates[] = '+62' . substr($digits, 1);
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    private function normalisasiTeleponUntukSimpan(string $telepon): string
    {
        $digits = preg_replace('/[^0-9]/', '', $telepon) ?: '';

        if (str_starts_with($digits, '62')) {
            return '0' . substr($digits, 2);
        }

        return $digits ?: trim($telepon);
    }
}
