@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>Notif & Komunikasi</h1>
            <p class="muted">Buat pesan untuk stok menipis, koordinasi gudang, atau pengadaan.</p>
        </div>
    </div>

    <section class="panel">
        <h2>Barang Stok Menipis</h2>
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Barang</th>
                    <th>Stok</th>
                    <th>Minimum</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lowStockItems as $item)
                    <tr>
                        <td>{{ $item->sku }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->current_stock }} {{ $item->unit }}</td>
                        <td>{{ $item->minimum_stock }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">Tidak ada stok menipis.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="panel" style="margin-top:16px">
        <h2>Buat Pesan</h2>
        <form method="POST" action="{{ route('notifikasi.store') }}">
            @csrf
            <div class="form-grid">
                <label>Judul
                    <input name="title" required>
                </label>
                <label>Penerima
                    <input name="recipient" placeholder="Tim Pengadaan / Admin / Email" required>
                </label>
                <label>Channel
                    <select name="channel" required>
                        <option value="internal">Internal</option>
                        <option value="email">Email</option>
                        <option value="whatsapp">WhatsApp</option>
                    </select>
                </label>
                <label>Status
                    <select name="status" required>
                        <option value="draft">Draft</option>
                        <option value="sent">Terkirim</option>
                    </select>
                </label>
            </div>
            <label style="margin-top:14px">Isi Pesan
                <textarea name="message" required></textarea>
            </label>
            <div class="actions-row" style="margin-top:18px">
                <button class="btn primary" type="submit">Simpan Pesan</button>
            </div>
        </form>
    </section>

    <section class="panel" style="margin-top:16px">
        <h2>Riwayat Pesan</h2>
        <table>
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Penerima</th>
                    <th>Channel</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($messages as $message)
                    <tr>
                        <td>{{ $message->title }}</td>
                        <td>{{ $message->recipient }}</td>
                        <td>{{ ucfirst($message->channel) }}</td>
                        <td>
                            <span class="badge {{ $message->status === 'sent' ? 'ok' : 'warn' }}">
                                {{ $message->status === 'sent' ? 'Terkirim' : 'Draft' }}
                            </span>
                        </td>
                        <td>{{ $message->created_at?->format('d M Y H:i') }}</td>
                        <td>
                            @if ($message->status !== 'sent')
                                <form method="POST" action="{{ route('notifikasi.status', $message) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn" type="submit">Tandai Terkirim</button>
                                </form>
                            @else
                                <span class="muted">{{ $message->sent_at?->format('d M Y H:i') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">Belum ada pesan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">{{ $messages->links() }}</div>
    </section>
@endsection
