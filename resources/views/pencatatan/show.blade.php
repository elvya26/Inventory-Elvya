@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>{{ $item->name }}</h1>
            <p class="muted">{{ $item->sku }} · {{ $item->category }} · {{ $item->location ?: 'Tanpa lokasi' }}</p>
        </div>
        <div class="actions-row">
            <a class="btn" href="{{ route('pencatatan.edit', $item) }}">Edit</a>
            <form method="POST" action="{{ route('pencatatan.destroy', $item) }}" onsubmit="return confirm('Hapus barang ini?')">
                @csrf
                @method('DELETE')
                <button class="btn" type="submit">Hapus</button>
            </form>
        </div>
    </div>

    <section class="grid stats">
        <div class="card">
            <span class="muted">Stok Saat Ini</span>
            <span class="stat-number">{{ $item->current_stock }} {{ $item->unit }}</span>
        </div>
        <div class="card">
            <span class="muted">Minimum Stok</span>
            <span class="stat-number">{{ $item->minimum_stock }}</span>
        </div>
        <div class="card">
            <span class="muted">Status</span>
            <span class="stat-number">{{ $item->isLowStock() ? 'Menipis' : 'Aman' }}</span>
        </div>
    </section>

    <section class="panel" style="margin-top:16px">
        <h2>Tambah Mutasi</h2>
        <form method="POST" action="{{ route('pencatatan.mutasi', $item) }}">
            @csrf
            <div class="form-grid">
                <label>Tipe
                    <select name="type" required>
                        <option value="masuk">Barang Masuk</option>
                        <option value="keluar">Barang Keluar</option>
                        <option value="penyesuaian">Penyesuaian Stok</option>
                    </select>
                </label>
                <label>Jumlah
                    <input type="number" min="1" name="quantity" required>
                </label>
                <label>Petugas
                    <input name="actor" placeholder="Nama petugas">
                </label>
                <label>Tanggal
                    <input type="datetime-local" name="occurred_at">
                </label>
            </div>
            <label style="margin-top:14px">Catatan
                <textarea name="note"></textarea>
            </label>
            <div class="actions-row" style="margin-top:18px">
                <a class="btn" href="{{ route('pencatatan.index') }}">Kembali</a>
                <button class="btn primary" type="submit">Catat Mutasi</button>
            </div>
        </form>
    </section>

    <section class="panel" style="margin-top:16px">
        <h2>Riwayat Mutasi</h2>
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Petugas</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($item->movements as $movement)
                    <tr>
                        <td>{{ $movement->occurred_at?->format('d M Y H:i') }}</td>
                        <td>{{ ucfirst($movement->type) }}</td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ $movement->actor ?: '-' }}</td>
                        <td>{{ $movement->note ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">Belum ada mutasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection
