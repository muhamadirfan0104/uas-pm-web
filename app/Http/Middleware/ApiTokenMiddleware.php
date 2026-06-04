<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?: $request->header('X-API-TOKEN');

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Token API diperlukan. Kirim Authorization: Bearer {token}.',
            ], 401);
        }

        $user = User::query()->where('api_token', $token)->first();

        if (! $user || ! $user->aktif) {
            return response()->json([
                'success' => false,
                'message' => 'Token API tidak valid atau akun nonaktif.',
            ], 401);
        }

        Auth::setUser($user);
        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
