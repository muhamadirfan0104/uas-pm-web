<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PembayaranApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $query = Pembayaran::with('pesanan.user')->latest();
        if ($request->filled('status')) $query->where('status', $request->status);
        return response()->json(['success' => true, 'data' => $query->paginate(15)]);
    }

    public function updateStatus(Request $request, Pembayaran $pembayaran): JsonResponse
    {
        abort_unless($request->user()->role === 'admin', 403);
        $data = $request->validate(['status' => ['required', Rule::in(['menunggu_pembayaran','menunggu_verifikasi','dibayar','ditolak','dibatalkan'])]]);
        $pembayaran->update(['status' => $data['status'], 'dibayar_pada' => $data['status'] === 'dibayar' ? now() : $pembayaran->dibayar_pada]);
        $pembayaran->pesanan?->update(['status_pembayaran' => $data['status'], 'status' => $data['status'] === 'dibayar' ? 'dibayar' : $pembayaran->pesanan->status]);
        return response()->json(['success' => true, 'message' => 'Status pembayaran diperbarui.', 'data' => $pembayaran->fresh('pesanan')]);
    }
}
