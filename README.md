# LicitaStore Inventory

> Satu aplikasi untuk mengelola stok, menjalankan penjualan, dan menjaga pelanggan tetap terhubung.

**LicitaStore Inventory** adalah aplikasi manajemen inventaris dan e-commerce berbasis Laravel. Aplikasi ini membantu tim operasional mencatat barang dan pergerakan stok, sementara pelanggan dapat melihat katalog, berbelanja, membayar, serta melacak pesanan—dari satu sistem yang terintegrasi.

## Mengapa LicitaStore?

Ketika data stok, penjualan, dan pelanggan tersebar di banyak tempat, keputusan bisnis menjadi lambat. LicitaStore menyatukannya dalam alur yang sederhana: barang masuk ke inventaris, tampil di katalog saat tersedia, lalu setiap pesanan dapat dipantau hingga pengiriman.

## Fitur utama

### Panel admin

- **Manajemen barang** — tambah, ubah, cari, dan hapus barang berdasarkan SKU, kategori, lokasi, satuan, harga, dan batas stok minimum.
- **Mutasi stok** — catat stok masuk, keluar, atau penyesuaian dengan riwayat yang mudah ditelusuri.
- **Media produk** — unggah gambar dan video untuk setiap barang.
- **Laporan stok** — lihat rekap stok serta pergerakan berdasarkan rentang tanggal, lalu cetak laporan.
- **Notifikasi internal** — susun dan perbarui status pesan untuk tim.
- **Pesanan & CRM** — pantau pesanan, kelola pengiriman, dan simpan catatan pelanggan.

### Storefront pelanggan

- Katalog produk yang hanya menampilkan stok tersedia.
- Pencarian produk, keranjang belanja, dan checkout.
- Estimasi pilihan pengiriman dan pelacakan resi.
- Pembayaran Virtual Account dan halaman status pembayaran.
- Portal pelanggan untuk melihat pesanan dan mengunggah dokumen.

## Teknologi

| Bagian | Teknologi |
| --- | --- |
| Backend | PHP 8.2 & Laravel 12 |
| Database | PostgreSQL (termasuk Neon) |
| Container | Docker Compose |
| Reverse proxy | Traefik |
| Autentikasi | Google OAuth |
| Integrasi opsional | DOKU, KiriminAja, MinIO/S3, dan Fonnte WhatsApp |

## Mulai cepat dengan Docker

### Prasyarat

- Docker Desktop
- Kredensial PostgreSQL yang dapat diakses (misalnya Neon)
- Google OAuth Client ID dan Client Secret

### 1. Siapkan environment

```powershell
Copy-Item .env.example .env
```

Lengkapi nilai berikut di `.env`:

```env
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=ep-your-neon-host.ap-southeast-1.aws.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=neondb_owner
DB_PASSWORD=your-password
DB_SSLMODE=require

SESSION_DRIVER=database

GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

Pada pengaturan Google OAuth, tambahkan URL callback yang sama: `http://localhost:8000/auth/google/callback`.

### 2. Bangun dan jalankan aplikasi

```powershell
docker compose up -d --build
docker compose exec pencatatan php artisan migrate --seed
docker compose exec pencatatan php artisan storage:link
```

### 3. Buka aplikasi

| Halaman | URL |
| --- | --- |
| Login admin | `http://localhost:8000/admin/login` |
| Panel admin | `http://localhost:8000/admin` |
| Storefront | `http://localhost:8000/licitastore/login` |
| Dashboard Traefik | `http://localhost:8080` |

## Integrasi opsional

Sebagian fitur tetap dapat dicoba tanpa layanan eksternal. Pengiriman menyediakan data simulasi bila `KIRIMINAJA_API_KEY` belum diisi; unggahan dokumen juga disimpan secara lokal bila MinIO/S3 belum dikonfigurasi.

Untuk mengaktifkan layanan nyata, tambahkan konfigurasi yang relevan pada `.env` dan pastikan kredensial tersebut tidak pernah di-commit:

```env
KIRIMINAJA_API_KEY=
KIRIMINAJA_BASE_URL=https://api.kiriminaja.com/v1

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=
AWS_BUCKET=
AWS_ENDPOINT=

FONNTE_TOKEN=
```

> Pastikan kredensial pembayaran, pengiriman, dan penyimpanan disimpan di environment variable atau secret manager—bukan di source code.

## Struktur singkat

```text
app/            Controller, model, service, dan middleware
database/       Migration serta data contoh (seeder)
resources/      Blade view untuk admin dan storefront
routes/         Definisi route aplikasi
docker/         Skrip entrypoint container
```

## Perintah berguna

```powershell
# Melihat log aplikasi
docker compose logs -f pencatatan

# Menjalankan ulang migration dan seeder
docker compose exec pencatatan php artisan migrate:fresh --seed

# Menghentikan container
docker compose down
```

## Kontribusi

Ide, perbaikan, dan pengembangan fitur sangat terbuka. Buat branch baru, lakukan perubahan yang terfokus, lalu ajukan pull request dengan penjelasan singkat dan langkah pengujiannya.

---

Dibuat untuk membantu operasional berjalan lebih rapi, agar tim bisa lebih fokus mengembangkan bisnis.
