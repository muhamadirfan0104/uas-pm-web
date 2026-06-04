<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'Email atau password tidak sesuai.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $role = Auth::user()->role;

        if (! Auth::user()->aktif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Akun ini sedang dinonaktifkan.'])
                ->onlyInput('email');
        }

        if (! in_array($role, ['admin', 'kasir'], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withErrors(['email' => 'Akun ini bukan admin/kasir. Silakan login dari aplikasi pembeli.'])
                ->onlyInput('email');
        }

        return $this->redirectByRole($role);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }

    private function redirectByRole(string $role): RedirectResponse
    {
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'kasir' => redirect()->route('kasir.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
