@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1 style="display: flex; align-items: center; gap: 10px;">
                <iconify-icon icon="solar:users-group-two-rounded-bold-duotone" style="font-size: 32px; color: var(--primary);"></iconify-icon>
                CRM & Database Pelanggan
            </h1>
            <p class="muted">Kelola klasifikasi leads, pantau histori RFM (Recency, Frequency, Monetary), dan follow-up via Whatsapp secara langsung.</p>
        </div>
    </div>

    <!-- Dashboard Widget CRM -->
    <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div class="panel" style="padding: 20px; display: flex; align-items: center; gap: 15px; border-left: 4px solid #64748b;">
            <div style="background: #f1f5f9; padding: 10px; border-radius: var(--radius); display: flex;">
                <iconify-icon icon="solar:user-broken" style="font-size: 28px; color: #64748b;"></iconify-icon>
            </div>
            <div>
                <span class="muted" style="display: block; font-size: 12px; font-weight: 500;">Pendaftaran (Leads)</span>
                <strong style="font-size: 22px; color: var(--text);">{{ $totalLeads }}</strong>
            </div>
        </div>

        <div class="panel" style="padding: 20px; display: flex; align-items: center; gap: 15px; border-left: 4px solid var(--primary);">
            <div style="background: var(--primary-light); padding: 10px; border-radius: var(--radius); display: flex;">
                <iconify-icon icon="solar:user-circle-bold-duotone" style="font-size: 28px; color: var(--primary);"></iconify-icon>
            </div>
            <div>
                <span class="muted" style="display: block; font-size: 12px; font-weight: 500;">Pelanggan Aktif</span>
                <strong style="font-size: 22px; color: var(--text);">{{ $totalActive }}</strong>
            </div>
        </div>

        <div class="panel" style="padding: 20px; display: flex; align-items: center; gap: 15px; border-left: 4px solid #d97706;">
            <div style="background: #fef3c7; padding: 10px; border-radius: var(--radius); display: flex;">
                <iconify-icon icon="solar:medal-star-bold-duotone" style="font-size: 28px; color: #d97706;"></iconify-icon>
            </div>
            <div>
                <span class="muted" style="display: block; font-size: 12px; font-weight: 500;">VIP Gold Customer</span>
                <strong style="font-size: 22px; color: var(--text);">{{ $totalVip }}</strong>
            </div>
        </div>

        <div class="panel" style="padding: 20px; display: flex; align-items: center; gap: 15px; border-left: 4px solid #16a34a;">
            <div style="background: #dcfce7; padding: 10px; border-radius: var(--radius); display: flex;">
                <iconify-icon icon="solar:double-alt-arrow-up-bold-duotone" style="font-size: 28px; color: #16a34a;"></iconify-icon>
            </div>
            <div>
                <span class="muted" style="display: block; font-size: 12px; font-weight: 500;">Customer LTV Revenue</span>
                <strong style="font-size: 22px; color: var(--text);">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    <!-- Pencarian -->
    <div class="panel no-print" style="margin-bottom: 24px; padding: 18px;">
        <form method="GET" action="{{ route('admin.crm.index') }}" style="display: flex; gap: 12px;">
            <div style="position: relative; flex-grow: 1;">
                <input name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau nomor WhatsApp pelanggan..." style="width: 100%; padding-left: 36px;">
                <iconify-icon icon="solar:magnifer-linear" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 18px;"></iconify-icon>
            </div>
            <button class="btn primary" type="submit" style="padding: 0 24px;">Cari Pelanggan</button>
            <a class="btn" href="{{ route('admin.crm.index') }}">Reset</a>
        </form>
    </div>

    <!-- Daftar Customer CRM -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        @forelse ($customers as $customer)
            @php
                $seg = $customer->segment;
                $badgeClass = 'muted';
                $badgeText = 'Lead (Belum Belanja)';
                $badgeColor = '#64748b';
                $badgeBg = '#f1f5f9';

                if ($seg === 'VIP') {
                    $badgeText = 'VIP Gold';
                    $badgeColor = '#d97706';
                    $badgeBg = '#fef3c7';
                } elseif ($seg === 'Churn Alert') {
                    $badgeText = 'Churn Alert (Inactive)';
                    $badgeColor = '#dc2626';
                    $badgeBg = '#fee2e2';
                } elseif ($seg === 'Active') {
                    $badgeText = 'Active Customer';
                    $badgeColor = 'var(--primary)';
                    $badgeBg = 'var(--primary-light)';
                }
            @endphp
            <div class="panel" style="padding: 24px; border-radius: 12px; display: grid; grid-template-columns: 1.2fr 1.5fr 1.3fr; gap: 24px; align-items: start;">
                <!-- Kolom 1: Profil Pelanggan -->
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 4px;">
                            <h2 style="font-size: 18px; margin: 0; font-weight: 700; color: var(--text);">{{ $customer->name }}</h2>
                            <span style="font-size: 11px; padding: 2px 8px; border-radius: 20px; font-weight: 600; color: {{ $badgeColor }}; background: {{ $badgeBg }}; border: 1px solid {{ $badgeColor }}20;">
                                {{ $badgeText }}
                            </span>
                        </div>
                        <span class="muted" style="font-size: 13px;">{{ $customer->email }}</span>
                    </div>

                    <div style="font-size: 13px; line-height: 1.5;">
                        <span class="muted" style="display: block; font-size: 11px;">Nomor Telepon:</span>
                        <strong>{{ $customer->phone ?: '-' }}</strong>
                    </div>

                    <!-- Min.io Verification Doc Link -->
                    <div>
                        <span class="muted" style="display: block; font-size: 11px; margin-bottom: 6px;">Berkas Verifikasi (Min.io):</span>
                        @if ($customer->document_url)
                            <a href="{{ $customer->document_url }}" target="_blank" class="btn" style="min-height: auto; padding: 6px 12px; font-size: 11px; background: #e0f2fe; color: #0369a1; border-color: #bae6fd; display: inline-flex; align-items: center; gap: 4px;">
                                <iconify-icon icon="solar:document-bold-duotone" style="font-size: 14px;"></iconify-icon>
                                Lihat Dokumen
                            </a>
                        @else
                            <span class="muted" style="font-size: 12px; font-style: italic;">Belum mengunggah identitas</span>
                        @endif
                    </div>
                </div>

                <!-- Kolom 2: Analisis RFM & Catatan CRM -->
                <div style="display: flex; flex-direction: column; gap: 14px;">
                    <!-- Profil RFM -->
                    <div style="background: var(--bg-hover); padding: 10px 14px; border-radius: var(--radius); border: 1px solid var(--border);">
                        <div style="font-weight: 600; font-size: 11px; text-transform: uppercase; color: var(--muted); margin-bottom: 6px; letter-spacing: 0.05em;">Analisis LTV & RFM</div>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; font-size: 12px;">
                            <div>
                                <span style="display: block; color: var(--muted); font-size: 10px;">Recency</span>
                                <strong>{{ $customer->recency ? \Carbon\Carbon::parse($customer->recency)->format('d/m/Y') : 'Belum order' }}</strong>
                            </div>
                            <div>
                                <span style="display: block; color: var(--muted); font-size: 10px;">Frequency</span>
                                <strong>{{ $customer->frequency }}x order</strong>
                            </div>
                            <div>
                                <span style="display: block; color: var(--muted); font-size: 10px;">Monetary</span>
                                <strong>Rp {{ number_format($customer->monetary, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Histori Order -->
                    @if ($customer->orders->count() > 0)
                        <div style="font-size: 12px;">
                            <div style="font-weight: 600; color: var(--text); margin-bottom: 4px; display: flex; align-items: center; gap: 4px;">
                                <iconify-icon icon="solar:history-bold-duotone" style="font-size: 14px; color: var(--primary);"></iconify-icon>
                                Histori Pembelian (Terakhir):
                            </div>
                            <ul style="margin: 0; padding-left: 15px; display: flex; flex-direction: column; gap: 4px; color: var(--muted);">
                                @foreach ($customer->orders->take(3) as $ord)
                                    <li>
                                        <strong>{{ $ord->order_number }}</strong> - Rp {{ number_format($ord->total_amount, 0, ',', '.') }} 
                                        <span class="badge {{ in_array($ord->status, ['paid', 'shipped', 'completed']) ? 'success' : 'warning' }}" style="font-size: 9px; padding: 1px 4px;">{{ in_array($ord->status, ['paid', 'shipped', 'completed']) ? 'PAID' : 'PENDING' }}</span>
                                    </li>
                                @endforeach
                                @if ($customer->orders->count() > 3)
                                    <li style="list-style-type: none; font-style: italic;">+ {{ $customer->orders->count() - 3 }} pesanan lainnya</li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    <!-- Catatan Follow-Up CRM -->
                    <div>
                        <h3 style="font-size: 13px; margin: 0 0 6px 0; font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 4px;">
                            <iconify-icon icon="solar:pen-bold-duotone" style="font-size: 14px; color: var(--primary);"></iconify-icon>
                            Catatan Internal
                        </h3>
                        <form method="POST" action="{{ route('admin.crm.notes', $customer) }}">
                            @csrf
                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                <textarea name="notes" placeholder="Catatan nego, diskon khusus, status follow-up..." required style="min-height: 70px; font-size: 13px; padding: 8px; background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius); color: var(--text);">{{ $customer->notes }}</textarea>
                                <button class="btn primary" type="submit" style="align-self: flex-end; min-height: auto; padding: 6px 12px; font-size: 11px;">Simpan Catatan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Kolom 3: Tindakan Hubungan Pelanggan (Follow Up CRM) -->
                <div style="display: flex; flex-direction: column; gap: 12px; height: 100%; justify-content: center; background: var(--bg-hover); padding: 16px; border-radius: var(--radius); border: 1px solid var(--border);">
                    <span class="muted" style="font-size: 11px; text-align: center; display: block; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;">Aksi Follow-Up</span>
                    
                    @if ($customer->whatsapp_link)
                        <!-- Tampilkan info pesan yang akan dikirim -->
                        <div style="font-size: 11px; color: var(--muted); border-top: 1px solid var(--border); padding-top: 8px;">
                            <span style="font-weight: 600; display: block; margin-bottom: 2px;">Kampanye WhatsApp Otomatis:</span>
                            @if ($seg === 'Lead')
                                <em>Diskon pesanan pertama & link katalog belanja.</em>
                            @elseif ($seg === 'VIP')
                                <em>Ucapan loyalitas & layanan prioritas VIP.</em>
                            @elseif ($seg === 'Churn Alert')
                                <em>Re-engagement penawaran koleksi produk baru.</em>
                            @else
                                <em>Tindak lanjut kualitas layanan belanja terbaru.</em>
                            @endif
                        </div>

                        <a href="{{ $customer->whatsapp_link }}" target="_blank" class="btn" style="justify-content: center; font-weight: bold; background: #25d366; color: white; border-color: #25d366; font-size: 13px; padding: 10px 14px; display: flex; align-items: center; gap: 6px; border-radius: 8px;">
                            <iconify-icon icon="solar:chat-round-line-bold" style="font-size: 18px;"></iconify-icon>
                            Kirim WhatsApp
                        </a>
                    @else
                        <button class="btn" disabled style="justify-content: center; font-size: 13px; opacity: 0.6; display: flex; align-items: center; gap: 6px;">
                            <iconify-icon icon="solar:chat-round-line-bold" style="font-size: 18px;"></iconify-icon>
                            Kirim WhatsApp
                        </button>
                        <span class="muted" style="font-size: 10px; text-align: center; font-style: italic;">Nomor WA tidak terdaftar</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="panel text-center" style="padding: 48px; border: 1px dashed var(--border);">
                <iconify-icon icon="solar:users-group-two-rounded-broken" style="font-size: 56px; color: var(--muted); margin-bottom: 12px; display: inline-block;"></iconify-icon>
                <h3>Pelanggan Tidak Ditemukan</h3>
                <p class="muted">Belum ada profil pembeli yang terdaftar atau cocok dengan pencarian Anda.</p>
            </div>
        @endforelse
    </div>

    <div class="pagination" style="margin-top: 24px;">{{ $customers->links() }}</div>
@endsection
