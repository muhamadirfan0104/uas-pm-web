<?php

namespace App\Http\Controllers\Api\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AlamatApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $request->user()->alamat()->latest()->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $data['user_id'] = $request->user()->id;
        $alamat = Alamat::create($data);

        if ($alamat->utama) {
            Alamat::where('user_id', $request->user()->id)->where('id', '!=', $alamat->id)->update(['utama' => false]);
        }

        return response()->json(['success' => true, 'message' => 'Alamat berhasil disimpan.', 'data' => $alamat], 201);
    }

    public function update(Request $request, Alamat $alamat): JsonResponse
    {
        abort_unless($alamat->user_id === $request->user()->id, 403);
        $alamat->update($this->validated($request));

        if ($alamat->utama) {
            Alamat::where('user_id', $request->user()->id)->where('id', '!=', $alamat->id)->update(['utama' => false]);
        }

        return response()->json(['success' => true, 'message' => 'Alamat berhasil diperbarui.', 'data' => $alamat]);
    }

    public function destroy(Request $request, Alamat $alamat): JsonResponse
    {
        abort_unless($alamat->user_id === $request->user()->id, 403);
        $alamat->delete();

        return response()->json(['success' => true, 'message' => 'Alamat berhasil dihapus.']);
    }

    public function setUtama(Request $request, Alamat $alamat): JsonResponse
    {
        abort_unless($alamat->user_id === $request->user()->id, 403);
        Alamat::where('user_id', $request->user()->id)->update(['utama' => false]);
        $alamat->update(['utama' => true]);

        return response()->json(['success' => true, 'message' => 'Alamat utama berhasil dipilih.', 'data' => $alamat]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama_penerima' => ['required', 'string', 'max:100'],
            'telepon' => ['required', 'string', 'max:20'],
            'alamat_lengkap' => ['required', 'string'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'utama' => ['nullable', 'boolean'],
        ]);
    }
}
