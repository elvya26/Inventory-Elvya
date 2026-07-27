@extends('layouts.guest')

@section('title', 'Masuk - Licita Store')

@section('content')
    <style>
        .auth-container {
            display: flex;
            min-height: 100vh;
            background: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .auth-info-pane {
            flex: 1.2;
            background: linear-gradient(135deg, #0d9488 0%, #115e59 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 60px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }

        /* Ambient glows */
        .auth-info-pane::before {
            content: '';
            position: absolute;
            width: 350px;
            height: 350px;
            background: rgba(20, 184, 166, 0.4);
            filter: blur(100px);
            border-radius: 50%;
            top: -50px;
            left: -50px;
        }

        .auth-info-pane::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(4, 120, 87, 0.3);
            filter: blur(120px);
            border-radius: 50%;
            bottom: -50px;
            right: -50px;
        }

        .auth-brand-wrapper {
            position: relative;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .auth-brand-logo {
            width: 54px;
            height: 54px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-brand-logo iconify-icon {
            font-size: 28px;
            color: #ffffff;
        }

        .auth-brand-text h1 {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .auth-brand-text p {
            font-size: 13px;
            opacity: 0.8;
            margin: 2px 0 0;
        }

        .auth-hero-content {
            position: relative;
            z-index: 10;
            margin: 60px 0;
        }

        .auth-hero-title {
            font-size: 38px;
            font-weight: 800;
            line-height: 1.2;
            margin: 0 0 20px;
            letter-spacing: -0.03em;
        }

        .auth-hero-lead {
            font-size: 16px;
            opacity: 0.9;
            line-height: 1.6;
            margin: 0 0 40px;
            max-width: 520px;
        }

        .auth-features-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .auth-feature-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .auth-feature-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .auth-feature-icon iconify-icon {
            font-size: 20px;
            color: #ffffff;
        }

        .auth-feature-info h4 {
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 4px;
        }

        .auth-feature-info p {
            font-size: 13px;
            opacity: 0.8;
            margin: 0;
            line-height: 1.4;
        }

        .auth-footer-text {
            position: relative;
            z-index: 10;
            font-size: 13px;
            opacity: 0.7;
        }

        /* Form side */
        .auth-form-pane {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
        }

        .auth-card-wrapper {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.02);
            border: 1px solid #f1f5f9;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .auth-card-header {
            margin-bottom: 32px;
        }

        .auth-card-logo-container {
            width: 64px;
            height: 64px;
            background: #f0fdfa;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .auth-card-logo-container iconify-icon {
            font-size: 32px;
            color: #0d9488;
        }

        .auth-card-title {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 8px;
            letter-spacing: -0.02em;
        }

        .auth-card-subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 0;
            line-height: 1.5;
        }

        .btn-google-login {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            height: 52px;
            border-radius: 14px;
            border: 2px solid #e2e8f0;
            background: #ffffff;
            color: #334155;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }

        .btn-google-login:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(148, 163, 184, 0.12);
        }

        .btn-google-login:active {
            transform: translateY(0);
        }

        .btn-google-icon {
            width: 22px;
            height: 22px;
        }

        .auth-card-footer-note {
            margin-top: 24px;
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.5;
        }

        .copyright-text {
            margin-top: 32px;
            font-size: 12px;
            color: #94a3b8;
        }

        @media (max-width: 900px) {
            .auth-container {
                flex-direction: column;
            }
            .auth-info-pane {
                padding: 40px;
                flex: none;
            }
            .auth-hero-content {
                margin: 40px 0;
            }
            .auth-form-pane {
                padding: 40px 20px;
            }
        }
    </style>

    <div class="auth-container">
        <!-- Panel Info Kiri -->
        <div class="auth-info-pane">
            <div class="auth-brand-wrapper">
                <div class="auth-brand-logo">
                    <iconify-icon icon="solar:cart-large-4-bold-duotone"></iconify-icon>
                </div>
                <div class="auth-brand-text">
                    <h1>Licita Store</h1>
                    <p>Integrasi Inventori & E-Commerce</p>
                </div>
            </div>

            <div class="auth-hero-content">
                <h2 class="auth-hero-title">Belanja Instan Langsung Dari Gudang Pusat</h2>
                <p class="auth-hero-lead">Licita Store menghubungkan Anda langsung dengan pencatatan gudang utama. Menjamin ketersediaan stok yang akurat, proses bayar secepat kilat, dan pengiriman otomatis.</p>
                
                <ul class="auth-features-list">
                    <li class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <iconify-icon icon="solar:box-bold-duotone"></iconify-icon>
                        </div>
                        <div class="auth-feature-info">
                            <h4>Katalog Inventori Aktif</h4>
                            <p>Seluruh barang yang ditampilkan tersinkronisasi 100% dengan stok fisik gudang.</p>
                        </div>
                    </li>
                    <li class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <iconify-icon icon="solar:card-send-bold-duotone"></iconify-icon>
                        </div>
                        <div class="auth-feature-info">
                            <h4>DOKU Checkout Instan</h4>
                            <p>Pilihan pembayaran terlengkap (Virtual Account, QRIS, dll) dengan verifikasi otomatis.</p>
                        </div>
                    </li>
                    <li class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <iconify-icon icon="solar:delivery-bold-duotone"></iconify-icon>
                        </div>
                        <div class="auth-feature-info">
                            <h4>Kirim Otomatis & Cepat</h4>
                            <p>Order langsung didaftarkan ke kurir pengiriman (Kiriminaja) begitu VA dilunasi.</p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="auth-footer-text">
                © {{ date('Y') }} Licita Store. All rights reserved.
            </div>
        </div>

        <!-- Panel Form Kanan -->
        <div class="auth-form-pane">
            <div class="auth-card-wrapper">
                <div class="auth-card-header">
                    <div class="auth-card-logo-container">
                        <iconify-icon icon="solar:lock-keyhole-minimalistic-bold-duotone"></iconify-icon>
                    </div>
                    <h2 class="auth-card-title">Masuk E-Commerce</h2>
                    <p class="auth-card-subtitle">Untuk menjaga keamanan data transaksi dan pelacakan pesanan Anda, silakan masuk terlebih dahulu.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success" style="background: #e6f4ea; color: #137333; padding: 12px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; text-align: left;">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger" style="background: #fce8e6; color: #c5221f; padding: 12px; border-radius: 10px; font-size: 13px; margin-bottom: 20px; text-align: left;">
                        {{ session('error') }}
                    </div>
                @endif

                <div style="margin: 20px 0;">
                    <a class="btn-google-login" href="{{ route('ecommerce.login.google') }}">
                        <svg class="btn-google-icon" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M23.52 12.27c0-.85-.07-1.47-.22-2.12H12v3.83h6.48c-.13 1.05-.84 2.63-2.41 3.68l-.02.14 3.5 2.72.24.02c2.22-2.04 3.51-5.05 3.51-8.27z"/>
                            <path fill="#34A853" d="M12 24c3.24 0 5.96-1.08 7.94-2.94l-3.79-2.94c-1.01.68-2.36 1.16-4.15 1.16-3.18 0-5.87-2.12-6.84-5.22l-.13.01-3.7 2.87-.05.12C3.42 21.53 7.42 24 12 24z"/>
                            <path fill="#FBBC05" d="M5.16 14.06A7.2 7.2 0 0 1 4.73 12c0-.72.13-1.42.35-2.06l-.01-.15-3.75-2.93-.12.06A11.98 11.98 0 0 0 1 12c0 1.94.47 3.77 1.3 5.4l2.86-2.34z"/>
                            <path fill="#EA4335" d="M12 4.75c2.25 0 3.77.97 4.64 1.78l3.4-3.32C17.94 1.19 15.24 0 12 0 7.42 0 3.42 2.47 1.3 6.6l2.86 2.34C5.13 5.87 7.82 3.75 12 3.75z"/>
                        </svg>
                        Masuk dengan Akun Google
                    </a>
                </div>

                <p class="auth-card-footer-note">
                    Dengan menekan tombol masuk, Anda menyetujui pelacakan logistik Kiriminaja dan pemrosesan pesanan otomatis di Licita Store.
                </p>
            </div>
            
            <p class="copyright-text">Layanan Terpadu Licita E-Commerce Portal</p>
        </div>
    </div>
@endsection
