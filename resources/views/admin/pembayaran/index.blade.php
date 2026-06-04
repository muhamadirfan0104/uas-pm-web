@extends('layouts.admin')
@section('title','Pembayaran - SiTahu')
@section('content')
<div class="hero"><div><h1>Pembayaran</h1><p>Monitoring dan update pembayaran real dari tabel pembayaran.</p></div></div>
<div class="grid g4">
    <div class="card stat"><div><div class="stat-label">Dibayar</div><div class="stat-value">{{ $stats['dibayar'] }}</div><span class="stat-note c-green">Berhasil</span></div><div class="stat-icon">OK</div></div>
    <div class="card stat"><div><div class="stat-label">Menunggu</div><div class="stat-value">{{ $stats['menunggu'] }}</div><span class="stat-note c-yellow">Belum bayar</span></div><div class="stat-icon">⏳</div></div>
    <div class="card stat"><div><div class="stat-label">Gagal</div><div class="stat-value">{{ $stats['gagal'] }}</div><span class="stat-note c-red">Gagal</span></div><div class="stat-icon">❌</div></div>
    <div class="card stat"><div><div class="stat-label">Kedaluwarsa</div><div class="stat-value">{{ $stats['kedaluwarsa'] }}</div><span class="stat-note c-red">Expired</span></div><div class="stat-icon">⌛</div></div>
</div>
<div class="card" style="margin-top:16px">
    <form id="page-filter" class="toolbar" method="GET"><div class="toolbar-left"><input class="field" name="q" value="{{ request('q') }}" placeholder="Invoice / reference"><select class="field" name="status"><option value="">Semua status</option>@foreach(['menunggu_pembayaran','dibayar','gagal','kedaluwarsa','dibatalkan'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ $statusLabel($s) }}</option>@endforeach</select></div><button class="btn btn-secondary">Filter</button></form>
    <div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Reference</th><th>Metode</th><th>Status</th><th>Tanggal</th><th>Total</th><th>Aksi</th></tr></thead><tbody>
    @forelse($pembayaran as $pay)
        <tr><td>{{ $pay->pesanan?->nomor_invoice ?? '-' }}<span class="sub">{{ $pay->pesanan?->user?->name }}</span></td><td>{{ $pay->referensi_pembayaran ?? '-' }}</td><td>{{ strtoupper($pay->metode_pembayaran) }}</td><td><span class="chip {{ $statusClass($pay->status) }}">{{ $statusLabel($pay->status) }}</span></td><td>{{ $pay->created_at->format('d M Y H:i') }}</td><td>{{ $rupiah($pay->jumlah) }}</td><td><form method="POST" action="{{ route('admin.pembayaran.status',$pay) }}" class="actions" data-confirm-title="Ubah Status Pembayaran" data-confirm-message="Yakin ingin menyimpan perubahan status pembayaran ini?" data-confirm-button="Simpan Status">@csrf @method('PATCH')<select class="field" name="status">@foreach(['menunggu_pembayaran','dibayar','gagal','kedaluwarsa','dibatalkan'] as $s)<option value="{{ $s }}" @selected($pay->status===$s)>{{ $statusLabel($s) }}</option>@endforeach</select><button class="small-btn">Update</button></form></td></tr>
    @empty<tr><td colspan="7">Belum ada pembayaran.</td></tr>@endforelse
    </tbody></table></div><div class="card-pad pagination">{{ $pembayaran->links() }}</div>
</div>
@endsection
