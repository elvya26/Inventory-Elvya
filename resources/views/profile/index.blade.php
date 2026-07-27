@extends('layouts.app')

@section('content')
    <div class="header-row">
        <div>
            <h1>Profil Pengguna</h1>
            <p class="muted">Detail akun Anda yang terintegrasi melalui Google OAuth.</p>
        </div>
    </div>

    <div class="grid" style="grid-template-columns: 1fr 2fr; gap: 24px; align-items: start;">
        <!-- Card Detail Profil -->
        <div class="panel text-center" style="display: flex; flex-direction: column; align-items: center; padding: 32px 20px;">
            <div style="position: relative; margin-bottom: 20px;">
                @if ($currentUser->avatar)
                    <img src="{{ $currentUser->avatar }}" alt="{{ $currentUser->name }}" style="width: 120px; height: 120px; border-radius: 50%; border: 4px solid var(--line); object-fit: cover; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);">
                @else
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--bg); display: flex; justify-content: center; align-items: center; font-size: 36px; color: var(--muted); border: 4px solid var(--line);">
                        {{ strtoupper(substr($currentUser->name ?? $currentUser->email ?? 'U', 0, 1)) }}
                    </div>
                @endif
                <span class="badge {{ $currentUser->role === 'admin' ? 'danger' : 'ok' }}" style="position: absolute; bottom: 0; right: 0; padding: 6px 12px; font-size: 11px; border: 2px solid var(--panel);">
                    {{ strtoupper($currentUser->role) }}
                </span>
            </div>

            <h2 style="margin: 10px 0 4px; font-size: 22px;">{{ $currentUser->name }}</h2>
            <p class="muted" style="margin: 0 0 20px; font-size: 14px;">{{ $currentUser->email }}</p>

            <div style="width: 100%; border-top: 1px solid var(--line); padding-top: 20px; margin-top: 10px; text-align: left;">
                <div style="margin-bottom: 12px; display: flex; justify-content: space-between;">
                    <span class="muted" style="font-size: 13px;">Google ID:</span>
                    <strong style="font-size: 13px;">{{ $currentUser->google_id ?: '-' }}</strong>
                </div>
                <div style="margin-bottom: 12px; display: flex; justify-content: space-between;">
                    <span class="muted" style="font-size: 13px;">Hak Akses:</span>
                    <strong style="font-size: 13px;">{{ $currentUser->role === 'admin' ? 'Administrator' : 'Staff / User' }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span class="muted" style="font-size: 13px;">Terdaftar:</span>
                    <strong style="font-size: 13px;">{{ $currentUser->created_at?->format('d M Y') ?: '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- Form Edit Profil -->
        <div class="panel" style="padding: 32px;">
            <h2>Pengaturan Profil</h2>
            <p class="muted" style="margin-bottom: 24px;">Perbarui nama tampilan Anda atau ganti peran (role) untuk kebutuhan simulasi sistem.</p>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <label>Nama Lengkap
                        <input name="name" value="{{ old('name', $currentUser->name) }}" required>
                    </label>

                    <label>Peran / Role
                        <select name="role" required>
                            <option value="user" {{ $currentUser->role === 'user' ? 'selected' : '' }}>User (Staff Gudang biasa)</option>
                            <option value="admin" {{ $currentUser->role === 'admin' ? 'selected' : '' }}>Admin (Akses Penuh)</option>
                        </select>
                    </label>

                    <div style="margin-top: 10px;">
                        <button class="btn primary" type="submit" style="padding: 10px 24px; font-weight: bold;">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
