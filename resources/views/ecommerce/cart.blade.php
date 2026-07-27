@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>Keranjang Belanja</h1>
            <p class="muted">Kelola barang belanjaan Anda sebelum melakukan checkout pembayaran.</p>
        </div>
        <a class="btn" href="{{ route('ecommerce.index') }}">← Lanjut Belanja</a>
    </div>

    @if (empty($cart))
        <div class="panel text-center" style="padding: 60px;">
            <span style="font-size: 64px;">🛒</span>
            <h2 style="margin-top: 20px;">Keranjang belanja Anda kosong</h2>
            <p class="muted" style="margin-bottom: 24px;">Anda belum menambahkan produk apa pun ke keranjang belanja Anda.</p>
            <a class="btn primary" href="{{ route('ecommerce.index') }}">Mulai Belanja</a>
        </div>
    @else
        <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 24px; align-items: start;">
            <!-- List Item Keranjang -->
            <div class="panel" style="padding: 0; overflow: hidden;">
                <table style="border: none; width: 100%;">
                    <thead style="background: #f8fafc;">
                        <tr>
                            <th style="padding: 16px;">Produk</th>
                            <th style="padding: 16px;">Harga</th>
                            <th style="padding: 16px; text-align: center;">Jumlah</th>
                            <th style="padding: 16px; text-align: right;">Subtotal</th>
                            <th style="padding: 16px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach ($cart as $id => $item)
                            @php
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                            @endphp
                            <tr style="border-bottom: 1px solid var(--line);">
                                <td style="padding: 16px;">
                                    <div class="item-cell">
                                        @if ($item['image_path'])
                                            <img class="item-thumb" src="{{ $item['image_path'] }}" alt="{{ $item['name'] }}">
                                        @else
                                            <span class="item-thumb item-thumb--empty">—</span>
                                        @endif
                                        <div>
                                            <strong style="display: block; font-size: 14px;">{{ $item['name'] }}</strong>
                                            <span class="muted" style="font-size: 11px;">SKU: {{ $item['sku'] }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px; font-weight: 500; font-size: 14px;">
                                    Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </td>
                                <td style="padding: 16px; text-align: center;">
                                    <form method="POST" action="{{ route('ecommerce.cart.update') }}" style="display: inline-flex; align-items: center; gap: 4px; justify-content: center;">
                                        @csrf
                                        <input type="hidden" name="item_id" value="{{ $id }}">
                                        <input type="hidden" name="action" value="update">
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" style="width: 60px; text-align: center; min-height: 32px; padding: 4px;" onchange="this.form.submit()">
                                        <span class="muted" style="font-size: 12px; margin-left: 2px;">{{ $item['unit'] }}</span>
                                    </form>
                                </td>
                                <td style="padding: 16px; text-align: right; font-weight: 700; color: var(--primary); font-size: 14px;">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </td>
                                <td style="padding: 16px; text-align: center;">
                                    <form method="POST" action="{{ route('ecommerce.cart.update') }}">
                                        @csrf
                                        <input type="hidden" name="item_id" value="{{ $id }}">
                                        <input type="hidden" name="action" value="remove">
                                        <button type="submit" class="btn danger" style="padding: 4px 8px; font-size: 12px; min-height: auto;">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Ringkasan Order -->
            <div class="panel" style="padding: 24px;">
                <h2 style="font-size: 18px; margin-bottom: 16px; font-weight: 700; border-bottom: 1px solid var(--line); padding-bottom: 10px;">Ringkasan Belanja</h2>
                
                <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; font-size: 14px;">
                        <span class="muted">Total Barang:</span>
                        <strong>{{ count($cart) }} Baris</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; border-top: 1px dashed var(--line); padding-top: 12px;">
                        <span class="muted">Subtotal Belanja:</span>
                        <strong style="color: var(--primary); font-size: 16px;">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <a class="btn primary" href="{{ route('ecommerce.checkout') }}" style="width: 100%; padding: 12px; justify-content: center; font-weight: bold; font-size: 15px;">
                        Lanjut ke Checkout →
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
