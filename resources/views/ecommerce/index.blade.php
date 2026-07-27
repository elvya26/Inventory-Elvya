@extends('layouts.app')

@section('content')
    <style>
        /* Modernized Styles for Storefront Homepage */
        .store-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .store-title h1 {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 6px;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .store-title p {
            font-size: 14px;
            color: #64748b;
            margin: 0;
        }

        .store-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn-cart {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            color: #334155;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-cart:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            transform: translateY(-1px);
        }

        .btn-cart-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ef4444;
            color: #ffffff;
            font-size: 10px;
            font-weight: 700;
            padding: 3px 7px;
            border-radius: 10px;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 6px rgba(239, 68, 68, 0.15);
        }

        /* Hero Banner Section */
        .store-hero {
            background: linear-gradient(135deg, #0d9488 0%, #115e59 70%, #134e4a 100%);
            border-radius: 20px;
            padding: 44px;
            margin-bottom: 32px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
            box-shadow: 0 12px 30px rgba(13, 148, 136, 0.15);
        }

        .store-hero::before {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            top: -60px;
            left: -60px;
            pointer-events: none;
        }

        .store-hero-info {
            max-width: 65%;
            position: relative;
            z-index: 2;
        }

        .store-hero-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: inline-block;
            margin-bottom: 16px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .store-hero-title {
            color: #ffffff;
            font-size: 32px;
            font-weight: 800;
            margin: 0 0 12px;
            letter-spacing: -0.03em;
            line-height: 1.25;
        }

        .store-hero-desc {
            opacity: 0.9;
            margin: 0;
            font-size: 14px;
            line-height: 1.6;
            color: #ccfbf1;
        }

        .store-hero-illustration {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        /* Search Form Panel */
        .store-search-panel {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.02);
            padding: 16px;
            margin-bottom: 32px;
        }

        .store-search-form {
            display: flex;
            gap: 12px;
        }

        .store-search-input-wrapper {
            position: relative;
            flex-grow: 1;
        }

        .store-search-input-wrapper input {
            width: 100%;
            height: 46px;
            padding-left: 44px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            font-size: 14px;
            transition: all 0.2s ease;
            background: #f8fafc;
        }

        .store-search-input-wrapper input:focus {
            background: #ffffff;
            border-color: #0d9488;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
        }

        .store-search-input-wrapper iconify-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 20px;
        }

        /* Product Cards Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        .product-card {
            background: #ffffff;
            border-radius: 20px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06), 0 2px 4px rgba(15, 23, 42, 0.01);
            border-color: #e2e8f0;
        }

        .product-thumb-container {
            position: relative;
            background: #f8fafc;
            height: 200px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .product-thumb-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-thumb-image {
            transform: scale(1.06);
        }

        .product-category-tag {
            position: absolute;
            top: 14px;
            left: 14px;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(8px);
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 30px;
            letter-spacing: 0.02em;
            z-index: 3;
        }

        .product-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            gap: 10px;
        }

        .product-sku {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .product-name {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            line-height: 1.4;
            min-height: 44px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: color 0.2s ease;
        }

        .product-card:hover .product-name {
            color: #0d9488;
        }

        .product-meta-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: auto;
            padding-top: 12px;
            border-top: 1px solid #f8fafc;
        }

        .product-price-label {
            font-size: 11px;
            color: #64748b;
            display: block;
            margin-bottom: 2px;
        }

        .product-price-value {
            color: #0d9488;
            font-size: 18px;
            font-weight: 800;
            letter-spacing: -0.01em;
        }

        .product-stock-container {
            text-align: right;
        }

        .product-stock-label {
            font-size: 11px;
            color: #64748b;
            display: block;
            margin-bottom: 2px;
        }

        .product-stock-badge {
            font-size: 12px;
            font-weight: 700;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .product-stock-badge.ok {
            background: #f0fdf4;
            color: #166534;
        }

        .product-stock-badge.low {
            background: #fffbeb;
            color: #b45309;
        }

        .product-actions-footer {
            padding: 0 20px 20px;
            display: flex;
            gap: 10px;
        }

        .btn-card-action {
            flex: 1;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            color: #475569;
        }

        .btn-card-action:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .btn-card-action.primary {
            background: #0d9488;
            border-color: #0d9488;
            color: #ffffff;
        }

        .btn-card-action.primary:hover {
            background: #0f766e;
            border-color: #0f766e;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(13, 148, 136, 0.15);
        }

        @media (max-width: 768px) {
            .store-hero {
                flex-direction: column;
                align-items: flex-start;
                padding: 32px;
            }
            .store-hero-info {
                max-width: 100%;
                margin-bottom: 24px;
            }
            .store-hero-illustration {
                align-self: center;
            }
        }
    </style>

    <!-- Header Area -->
    <div class="store-header">
        <div class="store-title">
            <h1>Katalog Belanja</h1>
            <p>Pilih dan pesan produk berkualitas tinggi langsung dari penyimpanan pusat kami.</p>
        </div>
        <div class="store-actions">
            <a class="btn-cart" href="{{ route('ecommerce.cart') }}">
                <iconify-icon icon="solar:cart-large-4-bold-duotone" style="font-size: 20px; color: #475569;"></iconify-icon>
                <span>Keranjang</span>
                @php $cartCount = count(session('cart', [])); @endphp
                @if ($cartCount > 0)
                    <span class="btn-cart-badge">{{ $cartCount }}</span>
                @endif
            </a>
            <a class="btn primary" href="{{ route('ecommerce.customer') }}" style="display: inline-flex; align-items: center; gap: 6px; border-radius: 12px; height: 44px; padding: 0 16px; font-weight: 700;">
                <iconify-icon icon="solar:user-circle-bold-duotone" style="font-size: 20px;"></iconify-icon>
                Akun Saya
            </a>
        </div>
    </div>

    <!-- Hero Banner Section -->
    <div class="store-hero">
        <div class="store-hero-info">
            <span class="store-hero-badge">Promo Terbatas</span>
            <h2 class="store-hero-title">Belanja Instan Langsung Dari Gudang</h2>
            <p class="store-hero-desc">Dapatkan kepastian stok 100% akurat. Transaksi kilat dengan integrasi DOKU Checkout, pelacakan pengiriman Kiriminaja real-time, dan pengiriman aman.</p>
        </div>
        <div class="store-hero-illustration">
            <iconify-icon icon="solar:card-send-bold-duotone" style="font-size: 96px; color: rgba(255,255,255,0.9);"></iconify-icon>
        </div>
    </div>

    <!-- Search Form Panel -->
    <div class="store-search-panel">
        <form method="GET" action="{{ route('ecommerce.index') }}" class="store-search-form">
            <div class="store-search-input-wrapper">
                <input name="search" value="{{ request('search') }}" placeholder="Cari nama barang atau kategori belanja...">
                <iconify-icon icon="solar:magnifer-linear"></iconify-icon>
            </div>
            <button class="btn primary" type="submit" style="padding: 0 24px; border-radius: 12px; height: 46px; font-weight: 700;">Cari Produk</button>
        </form>
    </div>

    <!-- Grid Katalog Barang -->
    <div class="product-grid">
        @forelse ($items as $item)
            <div class="product-card">
                <!-- Thumbnail -->
                <div class="product-thumb-container">
                    @if ($item->image_path)
                        <img src="{{ $item->image_path }}" alt="{{ $item->name }}" class="product-thumb-image">
                    @else
                        <iconify-icon icon="solar:box-broken" style="font-size: 56px; color: #cbd5e1;"></iconify-icon>
                    @endif
                    <span class="product-category-tag">
                        {{ $item->category }}
                    </span>
                </div>

                <!-- Body -->
                <div class="product-body">
                    <span class="product-sku">{{ $item->sku }}</span>
                    <h3 class="product-name" title="{{ $item->name }}">
                        {{ $item->name }}
                    </h3>
                    
                    <div class="product-meta-row">
                        <div>
                            <span class="product-price-label">Harga</span>
                            <strong class="product-price-value">Rp {{ number_format($item->price, 0, ',', '.') }}</strong>
                        </div>
                        <div class="product-stock-container">
                            <span class="product-stock-label">Ketersediaan</span>
                            <span class="product-stock-badge {{ $item->isLowStock() ? 'low' : 'ok' }}">
                                {{ $item->current_stock }} {{ $item->unit }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="product-actions-footer">
                    <a class="btn-card-action" href="{{ route('ecommerce.show', $item) }}">
                        <iconify-icon icon="solar:eye-bold-duotone" style="font-size: 16px;"></iconify-icon>
                        Detail
                    </a>
                    <form method="POST" action="{{ route('ecommerce.cart.add', $item) }}" style="flex: 1; display: flex;">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button class="btn-card-action primary" type="submit" style="width: 100%;">
                            <iconify-icon icon="solar:cart-plus-bold-duotone" style="font-size: 16px;"></iconify-icon>
                            + Keranjang
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="panel text-center" style="grid-column: 1 / -1; padding: 56px; border: 1px dashed #e2e8f0; border-radius: 20px;">
                <iconify-icon icon="solar:magnifer-broken" style="font-size: 56px; color: #cbd5e1; margin-bottom: 14px; display: inline-block;"></iconify-icon>
                <h3 style="font-weight: 700; color: #1e293b; margin: 0 0 8px;">Produk Tidak Ditemukan</h3>
                <p class="muted" style="margin-bottom: 20px;">Maaf, produk yang Anda cari tidak tersedia atau sedang habis.</p>
                <a class="btn primary" href="{{ route('ecommerce.index') }}" style="border-radius: 12px; font-weight: 700;">Lihat Semua Produk</a>
            </div>
        @endforelse
    </div>

    <div class="pagination" style="margin-top: 32px;">{{ $items->links() }}</div>
@endsection
