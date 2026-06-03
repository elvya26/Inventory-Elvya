@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>Cetak Laporan</h1>
            <p class="muted">Rekap stok dan mutasi barang berdasarkan periode.</p>
        </div>
        <a class="btn primary" href="{{ route('laporan.print', request()->query()) }}" target="_blank">Cetak</a>
    </div>

    <form class="panel no-print" method="GET" action="{{ route('laporan.index') }}">
        <div class="form-grid">
            <label>Dari Tanggal
                <input type="date" name="from" value="{{ request('from') }}">
            </label>
            <label>Sampai Tanggal
                <input type="date" name="to" value="{{ request('to') }}">
            </label>
        </div>
        <div class="actions-row" style="margin-top:18px">
            <a class="btn" href="{{ route('laporan.index') }}">Reset</a>
            <button class="btn primary" type="submit">Tampilkan</button>
        </div>
    </form>

    <section class="grid stats" style="margin-top:16px">
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
