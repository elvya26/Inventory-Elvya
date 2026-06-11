# TODO - Modifikasi upload media (gambar/video) per item tanpa upload ke pihak ketiga

- [x] Tambah kolom `image_path` dan `video_path` pada tabel `items` (migration baru)
- [x] Update model `Item` agar `fillable` mencakup kolom media
- [x] Update view `resources/views/pencatatan/form.blade.php` (enctype + input file)
- [x] Update controller `app/Http/Controllers/InventoryController.php`:
  - [x] Validasi file gambar/video
  - [x] Simpan file via `$request->file(...)->store(...)`
  - [x] Update kolom `image_path`/`video_path`
  - [x] Hapus file media saat item dihapus
- [x] Update view `resources/views/pencatatan/show.blade.php` untuk menampilkan preview gambar/video
- [x] Pastikan folder publik tersedia: jalankan `php artisan storage:link`
- [ ] Tes end-to-end di UI (tambah/edit barang, lalu lihat detail)
