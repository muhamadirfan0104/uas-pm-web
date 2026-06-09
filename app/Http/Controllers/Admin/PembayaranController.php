<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PembayaranController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pembayaran::query()
            ->with(['pesanan.user', 'pesanan.item.produk'])
            ->latest();

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('referensi_pembayaran', 'like', '%' . $keyword . '%')
                    ->orWhereHas('pesanan', function ($order) use ($keyword) {
                        $order->where('nomor_invoice', 'like', '%' . $keyword . '%')
                            ->orWhereHas('user', function ($user) use ($keyword) {
                                $user->where('name', 'like', '%' . $keyword . '%')
                                    ->orWhere('email', 'like', '%' . $keyword . '%')
                                    ->orWhere('telepon', 'like', '%' . $keyword . '%');
                            });
                    });
            });
        }

        $tab = (string) $request->input('tab', 'semua');
        if ($tab !== 'semua') {
            match ($tab) {
                'perlu_dicek' => $query->where('metode_pembayaran', 'transfer_bank')
                    ->where('status', 'menunggu_pembayaran')
                    ->whereNotNull('bukti_transfer')
                    ->where('bukti_transfer', '!=', ''),
                'belum_upload' => $query->where('metode_pembayaran', 'transfer_bank')
                    ->where('status', 'menunggu_pembayaran')
                    ->where(function ($q) {
                        $q->whereNull('bukti_transfer')->orWhere('bukti_transfer', '');
                    }),
                'dibayar' => $query->where('status', 'dibayar'),
                'ditolak' => $query->where('status', 'ditolak'),
                'cod' => $query->where('metode_pembayaran', 'cod'),
                default => null,
            };
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('metode')) {
            $query->where('metode_pembayaran', $request->metode);
        }

        if ($request->filled('bukti')) {
            if ($request->bukti === 'ada') {
                $query->whereNotNull('bukti_transfer')->where('bukti_transfer', '!=', '');
            } elseif ($request->bukti === 'belum') {
                $query->where(function ($q) {
                    $q->whereNull('bukti_transfer')->orWhere('bukti_transfer', '');
                });
            }
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        $pembayaran = $query->paginate(10)->withQueryString();

        $base = Pembayaran::query();
        $stats = [
            'perlu_dicek' => (clone $base)->where('metode_pembayaran', 'transfer_bank')
                ->where('status', 'menunggu_pembayaran')
                ->whereNotNull('bukti_transfer')
                ->where('bukti_transfer', '!=', '')
                ->count(),
            'belum_upload' => (clone $base)->where('metode_pembayaran', 'transfer_bank')
                ->where('status', 'menunggu_pembayaran')
                ->where(function ($q) {
                    $q->whereNull('bukti_transfer')->orWhere('bukti_transfer', '');
                })
                ->count(),
            'dibayar' => (clone $base)->where('status', 'dibayar')->count(),
            'ditolak' => (clone $base)->where('status', 'ditolak')->count(),
            'cod' => (clone $base)->where('metode_pembayaran', 'cod')->count(),
            'transfer' => (clone $base)->where('metode_pembayaran', 'transfer_bank')->count(),
            'nominal_menunggu' => (clone $base)->where('status', 'menunggu_pembayaran')->sum('jumlah'),
        ];

        return view('admin.pembayaran.index', compact('pembayaran', 'stats'));
    }

    public function terima(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $data = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($pembayaran, $data) {
            $pembayaran->loadMissing('pesanan');

            $pembayaran->update([
                'status' => 'dibayar',
                'catatan_admin' => $data['catatan_admin'] ?? null,
                'dibayar_pada' => Carbon::now(),
                'diverifikasi_pada' => Carbon::now(),
            ]);

            $pembayaran->pesanan?->update([
                'status_pembayaran' => 'dibayar',
                'status' => 'diproses',
            ]);
        });

        return back()->with('success', 'Pembayaran diterima. Pesanan masuk ke tahap diproses.');
    }

    public function tolak(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $data = $request->validate([
            'catatan_admin' => ['required', 'string', 'max:500'],
        ], [
            'catatan_admin.required' => 'Catatan penolakan wajib diisi agar pembeli tahu alasan upload ulang.',
        ]);

        DB::transaction(function () use ($pembayaran, $data) {
            $pembayaran->loadMissing('pesanan');

            $pembayaran->update([
                'status' => 'ditolak',
                'catatan_admin' => $data['catatan_admin'],
                'dibayar_pada' => null,
                'diverifikasi_pada' => Carbon::now(),
            ]);

            $pembayaran->pesanan?->update([
                'status_pembayaran' => 'ditolak',
                'status' => 'menunggu_pembayaran',
            ]);
        });

        return back()->with('success', 'Bukti transfer ditolak. Pembeli bisa upload ulang dari detail pesanan.');
    }

    public function updateStatus(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:menunggu_pembayaran,dibayar,ditolak,gagal,kedaluwarsa,dibatalkan'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($pembayaran, $data) {
            $pembayaran->loadMissing('pesanan');

            $pembayaran->update([
                'status' => $data['status'],
                'catatan_admin' => $data['catatan_admin'] ?? $pembayaran->catatan_admin,
                'dibayar_pada' => $data['status'] === 'dibayar' ? Carbon::now() : null,
                'diverifikasi_pada' => in_array($data['status'], ['dibayar', 'ditolak'], true) ? Carbon::now() : $pembayaran->diverifikasi_pada,
            ]);

            if ($pembayaran->pesanan) {
                $pembayaran->pesanan->update([
                    'status_pembayaran' => $data['status'],
                    'status' => match ($data['status']) {
                        'dibayar' => 'diproses',
                        'ditolak' => 'menunggu_pembayaran',
                        'dibatalkan' => 'dibatalkan',
                        default => $pembayaran->pesanan->status,
                    },
                ]);
            }
        });

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}
