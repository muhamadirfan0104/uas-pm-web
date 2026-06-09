@extends('layouts.pembeli')

@section('title', 'Profil Saya - SiTahu')

@push('styles')
<style>
    .profile-hero {
        border: 1px solid rgba(200,147,53,.20);
        border-radius: 30px;
        background:
            radial-gradient(circle at 100% 0%, rgba(200,147,53,.18), transparent 26rem),
            linear-gradient(135deg, #fff, #fffaf0);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }
    .profile-avatar {
        width: 84px;
        height: 84px;
        border-radius: 28px;
        display: grid;
        place-items: center;
        color: #fff;
        font-size: 30px;
        font-weight: 900;
        letter-spacing: -.04em;
        background: linear-gradient(135deg, var(--brand-color), var(--brand-hover));
        box-shadow: var(--shadow-brand);
        flex: 0 0 auto;
    }
    .profile-panel {
        border: 1px solid var(--line);
        border-radius: 26px;
        background: #fff;
        box-shadow: var(--shadow-xs);
        overflow: hidden;
    }
    .profile-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 20px 22px;
        border-bottom: 1px solid var(--line);
        background: linear-gradient(180deg, #fff, #fffdf8);
    }
    .profile-panel-body { padding: 22px; }
    .profile-icon {
        width: 42px;
        height: 42px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        background: var(--brand-soft);
        border: 1px solid rgba(200,147,53,.20);
        color: var(--brand-dark);
        flex: 0 0 auto;
    }
    .profile-stat {
        border: 1px solid var(--line);
        border-radius: 22px;
        background: #fff;
        padding: 18px;
        height: 100%;
        box-shadow: var(--shadow-xs);
    }
    .profile-stat .value {
        font-weight: 900;
        letter-spacing: -.035em;
        color: var(--ink);
        line-height: 1;
    }
    .profile-input {
        height: 48px;
        border-radius: 16px;
        border: 1px solid var(--line);
        font-weight: 700;
        box-shadow: none !important;
    }
    .profile-input:focus {
        border-color: rgba(200,147,53,.55);
        box-shadow: 0 0 0 .25rem rgba(200,147,53,.12) !important;
    }
    .profile-label {
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 900;
        color: var(--muted);
        letter-spacing: .04em;
        margin-bottom: 8px;
    }
    .profile-card-line {
        border: 1px solid var(--line);
        border-radius: 20px;
        padding: 16px;
        background: #fff;
    }
    .profile-card-line.primary {
        border-color: rgba(200,147,53,.28);
        background: linear-gradient(135deg, #fff, #fff8ea);
    }
    .profile-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 11px;
        border-radius: 999px;
        background: var(--brand-soft);
        color: var(--brand-dark);
        border: 1px solid rgba(200,147,53,.22);
        font-size: 12px;
        font-weight: 850;
    }
    .order-timeline-card {
        border: 1px solid var(--line);
        border-radius: 20px;
        padding: 16px;
        background: #fff;
        transition: .2s ease;
    }
    .order-timeline-card:hover {
        border-color: rgba(200,147,53,.28);
        transform: translateY(-1px);
        box-shadow: var(--shadow-sm);
    }
    .mini-product-thumb {
        width: 46px;
        height: 46px;
        border-radius: 15px;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, #fff, var(--brand-soft));
        border: 1px solid var(--line);
        color: var(--brand-dark);
        overflow: hidden;
        flex: 0 0 auto;
    }
    .mini-product-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .profile-side-nav .nav-link {
        border-radius: 999px;
        font-weight: 850;
        color: var(--muted);
        padding: 10px 14px;
    }
    .profile-side-nav .nav-link.active {
        color: var(--brand-dark);
        background: var(--brand-soft);
    }
    @media (max-width: 767.98px) {
        .profile-avatar { width: 72px; height: 72px; border-radius: 24px; font-size: 26px; }
        .profile-panel-head, .profile-panel-body { padding: 18px; }
    }
</style>
@endpush

@section('content')
@php
    $initial = strtoupper(mb_substr($user->name ?: 'P', 0, 1));
    $activeTab = session('profil_tab', 'akun');
    if ($errors->has('password_lama') || $errors->has('password')) {
        $activeTab = 'keamanan';
    }
@endphp

<div class="container py-4 py-lg-5">
    <div class="breadcrumb-modern mb-3">
        <a href="{{ route('pembeli-web.home') }}">Beranda</a>
        <i class="bi bi-chevron-right small"></i>
        <span>Profil Saya</span>
    </div>

    <section class="profile-hero p-4 p-lg-5 mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-3 gap-md-4">
                    <div class="profile-avatar">{{ $initial }}</div>
                    <div class="min-w-0">
                        <span class="profile-badge mb-2"><i class="bi bi-person-check-fill"></i> Akun pembeli aktif</span>
                        <h1 class="section-heading h2 mb-2 text-truncate">{{ $user->name }}</h1>
                        <div class="d-flex flex-wrap gap-2 text-muted fw-semibold small">
                            <span><i class="bi bi-envelope me-1"></i>{{ $user->email }}</span>
                            <span><i class="bi bi-phone me-1"></i>{{ $user->telepon ?: 'Nomor HP belum diisi' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="d-flex flex-column flex-sm-row justify-content-lg-end gap-2">
                    <a href="{{ route('pembeli-web.alamat.index') }}" class="btn btn-soft-brand px-4 py-3"><i class="bi bi-geo-alt me-2"></i>Alamat Saya</a>
                    <a href="{{ route('pembeli-web.pesanan.index') }}" class="btn btn-brand px-4 py-3"><i class="bi bi-receipt me-2"></i>Pesanan Saya</a>
                </div>
            </div>
        </div>
    </section>

    <div class="row g-3 g-lg-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="profile-stat">
                <div class="small text-muted fw-black text-uppercase">Total Pesanan</div>
                <div class="value h2 mt-2 mb-1">{{ $statProfil['total_pesanan'] ?? 0 }}</div>
                <div class="small text-brand fw-bold">Semua transaksi</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="profile-stat">
                <div class="small text-muted fw-black text-uppercase">Pesanan Aktif</div>
                <div class="value h2 mt-2 mb-1">{{ $statProfil['pesanan_aktif'] ?? 0 }}</div>
                <div class="small text-brand fw-bold">Masih berjalan</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="profile-stat">
                <div class="small text-muted fw-black text-uppercase">Ulasan</div>
                <div class="value h2 mt-2 mb-1">{{ $statProfil['ulasan'] ?? 0 }}</div>
                <div class="small text-brand fw-bold">Diberikan</div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="profile-stat">
                <div class="small text-muted fw-black text-uppercase">Total Belanja</div>
                <div class="value h4 mt-2 mb-1">{{ $rupiah($statProfil['total_belanja'] ?? 0) }}</div>
                <div class="small text-brand fw-bold">Pesanan dibayar</div>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <div class="profile-panel">
                <div class="profile-panel-head flex-column flex-md-row align-items-md-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="profile-icon"><i class="bi bi-sliders2"></i></div>
                        <div>
                            <h2 class="h5 fw-black mb-1">Pengaturan profil</h2>
                            <p class="text-muted mb-0 small fw-semibold">Perbarui data akun dan keamanan login.</p>
                        </div>
                    </div>
                    <ul class="nav profile-side-nav gap-1" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'akun' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#tab-akun" type="button" role="tab">Akun</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $activeTab === 'keamanan' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#tab-keamanan" type="button" role="tab">Keamanan</button>
                        </li>
                    </ul>
                </div>
                <div class="profile-panel-body">
                    <div class="tab-content">
                        <div class="tab-pane fade {{ $activeTab === 'akun' ? 'show active' : '' }}" id="tab-akun" role="tabpanel">
                            <form action="{{ route('pembeli-web.profil.update') }}" method="POST" class="row g-3">
                                @csrf
                                @method('PUT')
                                <div class="col-md-6">
                                    <label class="profile-label" for="name">Nama akun</label>
                                    <input type="text" id="name" name="name" class="form-control profile-input @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                    @error('name')<div class="invalid-feedback fw-semibold">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="profile-label" for="telepon">Nomor HP akun</label>
                                    <input type="text" id="telepon" name="telepon" class="form-control profile-input @error('telepon') is-invalid @enderror" value="{{ old('telepon', $user->telepon) }}" required>
                                    @error('telepon')<div class="invalid-feedback fw-semibold">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="profile-label" for="email">Email akun</label>
                                    <input type="email" id="email" name="email" class="form-control profile-input @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    @error('email')<div class="invalid-feedback fw-semibold">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <div class="profile-card-line bg-light">
                                        <div class="d-flex gap-3">
                                            <i class="bi bi-info-circle text-brand"></i>
                                            <div class="small text-muted fw-semibold">Data ini dipakai untuk login, invoice, dan konfirmasi pesanan. Nama penerima pengiriman tetap dapat berbeda melalui menu Alamat Saya.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-brand px-4 py-3"><i class="bi bi-check2-circle me-2"></i>Simpan Profil</button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade {{ $activeTab === 'keamanan' ? 'show active' : '' }}" id="tab-keamanan" role="tabpanel">
                            <form action="{{ route('pembeli-web.profil.password') }}" method="POST" class="row g-3">
                                @csrf
                                @method('PUT')
                                <div class="col-12">
                                    <label class="profile-label" for="password_lama">Password saat ini</label>
                                    <input type="password" id="password_lama" name="password_lama" class="form-control profile-input @error('password_lama') is-invalid @enderror" autocomplete="current-password" required>
                                    @error('password_lama')<div class="invalid-feedback fw-semibold">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="profile-label" for="password">Password baru</label>
                                    <input type="password" id="password" name="password" class="form-control profile-input @error('password') is-invalid @enderror" autocomplete="new-password" required>
                                    @error('password')<div class="invalid-feedback fw-semibold">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="profile-label" for="password_confirmation">Konfirmasi password baru</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control profile-input" autocomplete="new-password" required>
                                </div>
                                <div class="col-12">
                                    <div class="profile-card-line bg-light">
                                        <div class="d-flex gap-3">
                                            <i class="bi bi-shield-lock text-brand"></i>
                                            <div class="small text-muted fw-semibold">Gunakan password minimal 6 karakter. Setelah password diganti, akun tetap login di perangkat ini.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-brand px-4 py-3"><i class="bi bi-lock me-2"></i>Ubah Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-panel mt-4">
                <div class="profile-panel-head">
                    <div class="d-flex align-items-center gap-3">
                        <div class="profile-icon"><i class="bi bi-clock-history"></i></div>
                        <div>
                            <h2 class="h5 fw-black mb-1">Pesanan terbaru</h2>
                            <p class="text-muted mb-0 small fw-semibold">Pantau transaksi terakhir dari akun ini.</p>
                        </div>
                    </div>
                    <a href="{{ route('pembeli-web.pesanan.index') }}" class="btn btn-plain btn-sm px-3">Semua</a>
                </div>
                <div class="profile-panel-body">
                    @if($pesananTerbaru->count())
                        <div class="d-grid gap-3">
                            @foreach($pesananTerbaru as $pesanan)
                                @php
                                    $produkPreview = $pesanan->item->first()?->produk;
                                    $gambarPreview = $produkPreview?->gambarUtama?->url_gambar;
                                    $totalItemPesanan = $pesanan->item->sum('jumlah');
                                @endphp
                                <a href="{{ route('pembeli-web.pesanan.show', $pesanan->nomor_invoice) }}" class="order-timeline-card text-decoration-none d-flex align-items-center gap-3">
                                    <div class="mini-product-thumb">
                                        @if($gambarPreview)
                                            <img src="{{ asset('storage/' . $gambarPreview) }}" alt="{{ $produkPreview?->nama }}">
                                        @else
                                            <i class="bi bi-box-seam fs-5"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-grow-1">
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                            <span class="fw-black text-dark">{{ $pesanan->nomor_invoice }}</span>
                                            <span class="badge rounded-pill bg-brand-soft text-brand border border-soft">{{ $statusLabel($pesanan->status) }}</span>
                                        </div>
                                        <div class="small text-muted fw-semibold text-truncate">{{ $produkPreview?->nama ?: 'Produk pesanan' }} · {{ $totalItemPesanan }} item · {{ optional($pesanan->tanggal_pesanan)->format('d M Y H:i') }}</div>
                                    </div>
                                    <div class="text-end d-none d-sm-block">
                                        <div class="fw-black text-brand">{{ $rupiah($pesanan->total_bayar) }}</div>
                                        <small class="text-muted fw-bold">Detail</small>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center p-4 p-lg-5">
                            <div class="profile-icon mx-auto mb-3"><i class="bi bi-receipt"></i></div>
                            <h3 class="h5 fw-black mb-2">Belum ada pesanan</h3>
                            <p class="text-muted fw-semibold mb-3">Produk yang dibeli akan muncul di riwayat pesanan.</p>
                            <a href="{{ route('pembeli-web.produk') }}" class="btn btn-brand px-4 py-3">Belanja Sekarang</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="profile-panel mb-4">
                <div class="profile-panel-head">
                    <div class="d-flex align-items-center gap-3">
                        <div class="profile-icon"><i class="bi bi-compass"></i></div>
                        <div>
                            <h2 class="h5 fw-black mb-1">Menu akun</h2>
                            <p class="text-muted mb-0 small fw-semibold">Kelola data transaksi dari halaman terpisah.</p>
                        </div>
                    </div>
                </div>
                <div class="profile-panel-body d-grid gap-2">
                    <a href="{{ route('pembeli-web.alamat.index') }}" class="btn btn-soft-brand py-3 text-start"><i class="bi bi-geo-alt me-2"></i>Kelola Alamat Saya</a>
                    <a href="{{ route('pembeli-web.pesanan.index') }}" class="btn btn-soft-brand py-3 text-start"><i class="bi bi-receipt me-2"></i>Riwayat Pesanan</a>
                </div>
            </div>
            <div class="profile-panel">
                <div class="profile-panel-head">
                    <div class="d-flex align-items-center gap-3">
                        <div class="profile-icon"><i class="bi bi-box-arrow-right"></i></div>
                        <div>
                            <h2 class="h5 fw-black mb-1">Keluar akun</h2>
                            <p class="text-muted mb-0 small fw-semibold">Akhiri sesi pembeli di perangkat ini.</p>
                        </div>
                    </div>
                </div>
                <div class="profile-panel-body">
                    <form action="{{ route('pembeli-web.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-plain w-100 py-3 text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar dari Akun</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
