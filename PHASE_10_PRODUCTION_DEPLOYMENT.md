# Phase 10: Production Deployment & Optimization

Status: COMPLETE
Date: March 29, 2026

## Objective

Menjalankan paket final produksi agar ScanHadir bisa di-deploy dengan proses yang repeatable, aman, dan terukur.

## Deliverables

1. Route caching readiness
- Refactor route QR dari closure ke controller agar perintah route cache tidak gagal.
- File:
  - app/Http/Controllers/StudentQrCodeController.php
  - routes/web.php

2. Database performance indexes
- Menambahkan index komposit untuk query laporan dan analitik attendance.
- File:
  - database/migrations/2026_03_29_180000_add_performance_indexes_to_attendances_table.php
- Index ditambahkan:
  - attendances_date_status_index (date, status)
  - attendances_student_date_index (student_id, date)

3. Production preparation command
- Menambahkan command artisan terpusat untuk langkah persiapan produksi.
- File:
  - routes/console.php
- Command:
  - php artisan app:prepare-production --with-migrate --force
- Opsi:
  - --with-migrate
  - --with-seed
  - --force
  - --dry-run
- Langkah otomatis command:
  - Validasi environment/non-production guard
  - Validasi permission storage dan bootstrap/cache
  - optimize:clear
  - config:cache
  - route:cache
  - view:cache
  - event:cache
  - queue:restart

4. Deployment scripts
- Menyediakan script siap pakai untuk Windows dan Linux.
- File:
  - scripts/deploy-production.ps1
  - scripts/deploy-production.sh
- Isi alur script:
  - composer install --no-dev --prefer-dist --optimize-autoloader
  - npm ci + npm run build
  - php artisan app:prepare-production --with-migrate --force
  - Health check ke /up

5. Documentation update
- README diperbarui untuk workflow deployment Phase 10.

## How To Run (Recommended)

### Windows PowerShell

```powershell
powershell -ExecutionPolicy Bypass -File scripts/deploy-production.ps1
```

Dengan seeding:

```powershell
powershell -ExecutionPolicy Bypass -File scripts/deploy-production.ps1 -WithSeed
```

### Linux/macOS

```bash
bash scripts/deploy-production.sh
```

Dengan seeding:

```bash
bash scripts/deploy-production.sh --with-seed
```

## Manual Command Variant

```bash
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan app:prepare-production --with-migrate --force
```

## Verification Checklist

- php artisan migrate --force berhasil.
- php artisan app:prepare-production --dry-run --force menampilkan semua langkah.
- php artisan route:cache tidak gagal.
- Endpoint health /up merespons 200.
- Laporan/analitik attendance tetap berjalan dengan index baru.

## Rollback Notes

Jika diperlukan rollback:

```bash
php artisan migrate:rollback --step=1
php artisan optimize:clear
```

Step rollback di atas akan menghapus index performa yang baru ditambahkan.

## Production Outcome

Phase 10 menutup gap deployment operasional dengan:
- proses deploy terstandar,
- route cache compatibility,
- optimasi query attendance,
- command persiapan produksi terpusat,
- dan health check readiness.
