@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>Dashboard Inventory</h1>
            <p class="muted">Service aktif: {{ $serviceName }}</p>
        </div>
        <a class="btn primary" href="{{ route('pencatatan.create') }}">Tambah Barang</a>
    </div>

    <section class="grid stats">
        <div class="card">
            <span class="muted">Total Barang</span>
            <span class="stat-number">{{ $itemsCount }}</span>
        </div>
        <div class="card">
            <span class="muted">Stok Menipis</span>
            <span class="stat-number">{{ $lowStockCount }}</span>
        </div>
        <div class="card">
            <span class="muted">Mutasi Stok</span>
            <span class="stat-number">{{ $movementCount }}</span>
        </div>
        <div class="card">
            <span class="muted">Draft Notifikasi</span>
            <span class="stat-number">{{ $pendingNotifications }}</span>
        </div>
    </section>

    <section class="panel" style="margin-top:16px">
        <h2>Mutasi Terbaru</h2>
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Barang</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentMovements as $movement)
                    <tr>
                        <td>{{ $movement->occurred_at?->format('d M Y H:i') }}</td>
                        <td>{{ $movement->item?->name }}</td>
                        <td>{{ ucfirst($movement->type) }}</td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ $movement->note ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">Belum ada mutasi stok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
