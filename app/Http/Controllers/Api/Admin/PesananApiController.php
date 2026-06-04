<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PesananApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort_unless(in_array($request->user()->role, ['admin', 'kasir'], true), 403);
        $query = Pesanan::with(['user', 'pembayaran', 'pengiriman'])->latest('tanggal_pesanan');
        if ($request->filled('status')) $query->where('status', $request->status);
        return response()->json(['success' => true, 'data' => $query->paginate(15)]);
    }

    public function show(Request $request, Pesanan $pesanan): JsonResponse
    {
        abort_unless(in_array($request->user()->role, ['admin', 'kasir'], true), 403);
        return response()->json(['success' => true, 'data' => $pesanan->load(['user', 'item.produk', 'pembayaran', 'pengiriman', 'alamatPengiriman'])]);
    }

    public function updateStatus(Request $request, Pesanan $pesanan): JsonResponse
    {
        abort_unless(in_array($request->user()->role, ['admin', 'kasir'], true), 403);
        $data = $request->validate(['status' => ['required', Rule::in(['menunggu_pembayaran','dibayar','diproses','siap_diambil','dalam_pengantaran','selesai','dibatalkan'])]]);
        $pesanan->update(['status' => $data['status']]);
        return response()->json(['success' => true, 'message' => 'Status pesanan diperbarui.', 'data' => $pesanan]);
    }
}
