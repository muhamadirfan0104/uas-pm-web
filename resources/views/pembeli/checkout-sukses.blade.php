@extends('layouts.pembeli')

@section('title', 'Pesanan Berhasil - SiTahu')

@push('styles')
<style>
    .success-hero { border-radius: 34px; background: radial-gradient(circle at 80% 18%, rgba(22,163,74,.12), transparent 20rem), #fff; border: 1px solid var(--line); box-shadow: var(--shadow-md); }
    .success-icon { width: 88px; height: 88px; border-radius: 30px; display: grid; place-items: center; background: #dcfce7; color: #16a34a; font-size: 44px; }
    .success-line { display: flex; justify-content: space-between; gap: 16px; padding: 12px 0; border-bottom: 1px solid var(--line); }
    .success-img { width: 58px; height: 58px; border-radius: 16px; overflow: hidden; display: grid; place-items: center; background: var(--brand-soft); color: var(--brand-dark); flex: 0 0 auto; }
    .success-img img { width: 100%; height: 100%; object-fit: cover; }
    .payment-modal-card {
        border: 1px solid rgba(200,147,53,.22);
        border-radius: 24px;
        background: linear-gradient(180deg, #fffdf8, #fff);
        padding: 18px;
    }
    .bank-account-modal {
        display: grid;
        grid-template-columns: 46px minmax(0,1fr) auto;
        gap: 13px;
        align-items: center;
        padding: 15px;
        border-radius: 18px;
        background: #fff;
        border: 1px solid var(--line);
    }
    .bank-account-modal .bank-icon {
        width: 46px; height: 46px; border-radius: 15px;
        display: grid; place-items: center;
        color: var(--brand-dark);
        background: var(--brand-soft);
        border: 1px solid rgba(200,147,53,.18);
    }
    .bank-account-modal .bank-label { color: var(--muted); font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .04em; }
    .bank-account-modal .bank-number { color: var(--ink); font-size: 1.22rem; font-weight: 950; letter-spacing: -.02em; line-height: 1.15; }
    .proof-box { padding: 15px; border-radius: 18px; background: #f9fafb; border: 1px dashed rgba(200,147,53,.38); }
    @media (max-width: 575.98px) { .bank-account-modal { grid-template-columns: 42px minmax(0,1fr); } .bank-account-modal .btn { grid-column: 1 / -1; } }
</style>
@endpush

@section('content')
@php
    $payment = $pesanan->pembayaran;
    $pengiriman = $pesanan->pengiriman;
    $bankNama = trim((string) ($pengaturan->bank_nama ?? '')) ?: 'Bank belum diatur';
    $bankNomor = trim((string) ($pengaturan->bank_nomor_rekening ?? '')) ?: 'Nomor rekening belum diatur';
    $bankAtasNama = trim((string) ($pengaturan->bank_atas_nama ?? '')) ?: ($pengaturan->nama ?: 'SiTahu');
    $catatanPembayaran = trim((string) ($pengaturan->info_pembayaran ?? '')) ?: 'Transfer sesuai total bayar, lalu unggah bukti transfer agar pesanan dapat diperiksa admin.';
    $showTransferModal = $payment?->metode_pembayaran === 'transfer_bank' && empty($payment?->bukti_transfer) && (session('show_transfer_modal', false) || $errors->has('bukti_transfer'));
@endphp
<div class="container py-4 py-lg-5">
    <section class="success-hero p-4 p-lg-5 text-center mb-4">
        <div class="success-icon mx-auto mb-3"><i class="bi bi-check2-circle"></i></div>
        <span class="eyebrow mb-3"><i class="bi bi-receipt"></i> Pesanan berhasil dibuat</span>
        <h1 class="section-heading display-5 mb-3">Terima kasih, pesanan Anda sudah masuk.</h1>
        <p class="section-subtitle mb-4 mx-auto" style="max-width: 720px;">Simpan nomor invoice untuk memudahkan pengecekan status pembayaran, proses toko, dan pengambilan atau pengiriman.</p>
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
            <a href="{{ route('pembeli-web.pesanan.show', $pesanan->nomor_invoice) }}" class="btn btn-brand btn-lg px-4"><i class="bi bi-eye me-2"></i> Lihat Detail Pesanan</a>
            <a href="{{ route('pembeli-web.produk') }}" class="btn btn-plain btn-lg px-4">Belanja Lagi</a>
        </div>
    </section>

    <div class="row g-4 align-items-start">
        <div class="col-lg-7">
            <div class="surface-strong p-4 p-lg-5">
                <h2 class="h4 fw-bold mb-4">Produk yang dipesan</h2>
                <div class="d-grid gap-3">
                    @foreach($pesanan->item as $item)
                        @php $image = $item->produk?->gambarUtama?->url_gambar; @endphp
                        <div class="d-flex gap-3">
                            <div class="success-img">@if($image)<img src="{{ asset('storage/' . $image) }}" alt="{{ $item->produk?->nama }}">@else<i class="bi bi-box-seam"></i>@endif</div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-bold line-clamp-1">{{ $item->produk?->nama ?: 'Produk' }}</div>
                                <div class="small text-muted fw-semibold">{{ $item->jumlah }} x {{ $rupiah($item->harga_satuan) }}</div>
                            </div>
                            <div class="fw-bold">{{ $rupiah($item->subtotal) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="surface-strong p-4 p-lg-5">
                <h2 class="h4 fw-bold mb-4">Ringkasan invoice</h2>
                <div class="success-line"><span class="text-muted fw-semibold">Nomor Invoice</span><span class="fw-bold text-end">{{ $pesanan->nomor_invoice }}</span></div>
                <div class="success-line"><span class="text-muted fw-semibold">Tanggal</span><span class="fw-bold">{{ optional($pesanan->tanggal_pesanan)->format('d M Y H:i') }}</span></div>
                <div class="success-line"><span class="text-muted fw-semibold">Status Pesanan</span><span class="badge rounded-pill text-bg-warning fw-bold">{{ ucwords(str_replace('_',' ', $pesanan->status)) }}</span></div>
                <div class="success-line"><span class="text-muted fw-semibold">Pembayaran</span><span class="fw-bold text-uppercase">{{ $payment?->metode_pembayaran ?: '-' }}</span></div>
                <div class="success-line"><span class="text-muted fw-semibold">Pengambilan</span><span class="fw-bold">{{ $pesanan->metode_pengambilan === 'kurir_toko' ? 'Kurir Toko' : 'Ambil di Toko' }}</span></div>
                <div class="success-line"><span class="text-muted fw-semibold">Subtotal</span><span class="fw-bold">{{ $rupiah($pesanan->subtotal_produk) }}</span></div>
                <div class="success-line"><span class="text-muted fw-semibold">Pengiriman</span><span class="fw-bold">{{ $rupiah($pesanan->biaya_pengiriman) }}</span></div>
                <div class="d-flex justify-content-between align-items-end mt-3"><span class="fw-bold">Total Bayar</span><span class="price-text h3 mb-0">{{ $rupiah($pesanan->total_bayar) }}</span></div>
                @if($payment?->metode_pembayaran === 'transfer_bank')
                    @if($payment?->bukti_transfer)
                        <div class="alert alert-light border rounded-4 mt-4 mb-0 small fw-semibold text-muted"><i class="bi bi-bank text-brand me-1"></i> Bukti transfer sudah terkirim dan menunggu pemeriksaan admin.</div>
                    @else
                        <div class="alert alert-warning border-0 rounded-4 mt-4 mb-0 small fw-semibold"><i class="bi bi-bank text-brand me-1"></i> Silakan transfer lalu upload bukti pembayaran agar pesanan dapat diperiksa admin.</div>
                        <button type="button" class="btn btn-brand w-100 mt-3" data-bs-toggle="modal" data-bs-target="#transferPaymentModal"><i class="bi bi-upload me-2"></i> Upload Bukti Transfer</button>
                    @endif
                @elseif($payment?->metode_pembayaran === 'cod')
                    <div class="alert alert-light border rounded-4 mt-4 mb-0 small fw-semibold text-muted"><i class="bi bi-cash-coin text-brand me-1"></i> COD dipilih. Pembayaran dilakukan saat pesanan diambil atau diterima.</div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($payment?->metode_pembayaran === 'transfer_bank' && empty($payment?->bukti_transfer))
    <div class="modal fade" id="transferPaymentModal" tabindex="-1" aria-labelledby="transferPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 rounded-5 shadow-lg overflow-hidden">
                <div class="modal-header px-4 py-3 border-0" style="background: linear-gradient(180deg, #fff8ea, #fff);">
                    <div>
                        <h5 class="modal-title fw-black" id="transferPaymentModalLabel"><i class="bi bi-bank2 text-brand me-2"></i> Rekening transfer toko</h5>
                        <div class="small text-muted fw-semibold mt-1">Pesanan sudah dibuat. Transfer sesuai total bayar, lalu upload bukti transfer di sini.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <form action="{{ route('pembeli-web.pesanan.bukti-transfer', $pesanan->nomor_invoice) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="payment-modal-card mb-3">
                            <div class="bank-account-modal mb-3">
                                <div class="bank-icon"><i class="bi bi-credit-card-2-front"></i></div>
                                <div class="min-w-0">
                                    <div class="bank-label">{{ $bankNama }}</div>
                                    <div class="bank-number js-success-bank-number">{{ $bankNomor }}</div>
                                    <div class="small text-muted fw-bold mt-1">Atas nama {{ $bankAtasNama }}</div>
                                </div>
                                <button type="button" class="btn btn-soft-brand btn-sm js-success-copy-bank" data-copy="{{ $bankNomor }}"><i class="bi bi-copy me-1"></i> Salin</button>
                            </div>
                            <div class="d-flex justify-content-between align-items-end gap-3 rounded-4 bg-white border p-3 mb-3">
                                <span class="text-muted fw-bold">Total yang harus ditransfer</span>
                                <span class="price-text h4 mb-0">{{ $rupiah($pesanan->total_bayar) }}</span>
                            </div>
                            <div class="proof-box">
                                <label for="modal_bukti_transfer" class="form-label-mini">Upload bukti transfer</label>
                                <input type="file" name="bukti_transfer" id="modal_bukti_transfer" class="form-control checkout-field @error('bukti_transfer') is-invalid @enderror" accept="image/*,.pdf" required>
                                @error('bukti_transfer')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                <div class="small text-muted fw-semibold mt-2">Format JPG, PNG, WEBP, atau PDF. Maksimal 4 MB.</div>
                            </div>
                        </div>
                        <div class="alert alert-light border rounded-4 small fw-semibold text-muted mb-0"><i class="bi bi-info-circle text-brand me-1"></i> {{ $catatanPembayaran }}</div>
                    </div>
                    <div class="modal-footer px-4 py-3 border-0 bg-light-subtle">
                        <button type="button" class="btn btn-plain" data-bs-dismiss="modal">Nanti Saja</button>
                        <button type="submit" class="btn btn-brand px-4"><i class="bi bi-upload me-2"></i> Kirim Bukti Transfer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const shouldShowTransferModal = @json($showTransferModal);
        const transferModalEl = document.getElementById('transferPaymentModal');
        if (shouldShowTransferModal && transferModalEl && window.bootstrap) {
            new bootstrap.Modal(transferModalEl).show();
        }

        document.querySelectorAll('.js-success-copy-bank').forEach(function (button) {
            button.addEventListener('click', async function () {
                const value = button.dataset.copy || '';
                if (!value || value.toLowerCase().includes('belum diatur')) return;

                try {
                    await navigator.clipboard.writeText(value);
                } catch (e) {
                    const input = document.createElement('input');
                    input.value = value;
                    document.body.appendChild(input);
                    input.select();
                    document.execCommand('copy');
                    input.remove();
                }

                const oldHtml = button.innerHTML;
                button.innerHTML = '<i class="bi bi-check2 me-1"></i> Tersalin';
                setTimeout(() => button.innerHTML = oldHtml, 1400);
            });
        });
    });
</script>
@endpush

