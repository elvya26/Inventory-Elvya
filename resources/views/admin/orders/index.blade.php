@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>Kelola Pesanan E-Commerce</h1>
            <p class="muted">Monitor billing pemesanan, konfirmasi pembayaran, dan kelola ekspedisi pengiriman.</p>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="panel no-print" style="margin-bottom: 20px; padding: 18px;">
        <form method="GET" action="{{ route('admin.orders.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;">
            <label style="flex-grow: 1;">Cari Pesanan
                <input name="search" value="{{ request('search') }}" placeholder="Cari nomor order atau nama pembeli...">
            </label>
            
            <label style="width: 180px;">Status
                <select name="status">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (Belum Bayar)</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid (Lunas)</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped (Dikirim)</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </label>

            <button class="btn primary" type="submit" style="height: 40px; padding: 0 20px;">Terapkan Filter</button>
            <a class="btn" href="{{ route('admin.orders.index') }}" style="height: 40px; display: inline-flex; align-items: center;">Reset</a>
        </form>
    </div>

    <!-- Tabel Pesanan -->
    <div class="panel" style="padding: 0; overflow: hidden;">
        <table style="border: none; width: 100%;">
            <thead style="background: #f8fafc;">
                <tr>
                    <th style="padding: 16px;">Nomor Order</th>
                    <th style="padding: 16px;">Tanggal</th>
                    <th style="padding: 16px;">Nama Pelanggan</th>
                    <th style="padding: 16px;">Ekspedisi (Courier)</th>
                    <th style="padding: 16px; text-align: right;">Total Tagihan</th>
                    <th style="padding: 16px; text-align: center;">Status</th>
                    <th style="padding: 16px; text-align: center;">Resi</th>
                    <th style="padding: 16px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr style="border-bottom: 1px solid var(--line);">
                        <td style="padding: 16px; font-weight: bold; font-size: 14px;">
                            #{{ $order->order_number }}
                        </td>
                        <td style="padding: 16px; font-size: 13px;" class="muted">
                            {{ $order->created_at?->format('d M Y H:i') }}
                        </td>
                        <td style="padding: 16px; font-size: 13px;">
                            <strong>{{ $order->customer_name }}</strong><br>
                            <span class="muted" style="font-size: 11px;">{{ $order->customer_email }}</span>
                        </td>
                        <td style="padding: 16px; font-size: 13px;">
                            <span class="badge ok" style="font-size: 10px; background: #e0f2fe; color: #0369a1;">
                                {{ strtoupper($order->shipping_courier) }}
                            </span>
                        </td>
                        <td style="padding: 16px; text-align: right; font-weight: bold; font-size: 14px; color: var(--primary);">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td style="padding: 16px; text-align: center;">
                            <span class="badge {{ $order->status === 'paid' || $order->status === 'shipped' || $order->status === 'completed' ? 'ok' : ($order->status === 'cancelled' ? 'danger' : 'warn') }}" style="font-size: 10px;">
                                {{ strtoupper($order->status) }}
                            </span>
                        </td>
                        <td style="padding: 16px; text-align: center; font-family: monospace; font-size: 13px;" class="muted">
                            {{ $order->waybill ?: '-' }}
                        </td>
                        <td style="padding: 16px; text-align: center;">
                            <a class="btn" href="{{ route('admin.orders.show', $order) }}" style="padding: 6px 12px; font-size: 12px; min-height: auto;">Kelola</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="muted text-center" style="padding: 32px;">
                            Belum ada pesanan e-commerce masuk.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination" style="margin-top: 20px;">{{ $orders->links() }}</div>
@endsection
