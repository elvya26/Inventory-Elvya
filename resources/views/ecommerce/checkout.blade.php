@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>Checkout Pembelian</h1>
            <p class="muted">Lengkapi alamat pengiriman dan pilih metode pembayaran Virtual Account.</p>
        </div>
        <a class="btn" href="{{ route('ecommerce.cart') }}">← Kembali ke Keranjang</a>
    </div>

    <form method="POST" action="{{ route('ecommerce.checkout.store') }}">
        @csrf
        <div class="form-layout-container">
            <!-- Kolom Kiri: Formulir Pengiriman & Pembayaran -->
            <div class="form-column">
                <!-- Data Penerima -->
                <div class="panel" style="margin-bottom: 20px; padding: 24px;">
                    <h2 class="section-title">👤 Informasi Penerima</h2>
                    <p class="muted" style="margin-bottom: 18px; font-size: 13px;">Pastikan data kontak penerima benar untuk pengiriman.</p>

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <label>Nama Lengkap Penerima
                            <input name="customer_name" value="{{ old('customer_name', session('user_name')) }}" placeholder="Masukkan nama lengkap" required>
                        </label>
                        <div class="form-row-2">
                            <label>Alamat Email
                                <input type="email" name="customer_email" value="{{ old('customer_email', $currentUser->email ?? '') }}" placeholder="nama@email.com" required>
                            </label>
                            <label>Nomor Telepon/WhatsApp
                                <input name="customer_phone" value="{{ old('customer_phone') }}" placeholder="Contoh: 081234567890" required>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Alamat & Logistik (Kiriminaja) -->
                <div class="panel" style="margin-bottom: 20px; padding: 24px;">
                    <h2 class="section-title">🚚 Alamat Pengiriman & Kurir</h2>
                    <p class="muted" style="margin-bottom: 18px; font-size: 13px;">Tarif pengiriman dihitung otomatis menggunakan Kiriminaja API.</p>

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <label style="margin: 0; font-weight: 500;">Alamat Lengkap Tujuan</label>
                            <button type="button" id="btn-detect-location" class="btn" style="padding: 6px 12px; font-size: 12px; display: flex; align-items: center; gap: 6px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--bg); color: var(--text); cursor: pointer;" onclick="detectLocation()">
                                <iconify-icon icon="solar:gps-bold-duotone" style="font-size: 16px; color: var(--primary);"></iconify-icon>
                                <span>Gunakan Lokasi Saya</span>
                            </button>
                        </div>
                        <textarea id="shipping_address" name="shipping_address" placeholder="Tuliskan nama jalan, RT/RW, nomor rumah, kelurahan, kecamatan, kota/kabupaten, provinsi, dan kode pos." required></textarea>

                        <label>Pilihan Ekspedisi Kurir (API Kiriminaja)
                            <select name="shipping_courier" required onchange="updateTotal()">
                                @foreach ($shippingRates as $rate)
                                    <option value="{{ $rate['courier'] }}" data-cost="{{ $rate['cost'] }}">
                                        {{ $rate['name'] }} ({{ $rate['service'] }}) - Rp {{ number_format($rate['cost'], 0, ',', '.') }} [Estimasi: {{ $rate['etd'] }}]
                                    </option>
                                @endforeach
                            </select>
                        </label>
                    </div>
                </div>

                <!-- Pembayaran Virtual Account -->
                <div class="panel" style="padding: 24px;">
                    <h2 class="section-title">💳 Bank Transfer Virtual Account</h2>
                    <p class="muted" style="margin-bottom: 18px; font-size: 13px;">Pilih bank yang ingin Anda gunakan untuk pembayaran Virtual Account.</p>

                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px;">
                        <label style="border: 1px solid var(--line); border-radius: 8px; padding: 12px; display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer; text-align: center;">
                            <input type="radio" name="payment_bank" value="bca" checked style="min-height: auto;">
                            <strong>BCA</strong>
                            <span class="muted" style="font-size: 10px;">VA Otomatis</span>
                        </label>
                        <label style="border: 1px solid var(--line); border-radius: 8px; padding: 12px; display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer; text-align: center;">
                            <input type="radio" name="payment_bank" value="mandiri" style="min-height: auto;">
                            <strong>Mandiri</strong>
                            <span class="muted" style="font-size: 10px;">VA Otomatis</span>
                        </label>
                        <label style="border: 1px solid var(--line); border-radius: 8px; padding: 12px; display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer; text-align: center;">
                            <input type="radio" name="payment_bank" value="bni" style="min-height: auto;">
                            <strong>BNI</strong>
                            <span class="muted" style="font-size: 10px;">VA Otomatis</span>
                        </label>
                        <label style="border: 1px solid var(--line); border-radius: 8px; padding: 12px; display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer; text-align: center;">
                            <input type="radio" name="payment_bank" value="bri" style="min-height: auto;">
                            <strong>BRI</strong>
                            <span class="muted" style="font-size: 10px;">VA Otomatis</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Ringkasan & Submit -->
            <div class="form-column">
                <div class="panel" style="padding: 24px; display: flex; flex-direction: column; gap: 16px;">
                    <h2 class="section-title">🛒 Ringkasan Belanja</h2>
                    
                    @php $subtotal = 0; @endphp
                    <div style="display: flex; flex-direction: column; gap: 10px; max-height: 180px; overflow-y: auto; border-bottom: 1px solid var(--line); padding-bottom: 12px; margin-bottom: 8px;">
                        @foreach ($cart as $item)
                            @php $subtotal += $item['price'] * $item['quantity']; @endphp
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                                <div>
                                    <strong>{{ $item['name'] }}</strong>
                                    <span class="muted" style="display: block; font-size: 10px;">{{ $item['quantity'] }} {{ $item['unit'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                </div>
                                <strong class="muted">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</strong>
                            </div>
                        @endforeach
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 8px; font-size: 14px; border-bottom: 1px solid var(--line); padding-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span class="muted">Subtotal Produk:</span>
                            <strong id="subtotal" data-value="{{ $subtotal }}">Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span class="muted">Ongkos Kirim:</span>
                            <strong id="ongkir">Rp 0</strong>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; font-size: 16px; margin-top: 4px;">
                        <strong>Total Tagihan:</strong>
                        <strong id="total" style="color: var(--primary); font-size: 18px;">Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                    </div>

                    <button class="btn primary" type="submit" style="width: 100%; padding: 12px; justify-content: center; font-weight: bold; font-size: 15px; margin-top: 10px;">
                        Buat Pesanan & Bayar
                    </button>
                </div>
            </div>
        </div>
    </form>

    <script>
        function detectLocation() {
            var btn = document.getElementById('btn-detect-location');
            var txt = document.getElementById('shipping_address');
            var btnText = btn.querySelector('span');

            if (!navigator.geolocation) {
                alert("Geolocation tidak didukung oleh browser Anda.");
                return;
            }

            btn.disabled = true;
            btnText.innerText = "Mendeteksi...";
            
            navigator.geolocation.getCurrentPosition(function(position) {
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;
                
                fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lon)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.display_name) {
                            txt.value = data.display_name;
                        } else {
                            txt.value = lat + ", " + lon;
                        }
                        btnText.innerText = "Gunakan Lokasi Saya";
                        btn.disabled = false;
                    })
                    .catch(err => {
                        txt.value = lat + ", " + lon;
                        btnText.innerText = "Gunakan Lokasi Saya";
                        btn.disabled = false;
                    });
            }, function(error) {
                alert("Gagal mendapatkan lokasi. Pastikan izin lokasi telah diaktifkan.");
                btnText.innerText = "Gunakan Lokasi Saya";
                btn.disabled = false;
            });
        }

        function updateTotal() {
            var courierSelect = document.querySelector('select[name="shipping_courier"]');
            var selectedOption = courierSelect.options[courierSelect.selectedIndex];
            var shippingCost = parseInt(selectedOption.getAttribute('data-cost')) || 0;
            var subtotal = parseInt(document.getElementById('subtotal').getAttribute('data-value')) || 0;
            
            var total = subtotal + shippingCost;
            
            document.getElementById('ongkir').innerText = 'Rp ' + shippingCost.toLocaleString('id-ID');
            document.getElementById('total').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Run on load
        document.addEventListener('DOMContentLoaded', function() {
            updateTotal();
        });
    </script>
@endsection
