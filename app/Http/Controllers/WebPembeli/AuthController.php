<?php

namespace App\Http\Controllers\WebPembeli;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
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

        $data = $request->validate([
            'login' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Email atau nomor HP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $login = trim($data['login']);

        $field = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'telepon';

        $credentials = [
            $field => $login,
            'password' => $data['password'],
        ];

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['login' => 'Email/nomor HP atau password tidak sesuai.'])
                ->onlyInput('login');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user->aktif) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['login' => 'Akun pembeli sedang dinonaktifkan.'])
                ->onlyInput('login');
        }

        if ($user->role !== 'pembeli') {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['login' => 'Akun ini bukan akun pembeli. Gunakan akun pembeli untuk masuk ke web pembeli.'])
                ->onlyInput('login');
        }

        return redirect()
            ->route('pembeli-web.home')
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

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'telepon' => ['required', 'string', 'max:30', Rule::unique('users', 'telepon')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama pembeli wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email belum sesuai.',
            'email.unique' => 'Email sudah digunakan.',
            'telepon.required' => 'Nomor HP wajib diisi.',
            'telepon.unique' => 'Nomor HP sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'telepon' => $data['telepon'],
            'password' => Hash::make($data['password']),
            'role' => 'pembeli',
            'aktif' => true,
        ]);

        return redirect()
            ->route('pembeli-web.login')
            ->with('success', 'Registrasi berhasil. Silakan login untuk mulai belanja.');
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
}