@extends('layouts.pembeli')
@section('title','Produk Web Pembeli - SiTahu')
@section('content')
<section style="margin:24px 0">
    <h1 style="margin:0;font-size:34px;letter-spacing:-.05em">Produk Tahu</h1>
    <p class="sub">Data produk diambil dari endpoint <strong>/api/products</strong>, bukan data statis.</p>
</section>
<div id="loading" class="notice">Memuat produk dari API...</div>
<div id="products" class="grid" style="margin-top:16px"></div>
@endsection
@push('scripts')
<script>
(async function(){
    const loading = document.getElementById('loading');
    const box = document.getElementById('products');
    try {
        const res = await fetch('{{ url('/api/products') }}');
        const json = await res.json();
        const items = json.data?.data || [];
        loading.style.display = 'none';
        if (!items.length) {
            box.innerHTML = '<div class="card card-pad"><strong>Belum ada produk</strong><p class="sub">Produk akan muncul setelah admin menambahkan data.</p></div>';
            return;
        }
        box.innerHTML = items.map(item => `
            <article class="card">
                ${item.gambar_utama ? `<img src="${item.gambar_utama}" alt="${item.nama}" class="product-img" style="width:100%;object-fit:cover">` : `<div class="product-img">PRODUK</div>`}
                <div class="card-pad">
                    <strong>${item.nama}</strong>
                    <div class="sub">${item.satuan || '-'} · Stok ${item.stok}</div>
                    <div class="price">Rp ${Number(item.harga).toLocaleString('id-ID')}</div>
                </div>
            </article>
        `).join('');
    } catch (e) {
        loading.textContent = 'Gagal memuat produk dari API. Pastikan routes/api.php sudah aktif.';
    }
})();
</script>
@endpush
