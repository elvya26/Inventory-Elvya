@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>Detail Produk</h1>
            <p class="muted">Informasi lengkap spesifikasi produk dan ketersediaan stok.</p>
        </div>
        <a class="btn" href="{{ route('ecommerce.index') }}">← Kembali Ke Toko</a>
    </div>

    <div class="grid" style="grid-template-columns: 1.1fr 0.9fr; gap: 28px; align-items: start;">
        <!-- Area Visual / Media -->
        <div class="panel" style="padding: 24px; display: flex; flex-direction: column; gap: 20px;">
            <div style="background: #f8fafc; border: 1px solid var(--line); border-radius: 12px; height: 320px; display: flex; justify-content: center; align-items: center; overflow: hidden;">
                @if ($item->image_path)
                    <img src="{{ $item->image_path }}" alt="Gambar {{ $item->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <span style="font-size: 84px; opacity: 0.2;">📦</span>
                @endif
            </div>

            @if ($item->video_path)
                <div>
                    <h3 style="font-size: 15px; margin-bottom: 8px;">Video Demonstrasi Produk</h3>
                    <video controls style="width: 100%; border-radius: 10px; border: 1px solid var(--line); max-height: 240px;">
                        <source src="{{ $item->video_path }}">
                        Browser Anda tidak mendukung tag video.
                    </video>
                </div>
            @endif
        </div>

        <!-- Detail Spesifikasi & Beli -->
        <div class="panel" style="padding: 32px; display: flex; flex-direction: column; gap: 20px;">
            <div>
                <span class="badge ok" style="margin-bottom: 8px;">{{ strtoupper($item->category) }}</span>
                <h2 style="font-size: 26px; margin: 0 0 6px; font-weight: 700;">{{ $item->name }}</h2>
                <span class="muted" style="font-size: 13px; font-weight: bold; letter-spacing: 0.05em;">SKU: {{ $item->sku }}</span>
            </div>

            <div style="border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); padding: 16px 0; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span class="muted" style="font-size: 12px; display: block; margin-bottom: 4px;">Harga Satuan</span>
                    <strong style="color: var(--primary); font-size: 24px;">Rp {{ number_format($item->price, 0, ',', '.') }}</strong>
                </div>
                <div style="text-align: right;">
                    <span class="muted" style="font-size: 12px; display: block; margin-bottom: 4px;">Ketersediaan Stok</span>
                    <strong style="font-size: 18px; color: {{ $item->isLowStock() ? 'var(--warn)' : 'var(--ink)' }}">
                        {{ $item->current_stock }} {{ $item->unit }}
                    </strong>
                </div>
            </div>

            <div style="font-size: 14px; line-height: 1.6; display: flex; flex-direction: column; gap: 8px;">
                <div style="display: flex; justify-content: space-between;">
                    <span class="muted">Kategori:</span>
                    <strong>{{ $item->category }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span class="muted">Satuan:</span>
                    <strong>{{ $item->unit }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span class="muted">Lokasi Penyimpanan:</span>
                    <strong>{{ $item->location ?: 'Gudang Utama' }}</strong>
                </div>
            </div>

            <form method="POST" action="{{ route('ecommerce.cart.add', $item) }}" style="border-top: 1px solid var(--line); padding-top: 20px; display: flex; flex-direction: column; gap: 16px;">
                @csrf
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span class="muted" style="font-size: 13px; font-weight: bold; flex-shrink: 0;">Jumlah Pembelian:</span>
                    <input type="number" name="quantity" value="1" min="1" max="{{ $item->current_stock }}" required style="width: 80px; text-align: center;">
                    <span class="muted" style="font-size: 13px;">{{ $item->unit }}</span>
                </div>

                <div style="display: flex; gap: 12px;">
                    <button class="btn primary" type="submit" style="flex-grow: 1; padding: 12px; font-weight: bold; justify-content: center; font-size: 15px;">
                        🛒 Tambah Ke Keranjang
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
