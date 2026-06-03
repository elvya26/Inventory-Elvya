@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>Laporan Inventory</h1>
            <p class="muted">
                Periode:
                {{ $from ? $from->format('d M Y') : 'Awal data' }}
                sampai
                {{ $to ? $to->format('d M Y') : 'Hari ini' }}
            </p>
        </div>
        <button class="btn primary no-print" onclick="window.print()">Print</button>
    </div>

    <section class="grid stats">
        <div class="card">
            <span class="muted">Barang Masuk</span>
            <span class="stat-number">{{ $stockIn }}</span>
        </div>
        <div class="card">
            <span class="muted">Barang Keluar</span>
            <span class="stat-number">{{ $stockOut }}</span>
        </div>
        <div class="card">
            <span class="muted">Penyesuaian</span>
            <span class="stat-number">{{ $adjustments }}</span>
        </div>
    </section>

    @include('laporan.partials.tables')
@endsection
