# 📄 Laporan Serah Terima (Handover Report) - Agent VS
**Tanggal:** April 5, 2026
**Status Proyek:** Dashboard Logic & Routing Synchronization Fixes

---

## 🛠 Perbaikan yang Telah Dilakukan (Applied Fixes)

### 1. Standarisasi Terminologi Kehadiran
Terminologi di database dan aplikasi telah diseragamkan menjadi **Sentence Case** (Bahasa Indonesia) untuk konsistensi dengan sistem IOT-Attendance:
- `Hadir` (bukan `present` atau `hadir`)
- `Telat` (bukan `late` atau `telat`)
- `Sakit`
- `Izin`
- `Alpa` (bukan `absent`)

**File Terkait:**
- `database/factories/AttendanceFactory.php`
- `app/Livewire/TeacherDashboard.php`
- `app/Livewire/StudentDashboard.php`

### 2. Logika Dashboard Guru (Teacher Dashboard)
- **Problem:** Statistik tampil `0` karena hanya mengecek tabel `mata_kuliah_dosen_assignments`.
- **Fix:** Menambahkan pengecekan langsung ke kolom `teacher_id` di tabel `schedules`. Sekarang guru yang di-assign langsung di jadwal (tanpa tabel assignment tambahan) tetap terhitung statistiknya.
- **Status:** ✅ Terverifikasi Berhasil (18 Hadir, 3 Terlambat, 15 Alpa pada akun `guru1@sekolah.sch.id`).

### 3. Logika Dashboard Siswa (Student Dashboard)
- **Problem:** Statistik tampil `0%` dan query menggunakan status lowercase.
- **Fix:** 
    - Mengupdate query `selectRaw` agar menggunakan terminology Sentence Case (`Hadir`, `Telat`).
    - Memperbaiki rumus persentase kehadiran: `(Hadir + Telat) / Total Jadwal`.
- **Status:** ⚠️ Masih memerlukan verifikasi login manual. (Seeder sudah diperbaiki untuk mengisi data `rizki@sekolah.sch.id`, namun automation tools terkendala pada input form login).

### 4. Akses Sesi Dosen (DosenSessionController)
- **Problem:** Dosen terkena 403 atau tidak melihat jadwal di "Mata Kuliah Saya" jika tidak ada di tabel `mata_kuliah_dosen_assignments`.
- **Fix:** Mengupdate method `assignedSubjectIds` di `DosenSessionController` untuk menggabungkan subject dari tabel assignment DAN subject dari tabel `schedules` yang memiliki `teacher_id` dosen terkait.
- **Status:** ✅ Terverifikasi Berhasil.

### 5. Seeding Database
- **Perubahan:** `DatabaseSeeder.php` telah diperbarui untuk memastikan:
    - User Testing (`guru1`, `rizki`) mendapatkan data absensi yang valid di bulan berjalan (April 2026).
    - Semua status menggunakan format Sentence Case.

---

## 📋 Tugas Untuk Agent VS (Next Actions)

### 1. Verifikasi Dashboard Siswa
Lakukan login manual sebagai:
- **Email:** `rizki@sekolah.sch.id`
- **Password:** `siswa123`
Pastikan persentase kehadiran tampil (seharusnya > 0% jika seeder berjalan benar).

### 2. Sinkronisasi Route Prefixes (Penting!)
Saat ini masih terdapat campuran antara `/dosen/` dan `/teacher/` di beberapa sidebar link dan controller. 
- Disarankan untuk menyeragamkan semuanya ke `/teacher/` agar sesuai dengan middleware `role:teacher`.
- Pastikan tidak ada `redirect` loop antara `/dosen/dashboard` dan `/teacher/dashboard`.

### 3. Pembersihan (Cleanup)
- Hapus file-file testing lama (`docs/UI_TEST_REPORT_...`) jika sudah tidak relevan.
- Pastikan semua file `.env` dan `config` sudah sesuai dengan environment lokal user.

---

## 🚀 Cara Menjalankan Ulang
Untuk memastikan kondisi database paling bersih dan sesuai report ini, jalankan:
```bash
php artisan migrate:fresh --seed
```

---
*Laporan ini dibuat untuk memudahkan proses perbaikan selanjutnya.*
