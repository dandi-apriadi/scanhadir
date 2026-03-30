# ScanHadir - Laporan Temuan dan Perbaikan Prioritas

Dokumen ini merangkum temuan yang sudah divalidasi langsung terhadap kode saat ini, beserta perbaikan yang perlu dikerjakan.

## Ringkasan Eksekutif

Status aplikasi secara umum sudah berjalan, tetapi ada gap besar pada modul Master Data Admin:

1. Tombol aksi penting masih berupa placeholder alert.
2. Endpoint CRUD untuk Guru, Siswa, dan Kelas belum tersedia di routing.
3. Metode CRUD terkait juga belum tersedia di controller untuk ketiga modul tersebut.

Temuan lama terkait Alpine.js multiple instance tidak ditemukan bukti saat validasi file saat ini.

## Prioritas 1 - Blokir Operasional (Wajib)

### 1. Placeholder CRUD pada Master Data

#### Guru
- File: resources/views/admin/master/guru.blade.php
- Kondisi saat ini:
  - Tombol Tambah Guru masih alert placeholder.
  - Tombol Edit Guru masih alert placeholder.
  - Tombol Hapus Guru masih alert placeholder.
- Dampak: admin tidak bisa melakukan manajemen data guru dari UI.

#### Siswa
- File: resources/views/admin/master/siswa.blade.php
- Kondisi saat ini:
  - Tombol Tambah Siswa masih alert placeholder.
  - Tombol Edit Siswa masih alert placeholder.
  - Tombol Hapus Siswa masih alert placeholder.
  - Aksi Filter Lanjutan dan Ekspor juga masih placeholder.
- Dampak: alur operasional data siswa belum lengkap.

#### Kelas
- File: resources/views/admin/master/kelas.blade.php
- Kondisi saat ini:
  - Tombol Tambah Kelas masih alert placeholder.
  - Tombol Edit Kelas masih alert placeholder.
  - Tombol Hapus Kelas masih alert placeholder.
- Dampak: manajemen kelas tidak bisa dilakukan dari halaman admin.

### 2. Gap Route dan Controller untuk CRUD Master Data

- File: routes/web.php
- Kondisi saat ini:
  - Route master untuk guru, siswa, kelas baru tersedia versi GET (list halaman).
  - Belum ada route POST, PUT, DELETE untuk guru, siswa, kelas.

- File: app/Http/Controllers/DashboardController.php
- Kondisi saat ini:
  - Sudah ada method read/list: masterGuru, masterSiswa, masterKelas.
  - Belum ada method create/update/delete untuk guru, siswa, kelas.

### Rekomendasi Implementasi Prioritas 1

1. Tambah endpoint CRUD di grup route admin.master untuk guru, siswa, kelas.
2. Tambah method create/update/delete terkait di DashboardController atau pindahkan ke controller khusus per entitas.
3. Ganti alert placeholder menjadi:
   - submit ke form modal (Blade/Livewire), atau
   - navigasi ke halaman form create/edit.
4. Tambahkan validasi dan proteksi relasi:
   - Siswa wajib punya relasi user.
   - Hapus kelas harus aman terhadap relasi siswa/jadwal.
   - Hapus guru harus aman terhadap relasi jadwal/kelas.

## Prioritas 2 - Peningkatan Fungsional

### 1. Pengaturan Sistem (Upload Logo belum aktif)

- File: resources/views/admin/settings.blade.php
- Kondisi saat ini:
  - Form pengaturan utama sudah submit ke endpoint update.
  - Aksi upload logo masih placeholder alert.
- Dampak: branding sekolah belum bisa dikelola penuh dari UI.

### 2. Scanner QR perlu uji perangkat nyata

- File: resources/views/admin/scanner.blade.php
- File: app/Http/Controllers/DashboardController.php
- Kondisi saat ini:
  - UI scanner dan endpoint scanAttendance sudah ada.
  - Perlu pengujian real device untuk memastikan stabilitas kamera dan hasil scan berulang.

### 3. Verifikasi performa laporan kehadiran

- File: app/Livewire/AttendanceReports.php
- Kondisi saat ini:
  - Filter tanggal, kelas, status, pencarian, sorting, dan export sudah tersedia.
  - Perlu uji pada dataset besar untuk memastikan performa query dan pagination.

## Catatan Validasi

1. Klaim duplikasi Alpine.js tidak tervalidasi sebagai masalah aktif pada kondisi file saat ini.
2. Fokus perbaikan sebaiknya dipindah dari isu kosmetik umum ke penyelesaian CRUD Master Data terlebih dahulu.

## Akun Uji

1. Admin: admin@scanhadir.com / admin123
2. Teacher: guru1@sekolah.sch.id / guru123
3. Student: rizki@sekolah.sch.id / siswa123

Dokumen diperbarui pada 2026-03-30 berdasarkan validasi kode aktual.
