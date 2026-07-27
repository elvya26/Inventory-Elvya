@extends('layouts.app')

@section('content')
    <div style="max-width: 600px; margin: 0 auto;">
        <div class="panel" style="padding: 32px; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.03);">
            <div class="text-center" style="margin-bottom: 24px;">
                <span style="font-size: 48px;">🏧</span>
                <h1 style="font-size: 24px; margin: 10px 0 4px; font-weight: 700;">Simulasi Terminal Cek VA Bank</h1>
                <p class="muted" style="font-size: 13px;">Gunakan halaman ini untuk memverifikasi pembayaran Virtual Account (Simulasi Bank Core).</p>
            </div>

            <!-- Form Cari VA -->
            <form method="GET" action="{{ route('bank.cek_va.search') }}" style="margin-bottom: 24px;">
                <label>Nomor Virtual Account (VA)
                    <div style="display: flex; gap: 8px;">
                        <input name="va_number" value="{{ $vaNumber ?? '' }}" placeholder="Masukkan 15 digit nomor VA" required style="font-family: monospace; font-size: 16px; letter-spacing: 0.05em; flex-grow: 1;">
                        <button class="btn primary" type="submit" style="padding: 0 20px;">Cari VA</button>
                    </div>
                </label>
            </form>

            @if (isset($payment))
                <!-- Rincian VA Ditemukan -->
                <div style="border: 1px solid var(--line); border-radius: 12px; padding: 20px; background: var(--bg); display: flex; flex-direction: column; gap: 14px; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--line); padding-bottom: 10px;">
                        <strong style="font-size: 15px;">Rincian Virtual Account</strong>
                        <span class="badge {{ $payment->status === 'completed' ? 'ok' : 'warn' }}">
                            {{ $payment->status === 'completed' ? 'LUNAS (Paid)' : 'BELUM DIBAYAR (Pending)' }}
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px; line-height: 1.5;">
                        <div>
                            <span class="muted" style="display: block;">Bank</span>
                            <strong>{{ $payment->bank_name }}</strong>
                        </div>
                        <div>
                            <span class="muted" style="display: block;">Nomor VA</span>
                            <strong style="font-family: monospace; font-size: 14px;">{{ $payment->va_number }}</strong>
                        </div>
                        <div>
                            <span class="muted" style="display: block;">Nomor Order</span>
                            <strong>#{{ $payment->order->order_number }}</strong>
                        </div>
                        <div>
                            <span class="muted" style="display: block;">Nama Pelanggan</span>
                            <strong>{{ $payment->order->customer_name }}</strong>
                        </div>
                        <div style="grid-column: 1 / -1; border-top: 1px dashed var(--line); padding-top: 10px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span class="muted" style="display: block; font-size: 11px;">Total Pembayaran</span>
                                <strong style="color: var(--primary); font-size: 18px;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</strong>
                            </div>
                            @if ($payment->status === 'completed')
                                <div style="text-align: right;">
                                    <span class="muted" style="display: block; font-size: 11px;">Tanggal Lunas</span>
                                    <strong>{{ $payment->paid_at?->format('d M Y H:i') }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Items Summary -->
                    <div style="border-top: 1px solid var(--line); padding-top: 10px; font-size: 12px;">
                        <span class="muted" style="display: block; margin-bottom: 6px;">Detail Barang Pembelian:</span>
                        <ul style="padding-left: 18px; margin: 0; display: flex; flex-direction: column; gap: 4px;">
                            @foreach ($payment->order->items as $orderItem)
                                <li>{{ $orderItem->item?->name ?? 'Produk dihapus' }} ({{ $orderItem->quantity }} {{ $orderItem->item?->unit ?? 'pcs' }})</li>
                            @endforeach
                        </ul>
                    </div>

                    @if ($payment->status !== 'completed')
                        <!-- Button Simulasi Bayar Lunas -->
                        <form method="POST" action="{{ route('bank.cek_va.pay', $payment) }}" style="margin-top: 10px;">
                            @csrf
                            <button class="btn primary" type="submit" style="width: 100%; padding: 12px; justify-content: center; font-weight: bold; background: #059669; border-color: #059669;">
                                ✅ Simulasikan Bayar Lunas (Transfer Berhasil)
                            </button>
                        </form>
                    @endif
                </div>
            @elseif (isset($vaNumber))
                <!-- VA Tidak Ditemukan -->
                <div class="panel text-center" style="background: #fef2f2; border: 1px solid #fecaca; color: var(--danger); padding: 20px; border-radius: 12px;">
                    <strong>Virtual Account Tidak Ditemukan</strong>
                    <p style="margin: 6px 0 0; font-size: 13px; opacity: 0.95;">Nomor Virtual Account "{{ $vaNumber }}" tidak terdaftar dalam transaksi pemesanan manapun. Pastikan format nomor yang disalin sudah benar.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
