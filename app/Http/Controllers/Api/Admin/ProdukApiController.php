<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProdukApiController extends Controller
{
    private function guard(Request $request): void
    {
        abort_unless($request->user()->role === 'admin', 403);
    }

    public function index(Request $request): JsonResponse
    {
        abort_unless(in_array($request->user()->role, ['admin', 'kasir'], true), 403);
        $query = Produk::with('gambarUtama');
        if ($request->filled('q')) $query->where('nama', 'like', '%'.$request->q.'%');
        return response()->json(['success' => true, 'data' => $query->latest()->paginate(15)]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->guard($request);
        $produk = Produk::create($this->validated($request));
        return response()->json(['success' => true, 'message' => 'Produk berhasil dibuat.', 'data' => $produk], 201);
    }

    public function show(Request $request, Produk $produk): JsonResponse
    {
        abort_unless(in_array($request->user()->role, ['admin', 'kasir'], true), 403);
        return response()->json(['success' => true, 'data' => $produk->load('gambar')]);
    }

    public function update(Request $request, Produk $produk): JsonResponse
    {
        $this->guard($request);
        $produk->update($this->validated($request));
        return response()->json(['success' => true, 'message' => 'Produk berhasil diperbarui.', 'data' => $produk]);
    }

    public function destroy(Request $request, Produk $produk): JsonResponse
    {
        $this->guard($request);
        $produk->delete();
        return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus.']);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
            'satuan' => ['required', 'string', 'max:30'],
            'isi_per_satuan' => ['nullable', 'integer', 'min:0'],
            'berat' => ['nullable', 'numeric', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'masa_simpan' => ['nullable', 'integer', 'min:0'],
            'saran_penyimpanan' => ['nullable', 'string'],
            'saran_penyajian' => ['nullable', 'string'],
            'aktif' => ['nullable', 'boolean'],
        ]);
    }
}
