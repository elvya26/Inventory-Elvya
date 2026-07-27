@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>Pencatatan Barang</h1>
            <p class="muted">Kelola data barang dan stok gudang.</p>
        </div>
        <a class="btn primary" href="{{ route('pencatatan.create') }}">Tambah Barang</a>
    </div>

    <form class="actions-row" method="GET" action="{{ route('pencatatan.index') }}">
        <input name="search" value="{{ request('search') }}" placeholder="Cari SKU, nama, atau kategori">
        <button class="btn" type="submit">Cari</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Barang</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Lokasi</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->sku }}</td>
                    <td>
                        <div class="item-cell">
                            @if ($item->image_path)
                                <img class="item-thumb" src="{{ $item->image_path }}" alt="Gambar {{ $item->name }}">
                            @else
                                <span class="item-thumb item-thumb--empty" aria-hidden="true">—</span>
                            @endif
                            <span>{{ $item->name }}</span>
                        </div>
                    </td>
                    <td>{{ $item->category }}</td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>{{ $item->current_stock }} {{ $item->unit }}</td>
                    <td>{{ $item->location ?: '-' }}</td>
                    <td>
                        <span class="badge {{ $item->isLowStock() ? 'warn' : 'ok' }}">
                            {{ $item->isLowStock() ? 'Menipis' : 'Aman' }}
                        </span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <a class="btn" href="{{ route('pencatatan.show', $item) }}">Detail</a>
                            <form method="POST" action="{{ route('pencatatan.destroy', $item) }}" onsubmit="return confirm('Hapus barang {{ $item->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger" type="submit">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="muted">Data barang belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination">{{ $items->links() }}</div>
@endsection
