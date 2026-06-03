# Inventory Services Laravel

Aplikasi inventory berbasis Laravel, PostgreSQL Neon, Docker Compose, dan Traefik.

## Service Aplikasi

- `pencatatan`: CRUD barang dan mutasi stok di `/pencatatan`
- `cetak-laporan`: rekap stok dan halaman cetak di `/laporan`
- `notif-komunikasi`: pesan notifikasi dan komunikasi di `/notifikasi`

Traefik berjalan sebagai reverse proxy di `http://inventory.localhost`, dengan dashboard di `http://localhost:8080`.

## Menjalankan

1. Salin konfigurasi environment.

   ```powershell
   Copy-Item .env.example .env
   ```

2. Isi kredensial Neon PostgreSQL di `.env`.

   ```env
   DB_HOST=ep-your-neon-host.ap-southeast-1.aws.neon.tech
   DB_PORT=5432
   DB_DATABASE=neondb
   DB_USERNAME=neondb_owner
   DB_PASSWORD=password_neon
   DB_SSLMODE=require
   ```

3. Jalankan Docker Desktop, lalu build container.

   ```powershell
   docker compose up -d --build
   ```

4. Jalankan migration dan seed data contoh.

   ```powershell
   docker compose exec pencatatan php artisan migrate --seed
   ```

5. Buka aplikasi.

   ```text
   http://inventory.localhost
   ```

## Catatan Neon

Jika Neon memberi connection string, kamu juga bisa memakai `DATABASE_URL` di `.env`. Pastikan parameter SSL aktif karena Neon membutuhkan koneksi TLS.
