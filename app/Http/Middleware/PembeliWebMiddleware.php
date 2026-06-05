<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PembeliWebMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()
                ->route('pembeli-web.login')
                ->with('error', 'Silakan login sebagai pembeli dulu.');
        }

        if (! $user->aktif) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('pembeli-web.login')
                ->with('error', 'Akun pembeli sedang dinonaktifkan.');
        }

        if ($user->role === 'pembeli') {
            return $next($request);
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'kasir') {
            return redirect()->route('kasir.dashboard');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('pembeli-web.login')
            ->with('error', 'Akun ini tidak memiliki akses ke web pembeli.');
    }
}