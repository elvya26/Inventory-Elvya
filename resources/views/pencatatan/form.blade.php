@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>{{ $item->exists ? 'Edit Barang' : 'Tambah Barang' }}</h1>
            <p class="muted">Lengkapi identitas barang dan batas minimum stok.</p>
        </div>
    </div>

<form class="panel" method="POST" action="{{ $item->exists ? route('pencatatan.update', $item) : route('pencatatan.store') }}" enctype="multipart/form-data">
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

            <label>Gambar (opsional)
                <input type="file" name="image_path" accept="image/*">
                @if ($item->image_path)
                    <small class="muted">File saat ini:</small>
                    <img style="max-width:220px; width:100%; height:auto; border-radius:8px; margin-top:6px" src="{{ $item->image_path }}" alt="Gambar {{ $item->name }}">
                @endif
            </label>

            <label>Video (opsional)
                <input type="file" name="video_path" accept="video/*">
                @if ($item->video_path)
                    <small class="muted">File saat ini:</small>
                    <video controls style="max-width:280px; width:100%; border-radius:8px; margin-top:6px">
                        <source src="{{ $item->video_path }}">
                    </video>
                @endif
            </label>
        </div>

        <div class="actions-row" style="margin-top:18px">
            <a class="btn" href="{{ route('pencatatan.index') }}">Kembali</a>
            <button class="btn primary" type="submit">Simpan</button>
        </div>
    </form>
@endsection
