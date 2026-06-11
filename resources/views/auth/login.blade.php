@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="auth-shell">
        <div class="auth-panel auth-panel-info">
            <div class="auth-brand">
                <span class="auth-logo" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9A2.5 2.5 0 0 1 17.5 19h-11A2.5 2.5 0 0 1 4 16.5v-9Z" stroke="currentColor" stroke-width="1.6"/>
                        <path d="M8 9h8M8 12.5h5.5M8 16h3" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                </span>
                <div>
                    <p class="auth-kicker">Inventory Services</p>
                    <h1 class="auth-title">Kelola stok barang dengan lebih mudah</h1>
                </div>
            </div>

            <p class="auth-lead">
                Satu platform untuk pencatatan barang, mutasi stok, cetak laporan, dan komunikasi tim.
            </p>

            <ul class="auth-features">
                <li>
                    <strong>Pencatatan</strong>
                    <span>CRUD barang dan riwayat mutasi stok</span>
                </li>
                <li>
                    <strong>Laporan</strong>
                    <span>Rekap stok siap cetak kapan saja</span>
                </li>
                <li>
                    <strong>Notifikasi</strong>
                    <span>Pantau pesan dan status komunikasi</span>
                </li>
            </ul>
        </div>

        <div class="auth-panel auth-panel-form">
            <div class="auth-card">
                <div class="auth-card-head">
                    <h2>Selamat datang</h2>
                    <p class="muted">Masuk dengan akun Google untuk melanjutkan ke dashboard.</p>
                </div>

                @if (session('status'))
                    <div class="alert">{{ session('status') }}</div>
                @endif

                @if (session('error'))
                    <div class="errors">{{ session('error') }}</div>
                @endif

                <a class="btn-google" href="{{ route('login.google') }}">
                    <svg class="btn-google-icon" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4" d="M23.52 12.27c0-.85-.07-1.47-.22-2.12H12v3.83h6.48c-.13 1.05-.84 2.63-2.41 3.68l-.02.14 3.5 2.72.24.02c2.22-2.04 3.51-5.05 3.51-8.27z"/>
                        <path fill="#34A853" d="M12 24c3.24 0 5.96-1.08 7.94-2.94l-3.79-2.94c-1.01.68-2.36 1.16-4.15 1.16-3.18 0-5.87-2.12-6.84-5.22l-.13.01-3.7 2.87-.05.12C3.42 21.53 7.42 24 12 24z"/>
                        <path fill="#FBBC05" d="M5.16 14.06A7.2 7.2 0 0 1 4.73 12c0-.72.13-1.42.35-2.06l-.01-.15-3.75-2.93-.12.06A11.98 11.98 0 0 0 1 12c0 1.94.47 3.77 1.3 5.4l2.86-2.34z"/>
                        <path fill="#EA4335" d="M12 4.75c2.25 0 3.77.97 4.64 1.78l3.4-3.32C17.94 1.19 15.24 0 12 0 7.42 0 3.42 2.47 1.3 6.6l2.86 2.34C5.13 5.87 7.82 3.75 12 3.75z"/>
                    </svg>
                    Lanjutkan dengan Google
                </a>

                <p class="auth-note muted">
                    Hanya pengguna terdaftar yang dapat mengakses sistem ini.
                </p>
            </div>

            <p class="auth-footer muted">© {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </div>
@endsection
