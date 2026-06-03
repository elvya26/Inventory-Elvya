@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>{{ $item->exists ? 'Edit Barang' : 'Tambah Barang' }}</h1>
            <p class="muted">Lengkapi identitas barang dan batas minimum stok.</p>
        </div>
    </div>

    <form class="panel" method="POST" action="{{ $item->exists ? route('pencatatan.update', $item) : route('pencatatan.store') }}">
        @csrf
        @if ($item->exists)
            @method('PUT')
        @endif

        <div class="form-grid">
            <label>SKU
                <input name="sku" value="{{ old('sku', $item->sku) }}" required>
            </label>
            <label>Nama Barang
                <input name="name" value="{{ old('name', $item->name) }}" required>
            </label>
            <label>Kategori
                <input name="category" value="{{ old('category', $item->category) }}" required>
            </label>
            <label>Satuan
                <input name="unit" value="{{ old('unit', $item->unit) }}" required>
            </label>
            <label>Stok Saat Ini
                <input type="number" min="0" name="current_stock" value="{{ old('current_stock', $item->current_stock ?? 0) }}" required>
            </label>
            <label>Minimum Stok
                <input type="number" min="0" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock ?? 0) }}" required>
            </label>
            <label>Lokasi
                <input name="location" value="{{ old('location', $item->location) }}">
            </label>
        </div>

        <div class="actions-row" style="margin-top:18px">
            <a class="btn" href="{{ route('pencatatan.index') }}">Kembali</a>
            <button class="btn primary" type="submit">Simpan</button>
        </div>
    </form>
@endsection
