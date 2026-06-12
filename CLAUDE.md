# ScanHadir — Claude Code Context

## Obsidian Vault

**WAJIB DIBACA DI AWAL SETIAP SESI:**

```
C:\Users\acer\Documents\Obsidian Vault\02 Projects\ScanHadir Knowledge.md
```

File ini adalah source-of-truth untuk project. Baca sebelum mengerjakan task apapun.

## Setelah Perubahan Signifikan

Update `ScanHadir Knowledge.md` di vault jika ada:
- Model baru atau perubahan schema database
- Route baru atau perubahan routing
- Komponen Livewire baru
- Perubahan auth/role flow
- Migration baru yang di-commit
- Status pekerjaan yang berubah (ongoing/done/pending)

## Project Overview

- **Stack**: Laravel 11 + Livewire + Filament 3 + Alpine.js + Tailwind
- **Dev URL**: `http://scanhadir.test` (Laragon)
- **PHP**: ^8.2
- **Roles**: admin, teacher, dosen, student

## Aturan Penting

- Jangan jalankan `php artisan migrate` tanpa konfirmasi eksplisit dari user
- Layout terpisah per role: `layouts/admin.blade.php`, `layouts/teacher.blade.php`, `layouts/student.blade.php`
- Livewire components ada di `app/Livewire/`
- Semua master data CRUD ditangani oleh `DashboardController`

## Vault Path

```
C:\Users\acer\Documents\Obsidian Vault
```

Untuk project lain atau knowledge umum, vault ini berisi:
- `02 Projects/` — semua project knowledge
- `05 Tech Stack/` — tech stack references
- `06 Architecture/` — arsitektur patterns
- `09 Patterns/` — code patterns
