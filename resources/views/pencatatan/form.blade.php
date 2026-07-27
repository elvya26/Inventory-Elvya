@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>{{ $item->exists ? 'Edit Barang' : 'Tambah Barang Baru' }}</h1>
            <p class="muted">Isi detail informasi barang, harga, dan level stok minimum untuk notifikasi otomatis.</p>
        </div>
    </div>

    <form method="POST" action="{{ $item->exists ? route('pencatatan.update', $item) : route('pencatatan.store') }}" enctype="multipart/form-data">
        @csrf
        @if ($item->exists)
            @method('PUT')
        @endif

        <div class="form-layout-container">
            <!-- Kolom Kiri: Detail Informasi -->
            <div class="form-column">
                <!-- Section 1: Informasi Dasar -->
                <div class="panel" style="margin-bottom: 20px;">
                    <h2 class="section-title">📦 Informasi Dasar</h2>
                    <p class="muted" style="margin-bottom: 18px; font-size: 13px;">Identitas utama barang di dalam sistem inventory.</p>

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div class="form-row-2">
                            <label>SKU / Kode Barang
                                <input name="sku" value="{{ old('sku', $item->sku) }}" placeholder="Contoh: BRG-001" required>
                            </label>
                            <label>Nama Barang
                                <input name="name" value="{{ old('name', $item->name) }}" placeholder="Contoh: Kertas A4" required>
                            </label>
                        </div>

                        <div class="form-row-2">
                            <label>Kategori
                                <input name="category" value="{{ old('category', $item->category) }}" placeholder="Contoh: ATK / Elektronik" required>
                            </label>
                            <label>Satuan
                                <input name="unit" value="{{ old('unit', $item->unit) }}" placeholder="Contoh: Rim / Pcs / Box" required>
                            </label>
                        </div>

                        <label>Harga Barang (Rp)
                            <div class="input-addon-group">
                                <span class="input-addon">IDR</span>
                                <input type="number" min="0" step="any" name="price" value="{{ old('price', $item->price ?? 0) }}" placeholder="Contoh: 50000" required>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Section 2: Stok dan Lokasi -->
                <div class="panel">
                    <h2 class="section-title">📊 Inventori & Lokasi</h2>
                    <p class="muted" style="margin-bottom: 18px; font-size: 13px;">Atur stok awal, batas menipis, dan letak penyimpanan barang.</p>

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div class="form-row-3">
                            <label>Stok Saat Ini
                                <input type="number" min="0" name="current_stock" value="{{ old('current_stock', $item->current_stock ?? 0) }}" required>
                            </label>
                            <label>Minimum Stok (Batas Alert)
                                <input type="number" min="0" name="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock ?? 0) }}" required>
                            </label>
                            <label>Lokasi Penyimpanan
                                <input name="location" value="{{ old('location', $item->location) }}" placeholder="Contoh: Rak A-2">
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Media/Lampiran -->
            <div class="form-column">
                <div class="panel h-full" style="display: flex; flex-direction: column; gap: 16px;">
                    <h2 class="section-title">📷 Media Lampiran</h2>
                    <p class="muted" style="font-size: 13px; margin-bottom: 8px;">Tambahkan gambar atau video produk (opsional).</p>

                    <label class="file-upload-card">
                        <span>Gambar Barang</span>
                        <input type="file" name="image_path" accept="image/*" style="min-height: auto;">
                        @if ($item->image_path)
                            <div style="margin-top: 10px; text-align: center;">
                                <img src="{{ $item->image_path }}" alt="Gambar {{ $item->name }}" style="max-width: 100%; max-height: 180px; border-radius: 8px; border: 1px solid var(--line);">
                            </div>
                        @endif
                    </label>

                    <label class="file-upload-card">
                        <span>Video Barang</span>
                        <input type="file" name="video_path" accept="video/*" style="min-height: auto;">
                        @if ($item->video_path)
                            <div style="margin-top: 10px;">
                                <video controls style="width: 100%; max-height: 160px; border-radius: 8px;">
                                    <source src="{{ $item->video_path }}">
                                </video>
                            </div>
                        @endif
                    </label>
                </div>
            </div>
        </div>

        <div class="form-actions-bar panel no-print" style="margin-top: 20px; display: flex; justify-content: space-between; align-items: center; padding: 16px 24px;">
            <a class="btn" href="{{ route('pencatatan.index') }}">Batal</a>
            <button class="btn primary" type="submit" style="padding: 10px 32px; font-weight: bold;">{{ $item->exists ? 'Simpan Perubahan' : 'Tambah Barang' }}</button>
        </div>
    </form>
@endsection
