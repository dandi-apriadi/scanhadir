# ScanHadir - Laporan Status Implementasi Terkini

Dokumen ini merangkum status aktual berdasarkan validasi kode terakhir.

## Ringkasan Eksekutif

Status utama saat ini:

1. CRUD Master Data Admin (Guru, Siswa, Kelas) sudah aktif.
2. Placeholder tombol Add/Edit/Delete pada tiga modul tersebut sudah tidak ada.
3. Kredensial uji di seeder sudah disinkronkan dengan dokumentasi.
4. Redirect `/login` ke `/auth/login` sudah tersedia.
5. Bug Alpine.js pada modal edit jadwal sudah diperbaiki.

## Status Per Modul

### 1. Master Guru
- File: resources/views/admin/master/guru.blade.php
- Status:
  - Tambah Guru: aktif
  - Edit Guru: aktif
  - Hapus Guru: aktif
- Backend terkait:
  - Route CRUD tersedia
  - Validasi dan proteksi relasi delete tersedia

### 2. Master Siswa
- File: resources/views/admin/master/siswa.blade.php
- Status:
  - Tambah Siswa: aktif
  - Edit Siswa: aktif
  - Hapus Siswa: aktif
  - Filter lanjutan: aktif
  - Ekspor: aktif (Excel)
- Backend terkait:
  - Sinkronisasi users + students sudah diterapkan via transaksi DB
  - Proteksi delete jika ada riwayat absensi sudah diterapkan

### 3. Master Kelas
- File: resources/views/admin/master/kelas.blade.php
- Status:
  - Tambah Kelas: aktif
  - Edit Kelas: aktif
  - Hapus Kelas: aktif
- Backend terkait:
  - Proteksi delete jika masih dipakai siswa/jadwal sudah diterapkan

### 4. Master Jadwal (Alpine.js)
- File: resources/views/admin/master/jadwal.blade.php
- Temuan sebelumnya:
  - Error `Cannot read properties of null (reading 'class_id')` pada modal edit.
- Status sekarang:
  - Sudah diperbaiki dengan guard `template x-if="editSchedule"` untuk mencegah binding saat object masih null.

## Routing & Akses

- File: routes/web.php
- Status sekarang:
  - Redirect `/login` -> `/auth/login` tersedia.
  - Redirect legacy `/login/student` dan `/login/admin` tetap tersedia.

## Kredensial Uji (Seeder)

- File: database/seeders/DatabaseSeeder.php
- Status sekarang:
  - Admin: admin@scanhadir.com / admin123
  - Teacher: guru1@sekolah.sch.id / guru123
  - Student: rizki@sekolah.sch.id / siswa123

## Catatan Resource

- Favicon fisik sudah tersedia di public/favicon.ico.
- Asset `public/images/hero.png` tersedia.
- Jika masih ada 404 resource saat run lokal, kemungkinan berasal dari request browser eksternal/third-party, bukan dari aset inti yang divalidasi pada modul utama.

## Prioritas Lanjutan

1. Uji regresi manual pada semua modal CRUD di viewport mobile.
2. Tambahkan test feature khusus bug modal jadwal (guard editSchedule null) agar tidak muncul kembali.
3. Audit aset eksternal (avatar URL pihak ketiga) bila ingin mode offline penuh.

Dokumen diperbarui pada 2026-03-30.
