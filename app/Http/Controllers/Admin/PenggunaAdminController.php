<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PenggunaAdminController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->where('role', 'admin');

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('telepon', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('aktif', $request->status === 'aktif');
        }

        match ($request->input('sort')) {
            'nama' => $query->orderBy('name'),
            'status' => $query->orderByDesc('aktif')->orderBy('name'),
            'terlama' => $query->oldest(),
            default => $query->latest(),
        };

        $users = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => User::where('role', 'admin')->count(),
            'aktif' => User::where('role', 'admin')->where('aktif', true)->count(),
            'nonaktif' => User::where('role', 'admin')->where('aktif', false)->count(),
            'pembeli' => User::where('role', 'pembeli')->count(),
        ];

        return view('admin.pengguna-admin.index', compact('users', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'aktif' => ['nullable', 'boolean'],
        ]) + ['aktif' => false];

        $data['role'] = 'admin';

        User::create($data);

        return back()->with('success', 'Akun admin berhasil ditambahkan.');
    }

    public function update(Request $request, User $penggunaAdmin): RedirectResponse
    {
        abort_unless($penggunaAdmin->role === 'admin', 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($penggunaAdmin->id)],
            'telepon' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'aktif' => ['nullable', 'boolean'],
        ]) + ['aktif' => false];

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $data['role'] = 'admin';

        if ($penggunaAdmin->id === $request->user()->id && ! $data['aktif']) {
            return back()->withErrors(['akun' => 'Akun yang sedang login tidak boleh dinonaktifkan.']);
        }

        if ($penggunaAdmin->aktif && ! $data['aktif'] && $this->jumlahAdminAktif() <= 1) {
            return back()->withErrors(['akun' => 'Minimal harus ada satu akun admin aktif.']);
        }

        $penggunaAdmin->update($data);

        return back()->with('success', 'Akun admin berhasil diperbarui.');
    }

    public function toggle(Request $request, User $penggunaAdmin): RedirectResponse
    {
        abort_unless($penggunaAdmin->role === 'admin', 404);

        if ($penggunaAdmin->id === $request->user()->id) {
            return back()->withErrors(['akun' => 'Akun yang sedang login tidak boleh dinonaktifkan.']);
        }

        if ($penggunaAdmin->aktif && $this->jumlahAdminAktif() <= 1) {
            return back()->withErrors(['akun' => 'Minimal harus ada satu akun admin aktif.']);
        }

        $penggunaAdmin->update([
            'aktif' => ! $penggunaAdmin->aktif,
        ]);

        return back()->with('success', 'Status admin berhasil diubah.');
    }

    public function destroy(Request $request, User $penggunaAdmin): RedirectResponse
    {
        abort_unless($penggunaAdmin->role === 'admin', 404);

        if ($penggunaAdmin->id === $request->user()->id) {
            return back()->withErrors(['akun' => 'Akun yang sedang login tidak boleh dihapus.']);
        }

        if ($penggunaAdmin->aktif && $this->jumlahAdminAktif() <= 1) {
            return back()->withErrors(['akun' => 'Minimal harus ada satu akun admin aktif.']);
        }

        $penggunaAdmin->delete();

        return back()->with('success', 'Akun admin berhasil dihapus.');
    }

    private function jumlahAdminAktif(): int
    {
        return User::where('role', 'admin')->where('aktif', true)->count();
    }
}
