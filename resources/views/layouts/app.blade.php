<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="{{ route('dashboard') }}">Inventory Services</a>
            <nav class="nav">
                <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="{{ request()->routeIs('pencatatan.*') ? 'active' : '' }}" href="{{ route('pencatatan.index') }}">Pencatatan</a>
                <a class="{{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">Cetak Laporan</a>
                <a class="{{ request()->routeIs('notifikasi.*') ? 'active' : '' }}" href="{{ route('notifikasi.index') }}">Notif & Komunikasi</a>

                @if (session()->has('user_id'))
                    <a href="{{ route('logout') }}">Logout</a>
                @else
                    <a href="{{ route('login') }}">Masuk</a>
                @endif
            </nav>
        </div>
    </header>


    <main class="page">
        @if (session('status'))
            <div class="alert">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="errors">
                <strong>Periksa kembali input:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
