<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AkunController extends Controller
{
    public function edit(Request $request): View
    {
        return view('admin.akun.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'telepon' => ['nullable', 'string', 'max:20'],
            'password_lama' => ['nullable', 'required_with:password'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        if (! empty($data['password'])) {
            if (! Hash::check($data['password_lama'] ?? '', $user->password)) {
                return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.'])->withInput();
            }

            $user->password = $data['password'];
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->telepon = $data['telepon'] ?? null;
        $user->save();

        return back()->with('success', 'Profil akun berhasil diperbarui.');
    }
}
