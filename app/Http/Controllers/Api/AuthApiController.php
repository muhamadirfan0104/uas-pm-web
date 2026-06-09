<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthApiController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'telepon' => $data['telepon'] ?? null,
            'password' => $data['password'],
            'role' => 'pembeli',
            'aktif' => true,
            'api_token' => Str::random(80),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil.',
            'token' => $user->api_token,
            'user' => $this->userPayload($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'role' => ['nullable', Rule::in(['admin', 'pembeli'])],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['success' => false, 'message' => 'Email atau password tidak sesuai.'], 422);
        }

        if (! $user->aktif) {
            return response()->json(['success' => false, 'message' => 'Akun sedang dinonaktifkan.'], 403);
        }

        if (($data['role'] ?? null) && $user->role !== $data['role']) {
            return response()->json(['success' => false, 'message' => 'Role akun tidak sesuai.'], 403);
        }

        $user->forceFill(['api_token' => Str::random(80)])->save();

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token' => $user->api_token,
            'user' => $this->userPayload($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'user' => $this->userPayload($request->user())]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($data);

        return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui.', 'user' => $this->userPayload($user)]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'password_lama' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (! Hash::check($data['password_lama'], $request->user()->password)) {
            return response()->json(['success' => false, 'message' => 'Password lama tidak sesuai.'], 422);
        }

        $request->user()->update(['password' => $data['password']]);

        return response()->json(['success' => true, 'message' => 'Password berhasil diganti.']);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->forceFill(['api_token' => null])->save();

        return response()->json(['success' => true, 'message' => 'Logout berhasil.']);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'telepon' => $user->telepon,
            'role' => $user->role,
            'aktif' => (bool) $user->aktif,
        ];
    }
}
