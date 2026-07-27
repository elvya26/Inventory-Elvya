@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>Kelola Pesanan #{{ $order->order_number }}</h1>
            <p class="muted">Status Pesanan: 
                <span class="badge {{ $order->status === 'paid' || $order->status === 'shipped' || $order->status === 'completed' ? 'ok' : ($order->status === 'cancelled' ? 'danger' : 'warn') }}">
                    {{ strtoupper($order->status) }}
                </span>
            </p>
        </div>
        <a class="btn" href="{{ route('admin.orders.index') }}">← Kembali ke Pesanan</a>
    </div>

    <div class="grid" style="grid-template-columns: 2fr 1.1fr; gap: 24px; align-items: start;">
        <!-- Area Kiri: Rincian Produk, Penerima, dan Alamat -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Informasi Barang Dibeli -->
            <div class="panel" style="padding: 24px;">
                <h2 style="font-size: 16px; margin-bottom: 16px; font-weight: 700;">🛒 Barang yang Dipesan</h2>
                <table style="width: 100%; border-collapse: collapse; border: none;">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--line); background: #f8fafc;">
                            <th style="padding: 10px; text-align: left; font-size: 12px;" class="muted">SKU / Nama</th>
                            <th style="padding: 10px; text-align: center; font-size: 12px;" class="muted">Jumlah</th>
                            <th style="padding: 10px; text-align: right; font-size: 12px;" class="muted">Harga Satuan</th>
                            <th style="padding: 10px; text-align: right; font-size: 12px;" class="muted">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr style="border-bottom: 1px dashed var(--line);">
                                <td style="padding: 12px 10px; font-size: 13px;">
                                    <strong>{{ $item->item?->name ?? 'Produk dihapus' }}</strong><br>
                                    <span class="muted" style="font-size: 11px;">SKU: {{ $item->item?->sku ?? '-' }}</span>
                                </td>
                                <td style="padding: 12px 10px; text-align: center; font-size: 13px;">
                                    {{ $item->quantity }} {{ $item->item?->unit ?? 'pcs' }}
                                </td>
                                <td style="padding: 12px 10px; text-align: right; font-size: 13px;">
                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                </td>
                                <td style="padding: 12px 10px; text-align: right; font-weight: bold; font-size: 13px; color: var(--primary);">
                                    Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Alamat & Informasi Pengiriman -->
            <div class="panel" style="padding: 24px;">
                <h2 style="font-size: 16px; margin-bottom: 16px; font-weight: 700;">🏠 Informasi Pengiriman & Tujuan</h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 13px; line-height: 1.5; margin-bottom: 14px;">
                    <div>
                        <span class="muted" style="display: block;">Nama Penerima</span>
                        <strong>{{ $order->customer_name }}</strong>
                    </div>
                    <div>
                        <span class="muted" style="display: block;">Telepon/WhatsApp</span>
                        <strong>{{ $order->customer_phone ?: '-' }}</strong>
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <span class="muted" style="display: block;">Alamat Lengkap</span>
                        <strong>{{ $order->shipping_address }}</strong>
                    </div>
                </div>
            </div>

            <!-- Lacak Resi (Kiriminaja Tracking) -->
            @if ($order->waybill && $trackingInfo)
                <div class="panel" style="padding: 24px;">
                    <h2 style="font-size: 16px; margin-bottom: 12px; font-weight: 700;">📦 Status Pelacakan Paket (API Kiriminaja)</h2>
                    <div style="background: var(--bg); border: 1px solid var(--line); border-radius: 8px; padding: 16px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span class="muted" style="font-size: 11px; display: block;">NOMOR RESI Aggregator</span>
                            <strong style="font-size: 16px; font-family: monospace; color: var(--primary);">{{ $order->waybill }}</strong>
                        </div>
                        <span class="badge ok">{{ $trackingInfo['status'] }}</span>
                    </div>

                    <!-- Timeline Lacak Paket -->
                    <div style="display: flex; flex-direction: column; gap: 14px; padding-left: 8px;">
                        @foreach ($trackingInfo['history'] as $history)
                            <div style="position: relative; padding-left: 20px; border-left: 2px solid var(--primary); font-size: 12px; line-height: 1.5;">
                                <div style="position: absolute; left: -6px; top: 4px; width: 10px; height: 10px; border-radius: 50%; background: var(--primary);"></div>
                                <span class="muted" style="display: block; font-size: 10px; font-weight: bold;">{{ $history['time'] }}</span>
                                <span>{{ $history['description'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Area Kanan: Status Billing & Aksi Kurir -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Informasi Billing -->
            <div class="panel" style="padding: 24px;">
                <h2 style="font-size: 16px; margin-bottom: 16px; font-weight: 700; border-bottom: 1px solid var(--line); padding-bottom: 10px;">Billing & Pembayaran</h2>
                
                <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px; line-height: 1.5; margin-bottom: 16px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="muted">Subtotal Barang:</span>
                        <strong>Rp {{ number_format($order->total_amount - $order->shipping_cost, 0, ',', '.') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span class="muted">Ongkir ({{ strtoupper($order->shipping_courier) }}):</span>
                        <strong>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 15px; border-top: 1px dashed var(--line); padding-top: 10px; margin-top: 4px;">
                        <strong>Total Tagihan:</strong>
                        <strong style="color: var(--primary); font-size: 16px;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                    </div>
                </div>

                @if ($order->payment)
                    <div style="background: var(--bg); border: 1px solid var(--line); border-radius: 8px; padding: 14px; font-size: 12px; line-height: 1.6; display: flex; flex-direction: column; gap: 6px;">
                        <div>
                            <span class="muted">Metode Pembayaran:</span>
                            <strong style="display: block;">{{ $order->payment->payment_method }} {{ $order->payment->bank_name }}</strong>
                        </div>
                        <div>
                            <span class="muted">Nomor Virtual Account:</span>
                            <strong style="display: block; font-family: monospace; font-size: 13px;">{{ $order->payment->va_number }}</strong>
                        </div>
                        <div>
                            <span class="muted">Status Pembayaran:</span>
                            <span class="badge {{ $order->payment->status === 'completed' ? 'ok' : 'warn' }}" style="font-size: 9px; display: inline-block;">
                                {{ $order->payment->status === 'completed' ? 'LUNAS / COMPLETED' : 'PENDING' }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Aksi Logistik Kiriminaja -->
            @if ($order->status === 'paid')
                <div class="panel" style="padding: 24px; border: 1px solid #bbf7d0; background: #f0fdf4;">
                    <h3 style="font-size: 15px; margin-bottom: 8px; font-weight: 700; color: #166534;">📦 Proses Pengiriman</h3>
                    <p class="muted" style="font-size: 12px; margin-bottom: 16px; line-height: 1.4;">Pesanan ini telah lunas dibayar. Silakan lakukan booking kurir pengiriman secara otomatis melalui integrasi API Kiriminaja.</p>
                    
                    <form method="POST" action="{{ route('admin.orders.ship', $order) }}">
                        @csrf
                        <button class="btn primary" type="submit" style="width: 100%; justify-content: center; font-weight: bold; background: #059669; border-color: #059669;">
                            🚚 Kirim Paket (Booking Resi)
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection
