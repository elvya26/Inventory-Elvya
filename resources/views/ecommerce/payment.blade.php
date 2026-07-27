@extends('layouts.app')

@section('content')
    <div style="max-width: 680px; margin: 0 auto;">
        <div class="panel text-center" style="padding: 40px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.03);">
            <!-- Icon -->
            <div style="width: 72px; height: 72px; border-radius: 50%; background: #ecfdf5; display: inline-flex; justify-content: center; align-items: center; font-size: 32px; margin-bottom: 20px; color: #059669;">
                💳
            </div>

            <!-- Title -->
            <h1 style="font-size: 24px; margin: 0 0 8px; font-weight: 700; color: var(--ink);">Menunggu Pembayaran</h1>
            <p class="muted" style="margin: 0 0 24px; font-size: 14px;">Segera lakukan pembayaran sebelum Virtual Account Anda kedaluwarsa.</p>

            <!-- Order Info Summary -->
            <div class="panel text-center" style="background: var(--bg); border: 1px solid var(--line); border-radius: 10px; padding: 16px; margin-bottom: 24px; text-align: left; display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                <div>
                    <span class="muted" style="font-size: 11px; display: block; text-transform: uppercase; font-weight: bold; letter-spacing: 0.05em;">Nomor Pesanan</span>
                    <strong style="font-size: 15px;">#{{ $order->order_number }}</strong>
                </div>
                <div>
                    <span class="muted" style="font-size: 11px; display: block; text-transform: uppercase; font-weight: bold; letter-spacing: 0.05em;">Total Tagihan</span>
                    <strong style="font-size: 16px; color: var(--primary);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong>
                </div>
            </div>

            <!-- Virtual Account Details Card -->
            <div class="panel" style="padding: 24px; border: 2px solid var(--primary); border-radius: 12px; margin-bottom: 24px; text-align: left;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--line); padding-bottom: 12px;">
                    <span style="font-weight: bold; font-size: 15px;">Virtual Account {{ $order->payment->bank_name }}</span>
                    <span class="badge danger" style="font-size: 11px;">PENDING</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <div>
                        <span class="muted" style="font-size: 12px; display: block; margin-bottom: 4px;">Nomor Virtual Account</span>
                        <div style="display: flex; align-items: center; justify-content: space-between; background: var(--bg); border-radius: 6px; padding: 10px 14px; border: 1px solid var(--line);">
                            <strong style="font-size: 20px; font-family: monospace; letter-spacing: 0.05em; color: var(--ink);">{{ $order->payment->va_number }}</strong>
                            <button class="btn" style="min-height: auto; padding: 6px 12px; font-size: 12px;" onclick="navigator.clipboard.writeText('{{ $order->payment->va_number }}'); alert('VA disalin!')">Salin</button>
                        </div>
                    </div>

                    <div>
                        <span class="muted" style="font-size: 12px; display: block; margin-bottom: 2px;">Nama Rekening</span>
                        <strong style="font-size: 14px;">{{ config('app.name') }} - {{ $order->customer_name }}</strong>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div style="text-align: left; margin-bottom: 32px;">
                <h3 style="font-size: 15px; margin-bottom: 12px; font-weight: 700;">Petunjuk Pembayaran</h3>
                <ol style="font-size: 13px; line-height: 1.6; color: var(--muted); padding-left: 18px; margin: 0;">
                    <li>Buka aplikasi Mobile Banking, Internet Banking, atau datangi ATM bank Anda.</li>
                    <li>Pilih menu **Transfer** lalu klik **Virtual Account**.</li>
                    <li>Masukkan nomor Virtual Account **{{ $order->payment->va_number }}** di atas.</li>
                    <li>Periksa kembali rincian tagihan Anda, lalu masukkan PIN bank untuk konfirmasi.</li>
                    <li>Pesanan Anda akan terverifikasi lunas secara otomatis dalam beberapa detik.</li>
                </ol>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; flex-direction: column; gap: 10px;">
                @if(isset($dokuUrl) && $dokuUrl)
                    <button id="doku-checkout-button" class="btn" style="padding: 12px; justify-content: center; font-weight: bold; font-size: 14px; background: #e11d48; color: white; border-color: #e11d48; display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <iconify-icon icon="solar:card-2-bold-duotone" style="font-size: 20px;"></iconify-icon>
                        Bayar Instan dengan DOKU Checkout (Jokul)
                    </button>
                @endif
                <a class="btn primary" href="{{ route('bank.cek_va', ['va_number' => $order->payment->va_number]) }}" target="_blank" style="padding: 12px; justify-content: center; font-weight: bold; font-size: 14px;">
                    🏧 Buka Portal Bank / Cek VA Bank
                </a>
                <a class="btn" href="{{ route('ecommerce.customer') }}" style="padding: 12px; justify-content: center; font-size: 13px;">
                    Periksa Riwayat Pembelian Saya
                </a>
            </div>
        </div>
    </div>

    <!-- DOKU Checkout JS SDK (Sandbox) -->
    <script src="https://sandbox.doku.com/jokul-checkout-js/v1/jokul-checkout-1.0.0.js"></script>

    <script>
        @if(isset($dokuUrl) && $dokuUrl)
        document.getElementById('doku-checkout-button').addEventListener('click', function () {
            loadJokulCheckout('{{ $dokuUrl }}');
        });
        @endif

        // Polling status pembayaran setiap 2.5 detik
        setInterval(function() {
            fetch("{{ route('ecommerce.payment.status', $order) }}")
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'paid' || data.status === 'shipped' || data.status === 'completed') {
                        window.location.href = "{{ route('ecommerce.customer') }}?paid=1";
                    }
                })
                .catch(err => console.error("Error polling payment status:", err));
        }, 2500);
    </script>
@endsection
