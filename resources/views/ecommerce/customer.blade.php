@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>Portal Akun Pelanggan</h1>
            <p class="muted">Pantau status pesanan, lacak kiriman, dan kelola dokumen identitas Anda.</p>
        </div>
        <a class="btn" href="{{ route('ecommerce.index') }}">← Kembali Belanja</a>
    </div>

    @if (request()->has('paid'))
        <div class="alert" style="background: #dcfce7; border-color: #bbf7d0; color: #15803d; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; font-size: 14px;">
            <iconify-icon icon="solar:check-circle-bold-duotone" style="font-size: 22px; color: #16a34a;"></iconify-icon>
            <span><strong>Pembayaran Sukses!</strong> Virtual Account Anda berhasil dilunasi. Barang belanjaan Anda saat ini telah otomatis diproses untuk dikirim (SHIPPED).</span>
        </div>
    @endif

    <div class="grid" style="grid-template-columns: 1fr 2.2fr; gap: 24px; align-items: start;">
        <!-- Area Kiri: Profil & Upload Berkas Min.io -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Profil Card -->
            <div class="panel text-center" style="padding: 24px;">
                @if ($currentUser->avatar)
                    <img src="{{ $currentUser->avatar }}" alt="{{ $currentUser->name }}" style="width: 80px; height: 80px; border-radius: 50%; border: 3px solid var(--line); object-fit: cover; margin-bottom: 12px;">
                @else
                    <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--bg); display: inline-flex; justify-content: center; align-items: center; font-size: 28px; color: var(--muted); border: 3px solid var(--line); margin-bottom: 12px;">
                        {{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}
                    </div>
                @endif
                <h3 style="margin: 0 0 4px; font-size: 18px;">{{ $currentUser->name }}</h3>
                <p class="muted" style="margin: 0; font-size: 13px;">{{ $currentUser->email }}</p>
                <span class="badge ok" style="margin-top: 10px; font-size: 10px;">{{ strtoupper($currentUser->role) }}</span>
            </div>

            <!-- Upload Min.io -->
            <div class="panel" style="padding: 24px;">
                <h3 style="font-size: 15px; margin-bottom: 6px; font-weight: 700;">📂 Dokumen Verifikasi</h3>
                <p class="muted" style="font-size: 12px; margin-bottom: 16px;">Unggah foto KTP/Izin Usaha untuk melengkapi basis data pelanggan kami (Min.io Cloud Store).</p>

                @if ($crmCustomer && $crmCustomer->document_path)
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px; display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 20px;">📄</span>
                            <div>
                                <strong style="font-size: 12px; color: #166534; display: block;">Dokumen Terunggah</strong>
                                <span class="muted" style="font-size: 10px;">Penyimpanan: {{ str_starts_with($crmCustomer->document_path, 's3://') ? 'Min.io S3 Cloud' : 'Lokal (Fallback)' }}</span>
                            </div>
                        </div>
                        <a href="{{ $crmCustomer->document_path }}" target="_blank" class="btn" style="min-height: auto; padding: 6px 12px; font-size: 11px; justify-content: center;">Lihat File</a>
                    </div>
                @endif

                <form method="POST" action="{{ route('ecommerce.customer.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <input type="file" name="document" accept=".jpg,.jpeg,.png,.pdf" required style="min-height: auto; font-size: 12px; padding: 6px;">
                        <button class="btn primary" type="submit" style="font-size: 12px; padding: 8px 12px; justify-content: center;">Unggah Berkas</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Area Kanan: Riwayat Order & Kiriminaja Tracking -->
        <div class="panel" style="padding: 28px;">
            <h2 style="font-size: 18px; margin-bottom: 16px; font-weight: 700;">Riwayat Belanja Saya</h2>

            @forelse ($orders as $order)
                <div style="border: 1px solid var(--line); border-radius: 10px; padding: 18px; margin-bottom: 16px;">
                    <!-- Order Header Info -->
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--line); padding-bottom: 12px; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <strong style="font-size: 14px; color: var(--ink);">#{{ $order->order_number }}</strong>
                            <span class="muted" style="font-size: 11px; margin-left: 8px;">{{ $order->created_at?->format('d M Y H:i') }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span class="badge {{ $order->status === 'paid' || $order->status === 'shipped' || $order->status === 'completed' ? 'ok' : ($order->status === 'cancelled' ? 'danger' : 'warn') }}" style="font-size: 10px;">
                                {{ strtoupper($order->status) }}
                            </span>
                            @if ($order->status === 'pending')
                                <a href="{{ route('ecommerce.payment', $order) }}" class="btn" style="min-height: auto; padding: 4px 10px; font-size: 11px;">Bayar Sekarang</a>
                            @endif
                        </div>
                    </div>

                    <!-- Items Summary -->
                    <div style="margin-bottom: 12px;">
                        @foreach ($order->items as $item)
                            <div style="font-size: 13px; display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <span class="muted">{{ $item->item?->name ?? 'Produk dihapus' }} ({{ $item->quantity }}x)</span>
                                <strong>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</strong>
                            </div>
                        @endforeach
                        <div style="font-size: 13px; display: flex; justify-content: space-between; margin-top: 8px; border-top: 1px dashed var(--line); padding-top: 8px;">
                            <span class="muted">Ongkir ({{ strtoupper($order->shipping_courier) }}):</span>
                            <strong>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</strong>
                        </div>
                        <div style="font-size: 14px; display: flex; justify-content: space-between; margin-top: 4px;">
                            <strong>Total Bayar:</strong>
                            <strong style="color: var(--primary);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <!-- Kiriminaja Tracking Info widget -->
                    @if ($order->waybill && isset($trackingData[$order->id]))
                        @php $track = $trackingData[$order->id]; @endphp
                        <div style="background: var(--bg); border: 1px solid var(--line); border-radius: 8px; padding: 14px; margin-top: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--line); padding-bottom: 8px; margin-bottom: 8px;">
                                <span style="font-size: 12px; font-weight: bold; color: var(--primary);">📦 Resi Kiriminaja: {{ $order->waybill }}</span>
                                <span class="badge ok" style="font-size: 9px;">{{ $track['status'] ?? 'Dalam Proses' }}</span>
                            </div>
                            
                            <!-- Tracking Timeline -->
                            <div style="display: flex; flex-direction: column; gap: 10px; max-height: 120px; overflow-y: auto; padding-left: 6px;">
                                @foreach ($track['history'] as $history)
                                    <div style="position: relative; padding-left: 16px; border-left: 2px solid var(--primary); font-size: 11px; line-height: 1.4;">
                                        <div style="position: absolute; left: -5px; top: 3px; width: 8px; height: 8px; border-radius: 50%; background: var(--primary);"></div>
                                        <span class="muted" style="display: block; font-size: 10px; font-weight: bold;">{{ $history['time'] }}</span>
                                        <span>{{ $history['description'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="muted text-center" style="padding: 32px 0;">
                    Belum ada riwayat pesanan.
                </div>
            @endforelse
        </div>
    </div>
@endsection
