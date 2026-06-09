<?php

namespace App\Services\WebPembeli;

use App\Models\ItemKeranjang;
use App\Models\Keranjang;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KeranjangWebService
{
    public function data(): array
    {
        $user = Auth::user();

        if ($user && $user->role === 'pembeli') {
            $this->gabungkanSessionKeDatabase($user);

            return $this->dataDariDatabase($user);
        }

        return $this->dataDariSession();
    }

    public function totalItem(): int
    {
        return (int) $this->data()['totalItem'];
    }

    public function totalBelanja(): float
    {
        return (float) $this->data()['totalBelanja'];
    }

    public function tambah(Produk $produk, int $jumlah = 1): array
    {
        $jumlah = max(1, $jumlah);

        if (! $produk->aktif) {
            return ['success' => false, 'message' => 'Produk belum tersedia.'];
        }

        if ((int) $produk->stok <= 0) {
            return ['success' => false, 'message' => 'Stok produk sedang habis.'];
        }

        $user = Auth::user();

        if ($user && $user->role === 'pembeli') {
            $this->gabungkanSessionKeDatabase($user);
            $this->tambahKeDatabase($user, $produk, $jumlah);
        } else {
            $this->tambahKeSession($produk, $jumlah);
        }

        return ['success' => true, 'message' => $produk->nama . ' berhasil masuk keranjang.'];
    }

    public function ubah(Produk $produk, string $aksi, ?int $jumlah = null): array
    {
        $user = Auth::user();

        if ($user && $user->role === 'pembeli') {
            $this->gabungkanSessionKeDatabase($user);

            return $this->ubahDatabase($user, $produk, $aksi, $jumlah);
        }

        return $this->ubahSession($produk, $aksi, $jumlah);
    }

    public function hapus(Produk $produk): array
    {
        $user = Auth::user();

        if ($user && $user->role === 'pembeli') {
            $keranjang = $this->keranjangUser($user);

            ItemKeranjang::query()
                ->where('keranjang_id', $keranjang->id)
                ->where('produk_id', $produk->id)
                ->delete();
        } else {
            $keranjang = $this->sessionKeranjang();
            unset($keranjang[(string) $produk->id]);
            session(['keranjang_web' => $keranjang]);
        }

        return ['success' => true, 'message' => 'Produk berhasil dihapus dari keranjang.'];
    }

    public function kosongkan(): array
    {
        $user = Auth::user();

        if ($user && $user->role === 'pembeli') {
            $this->keranjangUser($user)->item()->delete();
        }

        session()->forget('keranjang_web');

        return ['success' => true, 'message' => 'Keranjang berhasil dikosongkan.'];
    }

    public function gabungkanSessionKeDatabase(User $user): void
    {
        if ($user->role !== 'pembeli') {
            return;
        }

        $sessionKeranjang = $this->sessionKeranjang();

        if (empty($sessionKeranjang)) {
            return;
        }

        DB::transaction(function () use ($user, $sessionKeranjang) {
            foreach ($sessionKeranjang as $item) {
                $produk = Produk::query()
                    ->where('aktif', true)
                    ->find($item['produk_id'] ?? null);

                if (! $produk || (int) $produk->stok <= 0) {
                    continue;
                }

                $this->tambahKeDatabase($user, $produk, (int) ($item['jumlah'] ?? 1));
            }
        });

        session()->forget('keranjang_web');
    }

    private function tambahKeDatabase(User $user, Produk $produk, int $jumlah): void
    {
        $keranjang = $this->keranjangUser($user);

        DB::transaction(function () use ($keranjang, $produk, $jumlah) {
            $produkSegar = Produk::query()
                ->where('aktif', true)
                ->findOrFail($produk->id);

            $item = ItemKeranjang::query()
                ->where('keranjang_id', $keranjang->id)
                ->where('produk_id', $produkSegar->id)
                ->first();

            $jumlahLama = $item ? (int) $item->jumlah : 0;
            $jumlahAkhir = min($jumlahLama + max(1, $jumlah), max(1, (int) $produkSegar->stok));
            $harga = (float) $produkSegar->harga;

            if ($item) {
                $item->update([
                    'jumlah' => $jumlahAkhir,
                    'harga_satuan' => $harga,
                    'subtotal' => $jumlahAkhir * $harga,
                ]);

                return;
            }

            ItemKeranjang::create([
                'keranjang_id' => $keranjang->id,
                'produk_id' => $produkSegar->id,
                'jumlah' => $jumlahAkhir,
                'harga_satuan' => $harga,
                'subtotal' => $jumlahAkhir * $harga,
            ]);
        });
    }

    private function tambahKeSession(Produk $produk, int $jumlah): void
    {
        $keranjang = $this->sessionKeranjang();
        $produkId = (string) $produk->id;
        $jumlahLama = isset($keranjang[$produkId]) ? (int) $keranjang[$produkId]['jumlah'] : 0;
        $jumlahAkhir = min($jumlahLama + max(1, $jumlah), max(1, (int) $produk->stok));

        $keranjang[$produkId] = [
            'produk_id' => $produk->id,
            'jumlah' => $jumlahAkhir,
        ];

        session(['keranjang_web' => $keranjang]);
    }

    private function ubahDatabase(User $user, Produk $produk, string $aksi, ?int $jumlah): array
    {
        $keranjang = $this->keranjangUser($user);

        $item = ItemKeranjang::query()
            ->where('keranjang_id', $keranjang->id)
            ->where('produk_id', $produk->id)
            ->first();

        if (! $item) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan di keranjang.'];
        }

        if (! $produk->aktif || (int) $produk->stok <= 0) {
            $item->delete();

            return ['success' => false, 'message' => 'Produk sudah tidak tersedia dan dihapus dari keranjang.'];
        }

        $jumlahSekarang = (int) $item->jumlah;
        $jumlahAkhir = $this->hitungJumlahBaru($jumlahSekarang, $aksi, $jumlah, (int) $produk->stok);
        $harga = (float) $produk->harga;

        $item->update([
            'jumlah' => $jumlahAkhir,
            'harga_satuan' => $harga,
            'subtotal' => $jumlahAkhir * $harga,
        ]);

        return ['success' => true, 'message' => 'Jumlah produk berhasil diperbarui.'];
    }

    private function ubahSession(Produk $produk, string $aksi, ?int $jumlah): array
    {
        $keranjang = $this->sessionKeranjang();
        $produkId = (string) $produk->id;

        if (! isset($keranjang[$produkId])) {
            return ['success' => false, 'message' => 'Produk tidak ditemukan di keranjang.'];
        }

        $jumlahSekarang = (int) $keranjang[$produkId]['jumlah'];
        $keranjang[$produkId]['jumlah'] = $this->hitungJumlahBaru($jumlahSekarang, $aksi, $jumlah, (int) $produk->stok);

        session(['keranjang_web' => $keranjang]);

        return ['success' => true, 'message' => 'Jumlah produk berhasil diperbarui.'];
    }

    private function hitungJumlahBaru(int $jumlahSekarang, string $aksi, ?int $jumlah, int $stok): int
    {
        if ($aksi === 'tambah') {
            $jumlahSekarang++;
        } elseif ($aksi === 'kurang') {
            $jumlahSekarang--;
        } elseif ($aksi === 'set') {
            $jumlahSekarang = (int) ($jumlah ?: 1);
        }

        return min(max(1, $jumlahSekarang), max(1, $stok));
    }

    private function dataDariDatabase(User $user): array
    {
        $keranjang = $this->keranjangUser($user);

        $keranjang->load(['item.produk.gambarUtama']);

        $items = $keranjang->item
            ->map(function (ItemKeranjang $item) {
                $produk = $item->produk;

                if (! $produk || ! $produk->aktif) {
                    $item->delete();
                    return null;
                }

                $jumlah = min(max(1, (int) $item->jumlah), max(1, (int) $produk->stok));
                $harga = (float) $produk->harga;
                $subtotal = $jumlah * $harga;

                if ($jumlah !== (int) $item->jumlah || (float) $item->harga_satuan !== $harga || (float) $item->subtotal !== $subtotal) {
                    $item->update([
                        'jumlah' => $jumlah,
                        'harga_satuan' => $harga,
                        'subtotal' => $subtotal,
                    ]);
                }

                return [
                    'produk' => $produk,
                    'jumlah' => $jumlah,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                    'stok_tersedia' => (int) $produk->stok,
                ];
            })
            ->filter()
            ->values();

        return $this->formatData($items);
    }

    private function dataDariSession(): array
    {
        $items = collect($this->sessionKeranjang())
            ->map(function ($item) {
                $produk = Produk::query()
                    ->with('gambarUtama')
                    ->find($item['produk_id'] ?? null);

                if (! $produk || ! $produk->aktif) {
                    return null;
                }

                $jumlah = min(max(1, (int) ($item['jumlah'] ?? 1)), max(1, (int) $produk->stok));
                $harga = (float) $produk->harga;
                $subtotal = $jumlah * $harga;

                return [
                    'produk' => $produk,
                    'jumlah' => $jumlah,
                    'harga' => $harga,
                    'subtotal' => $subtotal,
                    'stok_tersedia' => (int) $produk->stok,
                ];
            })
            ->filter()
            ->values();

        return $this->formatData($items);
    }

    private function formatData(Collection $items): array
    {
        return [
            'items' => $items,
            'totalItem' => (int) $items->sum('jumlah'),
            'totalBelanja' => (float) $items->sum('subtotal'),
        ];
    }

    private function keranjangUser(User $user): Keranjang
    {
        return Keranjang::firstOrCreate(['user_id' => $user->id]);
    }

    private function sessionKeranjang(): array
    {
        return collect(session('keranjang_web', []))
            ->filter(fn ($item) => isset($item['produk_id']))
            ->mapWithKeys(function ($item) {
                $produkId = (string) $item['produk_id'];

                return [
                    $produkId => [
                        'produk_id' => (int) $item['produk_id'],
                        'jumlah' => max(1, (int) ($item['jumlah'] ?? 1)),
                    ],
                ];
            })
            ->all();
    }
}
