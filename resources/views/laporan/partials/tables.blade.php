<section class="panel" style="margin-top:16px">
    <h2>Ringkasan Stok Barang</h2>
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Barang</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Minimum</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->sku }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category }}</td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>{{ $item->current_stock }} {{ $item->unit }}</td>
                    <td>{{ $item->minimum_stock }}</td>
                    <td>{{ $item->isLowStock() ? 'Menipis' : 'Aman' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="muted">Belum ada data barang.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>

<section class="panel" style="margin-top:16px">
    <h2>Riwayat Mutasi</h2>
    <table>
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Barang</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Petugas</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movements as $movement)
                <tr>
                    <td>{{ $movement->occurred_at?->format('d M Y H:i') }}</td>
                    <td>{{ $movement->item?->name }}</td>
                    <td>{{ ucfirst($movement->type) }}</td>
                    <td>{{ $movement->quantity }}</td>
                    <td>{{ $movement->actor ?: '-' }}</td>
                    <td>{{ $movement->note ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="muted">Belum ada mutasi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>
