<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Banner::query();

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                    ->orWhere('deskripsi', 'like', "%{$keyword}%");
            });
        }

        if ($request->status === 'aktif') {
            $query->where('aktif', true);
        } elseif ($request->status === 'nonaktif') {
            $query->where('aktif', false);
        }

        match ($request->sort) {
            'terbaru' => $query->latest(),
            'terlama' => $query->oldest(),
            default => $query->orderBy('urutan')->latest(),
        };

        $banner = $query->paginate(9)->withQueryString();

        $stats = [
            'total' => Banner::count(),
            'aktif' => Banner::where('aktif', true)->count(),
            'nonaktif' => Banner::where('aktif', false)->count(),
            'urutan_berikutnya' => ((int) Banner::max('urutan')) + 1,
        ];

        return view('admin.banner.index', compact('banner', 'stats'));
    }

    public function create(): View
    {
        return view('admin.banner.create', ['banner' => new Banner()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, true);
        $data['url_gambar'] = $request->file('gambar')->store('banner', 'public');

        Banner::create($data);

        return redirect()->route('admin.banner.index')->with('success', 'Banner berhasil ditambahkan.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banner.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $data = $this->validated($request, false);

        if ($request->hasFile('gambar')) {
            Storage::disk('public')->delete($banner->url_gambar);
            $data['url_gambar'] = $request->file('gambar')->store('banner', 'public');
        }

        $banner->update($data);

        return redirect()->route('admin.banner.index')->with('success', 'Banner berhasil diperbarui.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        Storage::disk('public')->delete($banner->url_gambar);
        $banner->delete();

        return back()->with('success', 'Banner berhasil dihapus.');
    }

    public function toggle(Banner $banner): RedirectResponse
    {
        $banner->update(['aktif' => ! $banner->aktif]);

        return back()->with('success', $banner->aktif ? 'Banner ditampilkan di beranda pembeli.' : 'Banner disembunyikan dari beranda pembeli.');
    }

    private function validated(Request $request, bool $gambarWajib): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'aktif' => ['nullable', 'boolean'],
            'gambar' => [$gambarWajib ? 'required' : 'nullable', 'image', 'max:2048'],
        ]) + ['aktif' => false, 'urutan' => 0];
    }
}
