@extends('layouts.admin')
@section('title','Ulasan - SiTahu')
@section('content')
<div class="hero"><div><h1>Ulasan</h1><p>Moderasi ulasan produk dengan data real dari tabel ulasan.</p></div></div>
<div class="grid g4">
    <div class="card stat"><div><div class="stat-label">Rating Rata-rata</div><div class="stat-value">{{ $stats['rata_rata'] }}</div><span class="stat-note c-green">5.0</span></div><div class="stat-icon">RT</div></div>
    <div class="card stat"><div><div class="stat-label">Total Ulasan</div><div class="stat-value">{{ $stats['total'] }}</div><span class="stat-note c-blue">Komentar</span></div><div class="stat-icon">UL</div></div>
    <div class="card stat"><div><div class="stat-label">Ulasan Foto</div><div class="stat-value">{{ $stats['foto'] }}</div><span class="stat-note c-green">Kamera/galeri</span></div><div class="stat-icon">FT</div></div>
    <div class="card stat"><div><div class="stat-label">Disembunyikan</div><div class="stat-value">{{ $stats['disembunyikan'] }}</div><span class="stat-note c-yellow">Moderasi</span></div><div class="stat-icon">MD</div></div>
</div>
<div class="card" style="margin-top:16px">
    <form id="page-filter" class="toolbar" method="GET"><div class="toolbar-left"><select class="field" name="rating"><option value="">Semua rating</option>@for($i=5;$i>=1;$i--)<option value="{{ $i }}" @selected(request('rating')==$i)>{{ $i }} Bintang</option>@endfor</select><select class="field" name="status"><option value="">Semua status</option><option value="tampil" @selected(request('status')==='tampil')>Tampil</option><option value="sembunyi" @selected(request('status')==='sembunyi')>Disembunyikan</option></select></div><button class="btn btn-secondary">Filter</button></form>
    <div class="table-wrap"><table><thead><tr><th>Produk</th><th>Pembeli</th><th>Rating</th><th>Komentar</th><th>Foto</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
    @forelse($ulasan as $review)
        <tr><td>{{ $review->produk?->nama ?? '-' }}</td><td>{{ $review->user?->name ?? '-' }}</td><td>{{ str_repeat('★',$review->rating) }}</td><td>{{ $review->komentar ?? '-' }}</td><td>@if($review->foto_ulasan)<img class="cover" src="{{ asset('storage/'.$review->foto_ulasan) }}" alt="foto ulasan">@else - @endif</td><td><span class="chip {{ $review->ditampilkan ? 'c-green' : 'c-gray' }}">{{ $review->ditampilkan ? 'Tampil' : 'Disembunyikan' }}</span></td><td><div class="actions"><form class="inline-form" method="POST" action="{{ route('admin.ulasan.toggle',$review) }}" data-confirm-title="Ubah Status Ulasan" data-confirm-message="Yakin ingin mengubah status tampilan ulasan ini?" data-confirm-button="Ubah Status">@csrf @method('PATCH')<button class="small-btn">{{ $review->ditampilkan ? 'Sembunyikan' : 'Tampilkan' }}</button></form><form class="inline-form" method="POST" action="{{ route('admin.ulasan.destroy',$review) }}" data-confirm-title="Konfirmasi Tindakan" data-confirm-message="Hapus ulasan ini?" data-confirm-button="Lanjutkan">@csrf @method('DELETE')<button class="small-btn">Hapus</button></form></div></td></tr>
    @empty<tr><td colspan="7">Belum ada ulasan.</td></tr>@endforelse
    </tbody></table></div><div class="card-pad pagination">{{ $ulasan->links() }}</div>
</div>
@endsection
