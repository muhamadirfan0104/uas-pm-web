<?php

namespace App\Services\WebPembeli;

use App\Models\ItemKeranjang;
use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KeranjangService
{
    private string $sessionKey = 'keranjang_web';

    public function data(): array
    {
        if ($this->isPembeliLogin()) {
            return $this->dataDatabase();
        }

        return $this->dataSession();
    }


    public function ringkasan(int $limit = 5): array
    {
        $data = $this->data();

        return [
            'items' => collect($data['items'] ?? [])->take($limit)->values(),
            'totalItem' => (int) ($data['totalItem'] ?? 0),
            'totalBelanja' => (float) ($data['totalBelanja'] ?? 0),
        ];
    }

    public function totalItem(): int
    {
        return (int) $this->data()['totalItem'];
    }

    public function tambah(Produk $produk, int $jumlah = 1): void
    {
        $jumlah = max(1, $jumlah);

        if ($this->isPembeliLogin()) {
            $this->tambahDatabase($produk, $jumlah);
            return;
        }

        $this->tambahSession($produk, $jumlah);
    }

    public function updateJumlah(Produk $produk, string $aksi, ?int $jumlah = null): bool
    {
        if ($this->isPembeliLogin()) {
            return $this->updateDatabase($produk, $aksi, $jumlah);
        }

        return $this->updateSession($produk, $aksi, $jumlah);
    }

    public function hapus(Produk $produk): void
    {
        if ($this->isPembeliLogin()) {
            $keranjang = $this->keranjangUser(false);

            if ($keranjang) {
                $keranjang->item()->where('produk_id', $produk->id)->delete();
            }

            return;
        }

        $keranjang = session($this->sessionKey, []);
        unset($keranjang[(string) $produk->id]);
        session([$this->sessionKey => $keranjang]);
    }

    public function hapusProdukIds(array $produkIds): void
    {
        $produkIds = collect($produkIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        if ($produkIds->isEmpty()) {
            return;
        }

        if ($this->isPembeliLogin()) {
            $keranjang = $this->keranjangUser(false);

            if ($keranjang) {
                $keranjang->item()->whereIn('produk_id', $produkIds->all())->delete();
            }

            return;
        }

        $keranjang = session($this->sessionKey, []);

        foreach ($produkIds as $produkId) {
            unset($keranjang[(string) $produkId]);
        }

        session([$this->sessionKey => $keranjang]);
    }

    public function kosongkan(): void
    {
        if ($this->isPembeliLogin()) {
            $keranjang = $this->keranjangUser(false);

            if ($keranjang) {
                $keranjang->item()->delete();
            }
        }

        session()->forget($this->sessionKey);
    }

    public function sinkronkanSessionKeDatabase(): void
    {
        if (! $this->isPembeliLogin()) {
            return;
        }

        $keranjangSession = session($this->sessionKey, []);

        if (empty($keranjangSession)) {
            return;
        }

        DB::transaction(function () use ($keranjangSession) {
            foreach ($keranjangSession as $item) {
                $produk = Produk::query()
                    ->where('aktif', true)
                    ->find($item['produk_id'] ?? null);

                if (! $produk || (int) $produk->stok <= 0) {
                    continue;
                }

                $this->tambahDatabase($produk, (int) ($item['jumlah'] ?? 1));
            }
        });

        session()->forget($this->sessionKey);
    }

    private function isPembeliLogin(): bool
    {
        return Auth::check() && Auth::user()?->role === 'pembeli';
    }

    private function keranjangUser(bool $buatJikaBelumAda = true): ?Keranjang
    {
        if (! $this->isPembeliLogin()) {
            return null;
        }

        $query = Keranjang::query()->where('user_id', Auth::id());

        if ($buatJikaBelumAda) {
            return $query->firstOrCreate(['user_id' => Auth::id()]);
        }

        return $query->first();
    }

    private function tambahDatabase(Produk $produk, int $jumlah): void
    {
        DB::transaction(function () use ($produk, $jumlah) {
            $keranjang = $this->keranjangUser();

            $item = ItemKeranjang::query()
                ->where('keranjang_id', $keranjang->id)
                ->where('produk_id', $produk->id)
                ->lockForUpdate()
                ->first();

            $jumlahLama = $item ? (int) $item->jumlah : 0;
            $jumlahAkhir = min($jumlahLama + $jumlah, max(1, (int) $produk->stok));
            $harga = (float) $produk->harga;

            ItemKeranjang::query()->updateOrCreate(
                [
                    'keranjang_id' => $keranjang->id,
                    'produk_id' => $produk->id,
                ],
                [
                    'jumlah' => $jumlahAkhir,
                    'harga_satuan' => $harga,
                    'subtotal' => $harga * $jumlahAkhir,
                ]
            );
        });
    }

    private function updateDatabase(Produk $produk, string $aksi, ?int $jumlah = null): bool
    {
        $keranjang = $this->keranjangUser(false);

        if (! $keranjang) {
            return false;
        }

        $item = $keranjang->item()->where('produk_id', $produk->id)->first();

        if (! $item) {
            return false;
        }

        $jumlahSekarang = (int) $item->jumlah;

        if ($aksi === 'tambah') {
            $jumlahSekarang++;
        }

        if ($aksi === 'kurang') {
            $jumlahSekarang--;
        }

        if ($aksi === 'set') {
            $jumlahSekarang = (int) ($jumlah ?: 1);
        }

        $jumlahSekarang = max(1, $jumlahSekarang);
        $jumlahSekarang = min($jumlahSekarang, max(1, (int) $produk->stok));
        $harga = (float) $produk->harga;

        $item->update([
            'jumlah' => $jumlahSekarang,
            'harga_satuan' => $harga,
            'subtotal' => $harga * $jumlahSekarang,
        ]);

        return true;
    }

    private function dataDatabase(): array
    {
        $keranjang = $this->keranjangUser(false);

        if (! $keranjang) {
            return $this->formatData(collect());
        }

        $items = $keranjang->item()
            ->with('produk.gambarUtama')
            ->latest()
            ->get()
            ->map(function (ItemKeranjang $item) {
                $produk = $item->produk;

                if (! $produk || ! $produk->aktif) {
                    return null;
                }

                $jumlah = max(1, (int) $item->jumlah);
                $jumlah = min($jumlah, max(1, (int) $produk->stok));
                $harga = (float) $produk->harga;
                $subtotal = $jumlah * $harga;

                if ((int) $item->jumlah !== $jumlah || (float) $item->harga_satuan !== $harga || (float) $item->subtotal !== $subtotal) {
                    $item->update([
                        'jumlah' => $jumlah,
                        'harga_satuan' => $harga,
                        'subtotal' => $subtotal,
                    ]);
                }

                return $this->formatItem($produk, $jumlah, $harga, $subtotal);
            })
            ->filter()
            ->values();

        return $this->formatData($items);
    }

    private function tambahSession(Produk $produk, int $jumlah): void
    {
        $keranjang = session($this->sessionKey, []);
        $produkId = (string) $produk->id;
        $jumlahLama = isset($keranjang[$produkId]) ? (int) $keranjang[$produkId]['jumlah'] : 0;
        $jumlahAkhir = min($jumlahLama + $jumlah, max(1, (int) $produk->stok));

        $keranjang[$produkId] = [
            'produk_id' => $produk->id,
            'jumlah' => $jumlahAkhir,
        ];

        session([$this->sessionKey => $keranjang]);
    }

    private function updateSession(Produk $produk, string $aksi, ?int $jumlah = null): bool
    {
        $keranjang = session($this->sessionKey, []);
        $produkId = (string) $produk->id;

        if (! isset($keranjang[$produkId])) {
            return false;
        }

        $jumlahSekarang = (int) $keranjang[$produkId]['jumlah'];

        if ($aksi === 'tambah') {
            $jumlahSekarang++;
        }

        if ($aksi === 'kurang') {
            $jumlahSekarang--;
        }

        if ($aksi === 'set') {
            $jumlahSekarang = (int) ($jumlah ?: 1);
        }

        $jumlahSekarang = max(1, $jumlahSekarang);
        $jumlahSekarang = min($jumlahSekarang, max(1, (int) $produk->stok));
        $keranjang[$produkId]['jumlah'] = $jumlahSekarang;

        session([$this->sessionKey => $keranjang]);

        return true;
    }

    private function dataSession(): array
    {
        $keranjang = session($this->sessionKey, []);

        $items = collect($keranjang)
            ->map(function ($item) {
                $produk = Produk::query()
                    ->with('gambarUtama')
                    ->find($item['produk_id'] ?? null);

                if (! $produk || ! $produk->aktif) {
                    return null;
                }

                $jumlah = max(1, (int) ($item['jumlah'] ?? 1));
                $jumlah = min($jumlah, max(1, (int) $produk->stok));
                $harga = (float) $produk->harga;
                $subtotal = $jumlah * $harga;

                return $this->formatItem($produk, $jumlah, $harga, $subtotal);
            })
            ->filter()
            ->values();

        return $this->formatData($items);
    }

    private function formatItem(Produk $produk, int $jumlah, float $harga, float $subtotal): array
    {
        return [
            'produk' => $produk,
            'jumlah' => $jumlah,
            'harga' => $harga,
            'subtotal' => $subtotal,
            'stok_tersedia' => (int) $produk->stok,
        ];
    }

    private function formatData(Collection $items): array
    {
        return [
            'items' => $items,
            'totalItem' => (int) $items->sum('jumlah'),
            'totalBelanja' => (float) $items->sum('subtotal'),
        ];
    }
}
