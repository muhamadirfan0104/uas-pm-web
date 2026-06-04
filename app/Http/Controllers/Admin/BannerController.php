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
    public function index(): View
    {
        $banner = Banner::orderBy('urutan')->latest()->paginate(12);

        return view('admin.banner.index', compact('banner'));
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

        return back()->with('success', 'Status banner berhasil diubah.');
    }

    private function validated(Request $request, bool $gambarWajib): array
    {
        return $request->validate([
            'judul' => ['required', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'aktif' => ['nullable', 'boolean'],
            'gambar' => [$gambarWajib ? 'required' : 'nullable', 'image', 'max:2048'],
        ]) + ['aktif' => false, 'urutan' => 0];
    }
}
