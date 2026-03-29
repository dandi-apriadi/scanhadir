# ScanHadir

Sistem presensi siswa berbasis QR Code untuk sekolah, dibangun dengan Laravel, Livewire, dan Filament.

[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4)](https://www.php.net)
[![Filament](https://img.shields.io/badge/Filament-3.2-orange)](https://filamentphp.com)
[![License](https://img.shields.io/badge/License-MIT-green)](https://opensource.org/licenses/MIT)

## Ringkasan

ScanHadir membantu sekolah mengelola kehadiran siswa secara cepat, akurat, dan terukur. Sistem ini mendukung pemindaian QR, dashboard siswa, dan panel administrasi untuk monitoring serta pelaporan.

## Fitur Utama

- Pemindaian kehadiran siswa melalui QR Code.
- Dashboard siswa untuk melihat status kehadiran.
- Alur login siswa berbasis Livewire.
- Panel admin berbasis Filament untuk pengelolaan data.
- Struktur data presensi, siswa, kelas, dan hari libur.
- Siap ekspor laporan dengan integrasi PDF (`barryvdh/laravel-dompdf`).

## Stack Teknologi

- Backend: Laravel 11, PHP 8.2+
- Frontend: Blade, Livewire, Tailwind CSS, Vite
- Admin Panel: Filament 3
- Database: MySQL/MariaDB (direkomendasikan)
- QR Code: simplesoftwareio/simple-qrcode

## Struktur Modul (High Level)

- `app/Livewire`: Komponen interaktif untuk login, scan, dashboard siswa.
- `app/Models`: Model utama (`Student`, `Attendance`, `StudentClass`, `Holiday`, `User`).
- `app/Filament`: Resource/panel admin.
- `database/migrations`: Skema tabel inti sistem.
- `routes/web.php`: Rute aplikasi publik dan autentikasi dasar.

## Alur Akses

- `/login`: Halaman login siswa.
- `/scan`: Halaman pemindaian presensi.
- `/dashboard`: Dashboard siswa (memerlukan autentikasi).
- `/admin`: Panel admin Filament (default path Filament).

## Instalasi Lokal

### 1. Clone repository

```bash
git clone https://github.com/dandi-apriadi/scanhadir.git
cd scanhadir
```

### 2. Install dependency

```bash
composer install
npm install
```

### 3. Konfigurasi environment

```bash
copy .env.example .env
php artisan key:generate
```

Atur koneksi database pada file `.env`, lalu jalankan migrasi:

```bash
php artisan migrate
```

### 4. Jalankan aplikasi

Opsi A (rekomendasi, satu perintah):

```bash
composer run dev
```

Opsi B (manual):

```bash
php artisan serve
npm run dev
```

## Testing

```bash
php artisan test
```

## Dokumentasi Tambahan

- Dokumen rancangan sistem: `docs/rancangan_sistem.md`

## Deployment Notes

- Pastikan `APP_ENV`, `APP_DEBUG`, dan kredensial database sesuai environment produksi.
- Jalankan optimasi sebelum deploy:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Kontribusi

Kontribusi terbuka melalui pull request. Gunakan gaya commit yang jelas dan sertakan konteks perubahan.

## Lisensi

Proyek ini menggunakan lisensi MIT.
