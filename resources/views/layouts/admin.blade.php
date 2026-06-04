<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SiTahu Admin')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --brand-color: #dfba68;
            --brand-active-bg: rgba(223, 186, 104, 0.15);
            --brand-active-text: #8a6321;
            --border-color: #e5e7eb;
            --sidebar-width: 260px;
            --sidebar-collapsed-width: 76px;
            --topbar-height: 60px;
        }

        body { 
            background-color: #f3f4f6;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, sans-serif; 
            color: #374151;
        }
        
        /* Layout Grid Utama */
        .app-shell { display: grid; grid-template-columns: var(--sidebar-width) 1fr; min-height: 100vh; transition: grid-template-columns 0.2s ease; }
        body.sidebar-collapsed .app-shell { grid-template-columns: var(--sidebar-collapsed-width) 1fr; }
        
        /* Sidebar Styling */
        .app-sidebar { position: sticky; top: 0; height: 100vh; overflow-y: auto; z-index: 1040; background-color: #ffffff; border-right: 1px solid var(--border-color); }
        .app-sidebar::-webkit-scrollbar { width: 4px; }
        .app-sidebar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        
        /* ==========================================
           PERBAIKAN MODE SEMPIT (COLLAPSED)
           ========================================== */
        body.sidebar-collapsed .brand-info,
        body.sidebar-collapsed .sidebar-label,
        body.sidebar-collapsed .sidebar-chevron,
        body.sidebar-collapsed .sidebar-submenu { display: none !important; }
        
        body.sidebar-collapsed .brand-block { padding: 0; justify-content: center; }
        body.sidebar-collapsed .app-sidebar { padding-left: 0.5rem !important; padding-right: 0.5rem !important; }
        
        /* Membuat tombol menjadi kotak simetris */
        body.sidebar-collapsed .sidebar-link,
        body.sidebar-collapsed .sidebar-parent { 
            justify-content: center; 
            padding: 0; 
            width: 44px; 
            height: 44px; 
            margin: 0 auto 0.25rem auto; 
            border-radius: 0.5rem;
        }
        
        /* Menghilangkan garis kuning vertikal di mode sempit */
        body.sidebar-collapsed .sidebar-link.active::before, 
        body.sidebar-collapsed .sidebar-parent.active::before { 
            display: none; 
        }

        /* ========================================== */
        
        /* Brand Logo Area */
        .brand-block { height: var(--topbar-height); padding: 0 1.25rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.75rem; }
        
        /* Menu Link Styling */
        .sidebar-link, .sidebar-parent { 
            display: flex; align-items: center; width: 100%; border: none; background: transparent;
            padding: 0.6rem 1rem; margin-bottom: 0.25rem; border-radius: 0.375rem;
            color: #4b5563; text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: all 0.15s;
        }
        
        /* Ikon Navigasi */
        .sidebar-icon { display: inline-flex; align-items: center; justify-content: center; width: 24px; font-size: 1.1rem; flex-shrink: 0; }
        .sidebar-label { flex-grow: 1; text-align: left; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-left: 0.5rem; }
        .sidebar-chevron { font-size: 0.75rem; transition: transform 0.2s; color: #9ca3af; }
        .sidebar-parent[aria-expanded="true"] .sidebar-chevron { transform: rotate(180deg); }
        
        /* Hover & Active States */
        .sidebar-link:hover, .sidebar-parent:hover { background-color: #f3f4f6; color: #111827; }
        
        .sidebar-link.active, .sidebar-parent.active { 
            background-color: var(--brand-active-bg); 
            color: var(--brand-active-text); 
            font-weight: 600; 
            position: relative;
        }
        
        /* Pita penanda aktif (Hanya tampil di mode lebar) */
        .sidebar-link.active::before, .sidebar-parent.active::before {
            content: ''; position: absolute; left: 0; top: 10%; bottom: 10%; width: 3px;
            background-color: var(--brand-active-text); border-radius: 0 4px 4px 0;
        }
        
        /* Sub-menu (Tree view) */
        .sidebar-submenu { padding-left: 2.25rem; margin-top: 0.15rem; margin-bottom: 0.5rem; position: relative; }
        .sidebar-submenu::before { content: ''; position: absolute; left: 1.25rem; top: 0; bottom: 0.5rem; width: 1px; background-color: #e5e7eb; }
        
        .sidebar-submenu a { 
            display: block; padding: 0.4rem 0.75rem; color: #6b7280; text-decoration: none; 
            font-size: 0.85rem; border-radius: 0.375rem; transition: all 0.15s; 
        }
        .sidebar-submenu a:hover { color: #111827; background-color: #f3f4f6; }
        .sidebar-submenu a.active { color: var(--brand-active-text); font-weight: 600; }

        /* Topbar Styling */
        .topbar { 
            height: var(--topbar-height); background-color: #ffffff; border-bottom: 1px solid var(--border-color); 
            display: flex; align-items: center; justify-content: space-between; padding: 0 1.5rem; position: sticky; top: 0; z-index: 1030;
        }
        
        /* Utilitas Tambahan */
        .content-wrapper { padding: 1.5rem; }
        
        /* Responsif Mobile */
        @media(max-width: 900px){
            .app-shell, body.sidebar-collapsed .app-shell { grid-template-columns: 1fr; }
            .app-sidebar { position: fixed; inset: 0 auto 0 0; width: var(--sidebar-width); transform: translateX(-110%); box-shadow: 4px 0 15px rgba(0,0,0,0.05); }
            .app-sidebar.open { transform: translateX(0); }
            body.sidebar-collapsed .brand-info, body.sidebar-collapsed .sidebar-label, body.sidebar-collapsed .sidebar-chevron, body.sidebar-collapsed .sidebar-submenu { display: block !important; }
            body.sidebar-collapsed .app-sidebar { padding-left: 1rem !important; padding-right: 1rem !important; }
            body.sidebar-collapsed .sidebar-link, body.sidebar-collapsed .sidebar-parent { justify-content: flex-start; padding-left: 1rem; width: 100%; height: auto; }
            body.sidebar-collapsed .sidebar-link.active::before, body.sidebar-collapsed .sidebar-parent.active::before { display: block; }
            .topbar { padding: 0 1rem; }
            .content-wrapper { padding: 1rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="offcanvas-backdrop fade d-none" id="sidebarOverlay"></div>

<div class="app-shell">
    
    <aside class="app-sidebar p-0 d-flex flex-column" id="sidebar">
        
        <div class="brand-block">
            <button class="btn btn-link p-0 text-dark text-decoration-none d-flex align-items-center justify-content-center flex-shrink-0 border-0" id="brandToggle" style="width: 24px;">
                <i class="bi bi-box-seam-fill" style="color: var(--brand-color); font-size: 1.4rem;"></i>
            </button>
            <div class="brand-info ms-1">
                <div class="fw-bold fs-5 lh-1 text-dark" style="letter-spacing: -0.02em;">SiTahu</div>
            </div>
        </div>

        <nav class="flex-grow-1 px-3 py-3 d-flex flex-column gap-1">
            
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-grid-1x2"></i></span>
                <span class="sidebar-label">Dashboard</span>
            </a>

            <div>
                <button type="button" class="sidebar-parent {{ request()->routeIs('admin.produk.*') || request()->routeIs('admin.stok.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#menuProduk" aria-expanded="{{ request()->routeIs('admin.produk.*') || request()->routeIs('admin.stok.*') ? 'true' : 'false' }}">
                    <span class="sidebar-icon"><i class="bi bi-box-seam"></i></span>
                    <span class="sidebar-label">Produk</span>
                    <i class="bi bi-chevron-down sidebar-chevron"></i>
                </button>
                <div class="collapse {{ request()->routeIs('admin.produk.*') || request()->routeIs('admin.stok.*') ? 'show' : '' }}" id="menuProduk">
                    <div class="sidebar-submenu">
                        <a href="{{ route('admin.produk.index') }}" class="{{ request()->routeIs('admin.produk.*') ? 'active' : '' }}">Produk Tahu</a>
                        <a href="{{ route('admin.stok.index') }}" class="{{ request()->routeIs('admin.stok.*') ? 'active' : '' }}">Stok Produk</a>
                    </div>
                </div>
            </div>

            <div>
                <button type="button" class="sidebar-parent {{ request()->routeIs('admin.pesanan.*') || request()->routeIs('admin.pembayaran.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#menuTransaksi" aria-expanded="{{ request()->routeIs('admin.pesanan.*') || request()->routeIs('admin.pembayaran.*') ? 'true' : 'false' }}">
                    <span class="sidebar-icon"><i class="bi bi-receipt"></i></span>
                    <span class="sidebar-label">Transaksi</span>
                    <i class="bi bi-chevron-down sidebar-chevron"></i>
                </button>
                <div class="collapse {{ request()->routeIs('admin.pesanan.*') || request()->routeIs('admin.pembayaran.*') ? 'show' : '' }}" id="menuTransaksi">
                    <div class="sidebar-submenu">
                        <a href="{{ route('admin.pesanan.index') }}" class="{{ request()->routeIs('admin.pesanan.*') ? 'active' : '' }}">Pesanan</a>
                        <a href="{{ route('admin.pembayaran.index') }}" class="{{ request()->routeIs('admin.pembayaran.*') ? 'active' : '' }}">Pembayaran</a>
                    </div>
                </div>
            </div>

            <div>
                <button type="button" class="sidebar-parent {{ request()->routeIs('admin.pengiriman.*') || request()->routeIs('admin.pembeli.*') || request()->routeIs('admin.ulasan.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#menuOperasional" aria-expanded="{{ request()->routeIs('admin.pengiriman.*') || request()->routeIs('admin.pembeli.*') || request()->routeIs('admin.ulasan.*') ? 'true' : 'false' }}">
                    <span class="sidebar-icon"><i class="bi bi-truck"></i></span>
                    <span class="sidebar-label">Operasional</span>
                    <i class="bi bi-chevron-down sidebar-chevron"></i>
                </button>
                <div class="collapse {{ request()->routeIs('admin.pengiriman.*') || request()->routeIs('admin.pembeli.*') || request()->routeIs('admin.ulasan.*') ? 'show' : '' }}" id="menuOperasional">
                    <div class="sidebar-submenu">
                        <a href="{{ route('admin.pengiriman.index') }}" class="{{ request()->routeIs('admin.pengiriman.*') ? 'active' : '' }}">Pengantaran</a>
                        <a href="{{ route('admin.pembeli.index') }}" class="{{ request()->routeIs('admin.pembeli.*') ? 'active' : '' }}">Data Pembeli</a>
                        <a href="{{ route('admin.ulasan.index') }}" class="{{ request()->routeIs('admin.ulasan.*') ? 'active' : '' }}">Ulasan</a>
                    </div>
                </div>
            </div>

            <div>
                <button type="button" class="sidebar-parent {{ request()->routeIs('admin.laporan.*') || request()->routeIs('admin.banner.*') ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#menuKonten" aria-expanded="{{ request()->routeIs('admin.laporan.*') || request()->routeIs('admin.banner.*') ? 'true' : 'false' }}">
                    <span class="sidebar-icon"><i class="bi bi-file-earmark-bar-graph"></i></span>
                    <span class="sidebar-label">Konten & Laporan</span>
                    <i class="bi bi-chevron-down sidebar-chevron"></i>
                </button>
                <div class="collapse {{ request()->routeIs('admin.laporan.*') || request()->routeIs('admin.banner.*') ? 'show' : '' }}" id="menuKonten">
                    <div class="sidebar-submenu">
                        <a href="{{ route('admin.laporan.index') }}" class="{{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">Laporan</a>
                        <a href="{{ route('admin.banner.index') }}" class="{{ request()->routeIs('admin.banner.*') ? 'active' : '' }}">Banner</a>
                    </div>
                </div>
            </div>

            <div class="mt-2 pt-2 border-top">
                <a href="{{ route('admin.pengaturan.edit') }}" class="sidebar-link {{ (request()->routeIs('admin.pengaturan.*') || request()->routeIs('admin.akun.*')) ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-gear"></i></span>
                    <span class="sidebar-label">Pengaturan</span>
                </a>

                <a href="{{ route('admin.pengguna-admin.index') }}" class="sidebar-link {{ request()->routeIs('admin.pengguna-admin.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-people"></i></span>
                    <span class="sidebar-label">Pengguna Admin</span>
                </a>
            </div>
            
        </nav>
    </aside>

    <main class="app-main d-flex flex-column min-vh-100">
        
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link text-dark p-0 d-lg-none" type="button" id="mobileMenuToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="d-none d-md-flex align-items-center gap-2">
                    <span class="badge bg-success rounded-circle p-1"></span>
                    <span class="small fw-medium text-muted"></span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                
                <div class="d-none d-md-block small text-muted">
                    {{ now()->translatedFormat('d M Y') }}
                </div>

                <div class="dropdown">
                    <button class="btn btn-light rounded-circle position-relative p-0 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border: 1px solid var(--border-color);" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell text-secondary"></i>
                        <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-white rounded-circle mt-1 ms-n1"></span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end border-0 shadow rounded-3 mt-2" style="width: 300px; padding: 0;">
                        <div class="px-3 py-2 border-bottom bg-light">
                            <strong class="small">Notifikasi</strong>
                        </div>
                        <div class="p-0">
                            <a href="#" class="dropdown-item p-3 border-bottom d-flex align-items-start gap-3 text-wrap">
                                <div class="text-warning"><i class="bi bi-cart-fill fs-5"></i></div>
                                <div>
                                    <div class="small fw-semibold text-dark">Pesanan Baru Masuk</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Silakan cek halaman pesanan untuk memproses.</div>
                                </div>
                            </a>
                        </div>
                        <div class="p-2 text-center">
                            <a href="#" class="small text-decoration-none fw-medium">Lihat Semua</a>
                        </div>
                    </div>
                </div>

                <div class="dropdown ms-1">
                    <button class="btn border-0 p-0 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="text-end d-none d-md-block">
                            <div class="fw-semibold small text-dark lh-1">{{ auth()->user()->name ?? 'Admin' }}</div>
                            <div class="text-muted mt-1" style="font-size: 0.7rem;">{{ auth()->user()->email ?? 'admin@sitahu.com' }}</div>
                        </div>
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" style="width: 36px; height: 36px; font-size: 0.85rem; background-color: var(--brand-color);">
                            {{ substr(auth()->user()->name ?? 'AD', 0, 2) }}
                        </div>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end border-0 shadow rounded-3 mt-2 py-2">
                        <a class="dropdown-item small py-2 d-flex align-items-center gap-2" href="{{ route('admin.akun.edit') }}">
                            <i class="bi bi-person-gear text-muted"></i> Pengaturan Akun
                        </a>
                        <div class="dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" data-confirm-title="Keluar Akun" data-confirm-message="Yakin ingin keluar dari dashboard SiTahu?" data-confirm-button="Keluar">
                            @csrf
                            <button class="dropdown-item small py-2 d-flex align-items-center gap-2 text-danger fw-medium" type="submit">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-wrapper flex-grow-1">
            
            @yield('content')

            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1080">
                @if(session('success'))
                    <div class="toast align-items-center text-bg-success border-0 shadow js-auto-toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="toast text-bg-danger border-0 shadow js-auto-toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle me-2"></i>Periksa input</strong>
                                <ul class="mb-0 small ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 mt-2" data-bs-dismiss="toast" aria-label="Tutup"></button>
                        </div>
                    </div>
                @endif
            </div>
            
        </div>
    </main>
</div>

<div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-body p-4 text-center">
                <div class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center text-warning-emphasis bg-warning-subtle" style="width:64px;height:64px;">
                    <i class="bi bi-exclamation-triangle fs-3"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2" id="globalConfirmTitle">Konfirmasi Tindakan</h5>
                <p class="text-muted small mb-0" id="globalConfirmMessage">Apakah kamu yakin ingin melanjutkan tindakan ini?</p>
            </div>
            <div class="modal-footer border-0 bg-light p-3 justify-content-center gap-2">
                <button type="button" class="btn btn-light border fw-medium px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn fw-bold px-4 text-white" id="globalConfirmButton" style="background: var(--brand-color, #dfba68);">
                    Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const body = document.body;
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const brandToggle = document.getElementById('brandToggle');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');

    if (localStorage.getItem('sitahu-sidebar-collapsed') === '1' && window.innerWidth > 900) { 
        body.classList.add('sidebar-collapsed'); 
    }

    brandToggle?.addEventListener('click', () => {
        if (window.innerWidth > 900) {
            body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sitahu-sidebar-collapsed', body.classList.contains('sidebar-collapsed') ? '1' : '0');
        }
    });

    mobileMenuToggle?.addEventListener('click', () => {
        sidebar.classList.add('open');
        sidebarOverlay.classList.remove('d-none');
        sidebarOverlay.classList.add('show');
    });

    sidebarOverlay?.addEventListener('click', () => {
        sidebar.classList.remove('open');
        sidebarOverlay.classList.add('d-none');
        sidebarOverlay.classList.remove('show');
    });

    document.querySelectorAll('.app-sidebar [data-bs-toggle="collapse"]').forEach((button) => {
        button.addEventListener('click', () => {
            if (body.classList.contains('sidebar-collapsed') && window.innerWidth > 900) {
                body.classList.remove('sidebar-collapsed');
                localStorage.setItem('sitahu-sidebar-collapsed', '0');
            }
        });
    });

    document.querySelectorAll('.js-instant-filter').forEach((form) => {
        let timer = null;
        const submitForm = () => {
            window.clearTimeout(timer);
            timer = window.setTimeout(() => form.requestSubmit(), 450);
        };

        form.querySelectorAll('input[type="search"], input[type="text"], input[type="date"], select').forEach((field) => {
            field.addEventListener('input', submitForm);
            field.addEventListener('change', () => form.requestSubmit());
        });
    });

    document.querySelectorAll('.js-auto-toast').forEach((toastEl) => {
        new bootstrap.Toast(toastEl, { delay: 4200 }).show();
    });

    let pendingConfirmForm = null;
    const globalConfirmModalEl = document.getElementById('globalConfirmModal');
    const globalConfirmTitle = document.getElementById('globalConfirmTitle');
    const globalConfirmMessage = document.getElementById('globalConfirmMessage');
    const globalConfirmButton = document.getElementById('globalConfirmButton');
    const globalConfirmModal = globalConfirmModalEl ? new bootstrap.Modal(globalConfirmModalEl) : null;

    document.querySelectorAll('form[data-confirm-title], form[data-confirm-message]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.confirmed === '1') {
                form.dataset.confirmed = '0';
                return;
            }

            event.preventDefault();
            pendingConfirmForm = form;

            if (globalConfirmTitle) {
                globalConfirmTitle.textContent = form.dataset.confirmTitle || 'Konfirmasi Tindakan';
            }

            if (globalConfirmMessage) {
                globalConfirmMessage.textContent = form.dataset.confirmMessage || 'Apakah kamu yakin ingin melanjutkan tindakan ini?';
            }

            if (globalConfirmButton) {
                globalConfirmButton.textContent = form.dataset.confirmButton || 'Lanjutkan';
            }

            globalConfirmModal?.show();
        });
    });

    globalConfirmButton?.addEventListener('click', () => {
        if (!pendingConfirmForm) return;

        pendingConfirmForm.dataset.confirmed = '1';
        globalConfirmModal?.hide();
        pendingConfirmForm.requestSubmit();
    });

</script>
@stack('scripts')
</body>
</html>