@extends('layouts.app')

@section('content')
<div class="card" style="max-width: 700px; margin: 0 auto;">
    <div class="card-header" style="display: flex; align-items: center; gap: 10px;">
        <iconify-icon icon="solar:delivery-bold-duotone" style="font-size: 28px; color: var(--primary);"></iconify-icon>
        <h2 style="margin: 0; font-size: 20px;">Lacak Pengiriman Pesanan</h2>
    </div>

    <div class="card-body">
        <form action="{{ route('ecommerce.track') }}" method="GET" style="margin-bottom: 25px;">
            <div style="display: flex; flex-direction: column; gap: 8px;">
                <label for="waybill" style="font-weight: 500; font-size: 14px;">Masukkan No. Resi (Waybill) atau No. Invoice Anda</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="waybill" name="waybill" placeholder="Contoh: INV-2026-0001 atau RESI123456" 
                           value="{{ $waybill ?? '' }}" required
                           style="flex: 1; padding: 10px 12px; border: 1px solid var(--border); border-radius: var(--radius); font-size: 14px; background: var(--bg); color: var(--text);">
                    <button type="submit" class="btn primary" style="display: flex; align-items: center; gap: 6px;">
                        <iconify-icon icon="solar:magnifer-bold-duotone" style="font-size: 18px;"></iconify-icon>
                        Lacak
                    </button>
                </div>
            </div>
        </form>

        @if ($waybill)
            @if ($order)
                <!-- Order Card Details -->
                <div style="background: var(--bg-hover); padding: 15px; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 25px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; font-size: 14px;">
                        <div>
                            <span style="color: var(--muted); display: block; font-size: 12px;">No. Invoice</span>
                            <strong>{{ $order->order_number }}</strong>
                        </div>
                        <div>
                            <span style="color: var(--muted); display: block; font-size: 12px;">Penerima</span>
                            <strong>{{ $order->customer_name }}</strong>
                        </div>
                        <div>
                            <span style="color: var(--muted); display: block; font-size: 12px;">No. Resi (Waybill)</span>
                            <strong>{{ $order->waybill ?? 'Belum Di-booking' }}</strong>
                        </div>
                        <div>
                            <span style="color: var(--muted); display: block; font-size: 12px;">Status Pembayaran</span>
                            <span class="badge {{ in_array($order->status, ['paid', 'shipped', 'completed']) ? 'success' : 'warning' }}" style="font-size: 11px;">
                                {{ in_array($order->status, ['paid', 'shipped', 'completed']) ? 'PAID' : 'PENDING' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if ($trackingResult && isset($trackingResult['history']) && count($trackingResult['history']) > 0)
                    <!-- Timeline -->
                    <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 15px;">Riwayat Pengiriman</h3>
                    <div style="position: relative; padding-left: 25px; border-left: 2px solid var(--border); margin-left: 10px;">
                        @foreach ($trackingResult['history'] as $idx => $history)
                            <div style="position: relative; margin-bottom: 25px;">
                                <!-- Timeline Point -->
                                <div style="position: absolute; left: -31px; top: 3px; width: 10px; height: 10px; border-radius: 50%; background: {{ $idx === 0 ? 'var(--primary)' : 'var(--border)' }}; border: 2px solid var(--bg);"></div>
                                
                                <div style="font-size: 14px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <strong style="color: {{ $idx === 0 ? 'var(--primary)' : 'var(--text)' }}">{{ $history['status'] ?? 'Transit' }}</strong>
                                        <span style="font-size: 11px; color: var(--muted);">{{ $history['date'] ?? '' }}</span>
                                    </div>
                                    @if (isset($history['note']) && $history['note'])
                                        <p style="margin: 4px 0 0 0; color: var(--muted); font-size: 13px;">{{ $history['note'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align: center; padding: 25px; border: 1px dashed var(--border); border-radius: var(--radius); color: var(--muted);">
                        <iconify-icon icon="solar:info-circle-bold-duotone" style="font-size: 40px; margin-bottom: 10px; display: inline-block;"></iconify-icon>
                        <p style="margin: 0; font-size: 14px;">Nomor resi terdaftar tetapi belum memiliki riwayat pengiriman di kurir.</p>
                    </div>
                @endif
            @else
                <div class="errors" style="margin: 0;">
                    <strong>Pesanan tidak ditemukan:</strong> Nomor resi atau invoice "{{ $waybill }}" tidak terdaftar dalam database kami.
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
