@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>Dashboard Inventory & E-Commerce</h1>
            <p class="muted">Service aktif: {{ $serviceName }} · Kelola stok gudang dan transaksi toko online secara terpadu.</p>
        </div>
        <a class="btn primary" href="{{ route('pencatatan.create') }}">Tambah Barang</a>
    </div>

    <!-- Grid Metrik Statistik -->
    <section class="grid stats" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
        <div class="card">
            <span class="muted">Total Barang</span>
            <span class="stat-number">{{ $itemsCount }}</span>
        </div>
        <div class="card">
            <span class="muted">Stok Menipis</span>
            <span class="stat-number" style="color: {{ $lowStockCount > 0 ? 'var(--danger)' : 'var(--ink)' }}">{{ $lowStockCount }}</span>
        </div>
        <div class="card">
            <span class="muted">Total Transaksi Belanja</span>
            <span class="stat-number">{{ $ordersCount }}</span>
        </div>
        <div class="card" style="border-left: 4px solid var(--primary);">
            <span class="muted">Omzet Penjualan (Paid)</span>
            <span class="stat-number" style="color: var(--primary);">Rp {{ number_format($revenue, 0, ',', '.') }}</span>
        </div>
    </section>

    <!-- Ringkasan Logistik & Mutasi Stok -->
    <div class="grid" style="grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; align-items: start;">
        <!-- Panel 1: Mutasi Terbaru -->
        <section class="panel">
            <h2>Mutasi Stok Gudang</h2>
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Barang</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentMovements as $movement)
                        <tr>
                            <td>{{ $movement->occurred_at?->format('d M Y H:i') }}</td>
                            <td>{{ $movement->item?->name }}</td>
                            <td>
                                <span class="badge {{ $movement->type === 'masuk' ? 'ok' : ($movement->type === 'keluar' ? 'danger' : 'warn') }}" style="font-size: 10px;">
                                    {{ ucfirst($movement->type) }}
                                </span>
                            </td>
                            <td>{{ $movement->quantity }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="muted text-center">Belum ada mutasi stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <!-- Panel 2: Pesanan E-Commerce Terbaru -->
        <section class="panel">
            <h2>Pesanan E-Commerce Baru</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" style="font-weight: bold; color: var(--primary);">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <strong>{{ $order->customer_name }}</strong>
                            </td>
                            <td style="font-weight: 500;">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td>
                                <span class="badge {{ $order->status === 'paid' || $order->status === 'shipped' || $order->status === 'completed' ? 'ok' : ($order->status === 'cancelled' ? 'danger' : 'warn') }}" style="font-size: 10px;">
                                    {{ strtoupper($order->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="muted text-center">Belum ada pesanan belanja masuk.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </div>
@endsection
