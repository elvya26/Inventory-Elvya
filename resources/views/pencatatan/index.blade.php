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
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category }}</td>
                    <td>{{ $item->current_stock }} {{ $item->unit }}</td>
                    <td>{{ $item->location ?: '-' }}</td>
                    <td>
                        <span class="badge {{ $item->isLowStock() ? 'warn' : 'ok' }}">
                            {{ $item->isLowStock() ? 'Menipis' : 'Aman' }}
                        </span>
                    </td>
                    <td>
                        <a class="btn" href="{{ route('pencatatan.show', $item) }}">Detail</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="muted">Data barang belum tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination">{{ $items->links() }}</div>
@endsection
