@extends('layouts.admin')
@section('title','Pesanan - SiTahu')
@section('content')
<div class="hero"><div><h1>Pesanan</h1><p>Data pesanan real dari tabel pesanan, item_pesanan, pembayaran, dan pengiriman.</p></div></div>
<div class="card">
    <form id="page-filter" class="toolbar" method="GET"><div class="toolbar-left"><input class="field" name="q" value="{{ request('q') }}" placeholder="Invoice / pembeli"><select class="field" name="status"><option value="">Semua status</option>@foreach(['menunggu_pembayaran','dibayar','diproses','siap_diambil','dalam_pengantaran','selesai','dibatalkan'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ $statusLabel($s) }}</option>@endforeach</select><select class="field" name="status_pembayaran"><option value="">Semua pembayaran</option>@foreach(['menunggu_pembayaran','dibayar','gagal','kedaluwarsa','dibatalkan'] as $s)<option value="{{ $s }}" @selected(request('status_pembayaran')===$s)>{{ $statusLabel($s) }}</option>@endforeach</select></div><button class="btn btn-secondary">Filter</button></form>
    <div class="table-wrap"><table><thead><tr><th>Invoice</th><th>Pembeli</th><th>Item</th><th>Penerimaan</th><th>Pembayaran</th><th>Status</th><th>Total</th><th>Aksi</th></tr></thead><tbody>
    @forelse($pesanan as $order)
        <tr><td><strong>{{ $order->nomor_invoice }}</strong><span class="sub">{{ optional($order->tanggal_pesanan)->format('d M Y H:i') }}</span></td><td>{{ $order->user?->name ?? '-' }}</td><td>{{ $order->item->sum('jumlah') }} item</td><td>{{ $statusLabel($order->metode_pengambilan) }}</td><td><span class="chip {{ $statusClass($order->status_pembayaran) }}">{{ $statusLabel($order->status_pembayaran) }}</span></td><td><span class="chip {{ $statusClass($order->status) }}">{{ $statusLabel($order->status) }}</span></td><td>{{ $rupiah($order->total_bayar) }}</td><td><a class="small-btn" href="{{ route('admin.pesanan.show',$order) }}">Detail</a></td></tr>
    @empty<tr><td colspan="8">Belum ada pesanan.</td></tr>@endforelse
    </tbody></table></div><div class="card-pad pagination">{{ $pesanan->links() }}</div>
</div>
@endsection
