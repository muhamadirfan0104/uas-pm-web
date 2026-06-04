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
        $query = User::query()
            ->whereIn('role', ['admin', 'kasir'])
            ->latest();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('telepon', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('aktif', $request->status === 'aktif');
        }

        $users = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => User::whereIn('role', ['admin', 'kasir'])->count(),
            'admin' => User::where('role', 'admin')->count(),
            'kasir' => User::where('role', 'kasir')->count(),
            'aktif' => User::whereIn('role', ['admin', 'kasir'])->where('aktif', true)->count(),
        ];

        return view('admin.pengguna-admin.index', compact('users', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['admin', 'kasir'])],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'aktif' => ['nullable', 'boolean'],
        ]) + ['aktif' => false];

        User::create($data);

        return back()->with('success', 'Pengguna admin berhasil ditambahkan.');
    }

    public function update(Request $request, User $penggunaAdmin): RedirectResponse
    {
        abort_unless(in_array($penggunaAdmin->role, ['admin', 'kasir'], true), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($penggunaAdmin->id)],
            'telepon' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in(['admin', 'kasir'])],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'aktif' => ['nullable', 'boolean'],
        ]) + ['aktif' => false];

        if (empty($data['password'])) {
            unset($data['password']);
        }

        if ($penggunaAdmin->id === $request->user()->id && (! $data['aktif'] || $data['role'] !== 'admin')) {
            return back()->withErrors(['akun' => 'Akun yang sedang login tidak boleh dinonaktifkan atau diubah menjadi kasir.']);
        }

        if ($penggunaAdmin->role === 'admin' && ($data['role'] !== 'admin' || ! $data['aktif']) && $this->jumlahAdminAktif() <= 1) {
            return back()->withErrors(['akun' => 'Minimal harus ada satu akun admin aktif.']);
        }

        $penggunaAdmin->update($data);

        return back()->with('success', 'Pengguna admin berhasil diperbarui.');
    }

    public function toggle(Request $request, User $penggunaAdmin): RedirectResponse
    {
        abort_unless(in_array($penggunaAdmin->role, ['admin', 'kasir'], true), 404);

        if ($penggunaAdmin->id === $request->user()->id) {
            return back()->withErrors(['akun' => 'Akun yang sedang login tidak boleh dinonaktifkan.']);
        }

        if ($penggunaAdmin->role === 'admin' && $penggunaAdmin->aktif && $this->jumlahAdminAktif() <= 1) {
            return back()->withErrors(['akun' => 'Minimal harus ada satu akun admin aktif.']);
        }

        $penggunaAdmin->update([
            'aktif' => ! $penggunaAdmin->aktif,
        ]);

        return back()->with('success', 'Status pengguna berhasil diubah.');
    }

    public function destroy(Request $request, User $penggunaAdmin): RedirectResponse
    {
        abort_unless(in_array($penggunaAdmin->role, ['admin', 'kasir'], true), 404);

        if ($penggunaAdmin->id === $request->user()->id) {
            return back()->withErrors(['akun' => 'Akun yang sedang login tidak boleh dihapus.']);
        }

        if ($penggunaAdmin->role === 'admin' && $this->jumlahAdminAktif() <= 1) {
            return back()->withErrors(['akun' => 'Minimal harus ada satu akun admin aktif.']);
        }

        $penggunaAdmin->delete();

        return back()->with('success', 'Pengguna admin berhasil dihapus.');
    }

    private function jumlahAdminAktif(): int
    {
        return User::where('role', 'admin')->where('aktif', true)->count();
    }
}
