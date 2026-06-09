<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WebPembeli\KeranjangService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private KeranjangService $keranjangService)
    {
    }

    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->role === 'pembeli') {
            return redirect()->route('pembeli-web.home');
        }

        return view('pembeli.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        if (Auth::check() && Auth::user()->role !== 'pembeli') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if (Auth::check() && Auth::user()->role === 'pembeli') {
            return redirect()->route('pembeli-web.home');
        }

        $validator = Validator::make($request->all(), [
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Email atau nomor HP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->except('password'))
                ->with('auth_modal', 'login');
        }

        $data = $validator->validated();

        $login = trim((string) $data['login']);
        $remember = $request->boolean('remember');

        $user = User::query()
            ->where('email', $login)
            ->orWhereIn('telepon', $this->variasiTelepon($login))
            ->first();

        if (! $user || ! Hash::check((string) $data['password'], (string) $user->password)) {
            return back()
                ->withErrors(['login' => 'Email/nomor HP atau password tidak sesuai dengan data pembeli.'])
                ->onlyInput('login')
                ->with('auth_modal', 'login');
        }

        if ($user->role !== 'pembeli') {
            return back()
                ->withErrors(['login' => 'Akun ini bukan akun pembeli. Gunakan halaman login admin untuk akun admin/kasir.'])
                ->onlyInput('login')
                ->with('auth_modal', 'login');
        }

        if (Schema::hasColumn('users', 'aktif') && ! (bool) $user->aktif) {
            return back()
                ->withErrors(['login' => 'Akun pembeli sedang dinonaktifkan.'])
                ->onlyInput('login')
                ->with('auth_modal', 'login');
        }

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $this->keranjangService->sinkronkanSessionKeDatabase();

        return redirect()
            ->intended(route('pembeli-web.home'))
            ->with('success', 'Login berhasil. Selamat datang di SiTahu.');
    }

    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->role === 'pembeli') {
            return redirect()->route('pembeli-web.home');
        }

        return view('pembeli.auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        if (Auth::check() && Auth::user()->role !== 'pembeli') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        if (Auth::check() && Auth::user()->role === 'pembeli') {
            return redirect()->route('pembeli-web.home');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'telepon' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama pembeli wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email belum sesuai.',
            'email.unique' => 'Email sudah digunakan.',
            'telepon.required' => 'Nomor HP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('auth_modal', 'register');
        }

        $data = $validator->validated();

        $telepon = $this->normalisasiTeleponUntukSimpan((string) $data['telepon']);

        if (User::query()->whereIn('telepon', $this->variasiTelepon($telepon))->exists()) {
            return back()
                ->withErrors(['telepon' => 'Nomor HP sudah digunakan.'])
                ->withInput()
                ->with('auth_modal', 'register');
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'telepon' => $telepon,
            'password' => Hash::make($data['password']),
            'role' => 'pembeli',
            'aktif' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $this->keranjangService->sinkronkanSessionKeDatabase();

        return redirect()
            ->intended(route('pembeli-web.home'))
            ->with('success', 'Registrasi berhasil. Akun pembeli sudah masuk dan siap digunakan.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('pembeli-web.home')
            ->with('success', 'Berhasil logout dari akun pembeli.');
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
