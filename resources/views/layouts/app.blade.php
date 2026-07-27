<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap">
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</head>
<body class="app-layout">
    <!-- Sidebar / NavDrawer -->
    <aside class="sidebar no-print">
        @php
            $isAdminPath = request()->is('admin') || request()->is('admin/*');
        @endphp

        <div class="sidebar-header">
            @if ($isAdminPath)
                <span class="sidebar-logo"><iconify-icon icon="solar:box-bold-duotone" style="font-size: 24px; color: var(--primary);"></iconify-icon></span>
                <span class="sidebar-brand">Inventory Services</span>
            @else
                <span class="sidebar-logo"><iconify-icon icon="solar:cart-large-4-bold-duotone" style="font-size: 24px; color: var(--primary);"></iconify-icon></span>
                <span class="sidebar-brand">Licita Store</span>
            @endif
        </div>

        @if (isset($currentUser))
            <!-- Profile Card inside NavDrawer -->
            <div class="sidebar-profile">
                <a href="{{ $isAdminPath ? route('profile.index') : route('ecommerce.customer') }}" class="profile-link">
                    @if ($currentUser->avatar)
                        <img class="profile-avatar" src="{{ $currentUser->avatar }}" alt="{{ $currentUser->name }}">
                    @else
                        <div class="profile-avatar-placeholder">{{ strtoupper(substr($currentUser->name ?? $currentUser->email ?? 'U', 0, 1)) }}</div>
                    @endif
                    <div class="profile-info">
                        <span class="profile-name">{{ $currentUser->name }}</span>
                        <span class="profile-role-badge {{ $isAdminPath && $currentUser->role === 'admin' ? 'admin' : 'user' }}">{{ $isAdminPath ? strtoupper($currentUser->role) : 'USER' }}</span>
                    </div>
                </a>
            </div>
        @endif


        @if ($isAdminPath)
            <!-- Sidebar for Admin Domain -->
            <nav class="sidebar-nav">
                <a class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <span class="nav-icon"><iconify-icon icon="solar:chart-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                    <span class="nav-label">Dashboard</span>
                </a>
                <a class="nav-item {{ request()->routeIs('pencatatan.*') ? 'active' : '' }}" href="{{ route('pencatatan.index') }}">
                    <span class="nav-icon"><iconify-icon icon="solar:document-text-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                    <span class="nav-label">Pencatatan</span>
                </a>
                <a class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                    <span class="nav-icon"><iconify-icon icon="solar:printer-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                    <span class="nav-label">Cetak Laporan</span>
                </a>
                <a class="nav-item {{ request()->routeIs('notifikasi.*') ? 'active' : '' }}" href="{{ route('notifikasi.index') }}">
                    <span class="nav-icon"><iconify-icon icon="solar:bell-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                    <span class="nav-label">Notif & Komunikasi</span>
                </a>
                
                @if (app()->environment('local') || (isset($currentUser) && $currentUser->role === 'admin'))
                    <div style="margin-top: 15px; margin-bottom: 5px; padding-left: 14px; font-size: 11px; font-weight: bold; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em;" class="no-print">Admin Panel</div>
                    <a class="nav-item {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:clipboard-list-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                        <span class="nav-label">Pesanan Toko</span>
                    </a>
                    <a class="nav-item {{ request()->routeIs('admin.crm.*') ? 'active' : '' }}" href="{{ route('admin.crm.index') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:users-group-two-rounded-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                        <span class="nav-label">CRM Pelanggan</span>
                    </a>
                @endif

                <div style="margin-top: 15px; margin-bottom: 5px; padding-left: 14px; font-size: 11px; font-weight: bold; color: var(--muted); text-transform: uppercase; letter-spacing: 0.05em;" class="no-print">Navigasi Luar</div>
                <a class="nav-item" href="/licitastore" target="_blank">
                    <span class="nav-icon"><iconify-icon icon="solar:cart-large-4-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                    <span class="nav-label">Kunjungi Toko</span>
                </a>
                <a class="nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.index') }}">
                    <span class="nav-icon"><iconify-icon icon="solar:user-circle-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                    <span class="nav-label">Profil Saya</span>
                </a>

                <div class="sidebar-spacer"></div>

                @if (session()->has('user_id'))
                    <a class="nav-item nav-logout" href="{{ route('logout') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:logout-3-bold-duotone" style="font-size: 20px; color: var(--danger);"></iconify-icon></span>
                        <span class="nav-label">Logout</span>
                    </a>
                @else
                    <a class="nav-item" href="{{ route('login') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:key-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                        <span class="nav-label">Masuk</span>
                    </a>
                @endif
            </nav>
        @else
            <!-- Sidebar for Customer Storefront Domain -->
            <nav class="sidebar-nav">
                <a class="nav-item {{ request()->routeIs('ecommerce.index') ? 'active' : '' }}" href="{{ route('ecommerce.index') }}">
                    <span class="nav-icon"><iconify-icon icon="solar:bag-3-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                    <span class="nav-label">Beranda</span>
                </a>
                <a class="nav-item {{ request()->routeIs('ecommerce.cart') ? 'active' : '' }}" href="{{ route('ecommerce.cart') }}">
                    <span class="nav-icon"><iconify-icon icon="solar:cart-large-4-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                    <span class="nav-label">Keranjang Belanja</span>
                </a>
                <a class="nav-item {{ request()->routeIs('ecommerce.customer') ? 'active' : '' }}" href="{{ route('ecommerce.customer') }}">
                    <span class="nav-icon"><iconify-icon icon="solar:user-circle-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                    <span class="nav-label">Portal Akun</span>
                </a>
                <a class="nav-item {{ request()->routeIs('bank.cek_va') ? 'active' : '' }}" href="{{ route('bank.cek_va') }}">
                    <span class="nav-icon"><iconify-icon icon="solar:card-transfer-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                    <span class="nav-label">Cek VA Bank</span>
                </a>
                <a class="nav-item {{ request()->routeIs('ecommerce.track') ? 'active' : '' }}" href="{{ route('ecommerce.track') }}">
                    <span class="nav-icon"><iconify-icon icon="solar:delivery-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                    <span class="nav-label">Lacak Pesanan</span>
                </a>


                <div class="sidebar-spacer"></div>

                @if (session()->has('user_id'))
                    <a class="nav-item nav-logout" href="{{ route('ecommerce.logout') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:logout-3-bold-duotone" style="font-size: 20px; color: var(--danger);"></iconify-icon></span>
                        <span class="nav-label">Logout</span>
                    </a>
                @else
                    <a class="nav-item" href="{{ route('ecommerce.login') }}">
                        <span class="nav-icon"><iconify-icon icon="solar:key-bold-duotone" style="font-size: 20px;"></iconify-icon></span>
                        <span class="nav-label">Masuk Toko</span>
                    </a>
                @endif
            </nav>
        @endif
    </aside>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Top bar for Mobile -->
        <header class="mobile-header no-print">
            <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>
            <span class="mobile-brand">
                @if ($isAdminPath)
                    Inventory Services
                @else
                    Licita Store
                @endif
            </span>
            @if (isset($currentUser))
                <a href="{{ $isAdminPath ? route('profile.index') : route('ecommerce.customer') }}">
                    @if ($currentUser->avatar)
                        <img class="mobile-avatar" src="{{ $currentUser->avatar }}" alt="{{ $currentUser->name }}">
                    @else
                        <div class="mobile-avatar-placeholder">{{ strtoupper(substr($currentUser->name ?? $currentUser->email ?? 'U', 0, 1)) }}</div>
                    @endif
                </a>
            @endif
        </header>

        <!-- Main Content -->
        <main class="content-wrapper">
            @if (session('status'))
                <div class="alert">
                    <span class="alert-text">{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="errors">
                    <strong>Periksa kembali input Anda:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
            document.querySelector('.sidebar-overlay').classList.toggle('open');
        }
    </script>
</body>
</html>
