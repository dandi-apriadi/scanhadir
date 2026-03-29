# ScanHadir - Dokumen Rancangan Sistem

## 1. Tujuan Sistem
ScanHadir dirancang sebagai sistem presensi siswa berbasis web dengan QR Code untuk meningkatkan kecepatan pencatatan hadir, akurasi data, dan transparansi monitoring kehadiran oleh pihak sekolah.

## 2. Ruang Lingkup

### 2.1 In Scope
- Manajemen data siswa, kelas, dan hari libur.
- Pencatatan presensi siswa berbasis QR.
- Login dan dashboard siswa.
- Monitoring data presensi melalui panel admin.
- Rekap dan kesiapan ekspor laporan.

### 2.2 Out of Scope (Tahap Saat Ini)
- Integrasi pembayaran atau akademik non-presensi.
- Integrasi biometrik (sidik jari/wajah).
- Mobile app native.

## 3. Aktor dan Hak Akses

### 3.1 Admin Sekolah
- Mengelola master data (siswa, kelas, kalender libur).
- Memantau statistik dan histori presensi.
- Melakukan validasi data dan pembuatan laporan.

### 3.2 Siswa
- Login ke sistem.
- Melakukan proses scan presensi.
- Melihat riwayat kehadiran pribadi.

## 4. Arsitektur Aplikasi

### 4.1 Teknologi
- Backend: Laravel 11 (PHP 8.2+)
- UI Interaktif: Livewire
- Admin Panel: Filament 3
- Styling: Tailwind CSS + Vite
- Database: MySQL/MariaDB
- QR Code: simplesoftwareio/simple-qrcode

### 4.2 Komponen Inti
- Komponen scan kehadiran pada Livewire.
- Komponen autentikasi siswa pada Livewire.
- Dashboard siswa untuk ringkasan presensi.
- Resource Filament untuk data operasional sekolah.

## 5. Alur Proses Bisnis

1. Siswa masuk ke halaman login.
2. Siswa terautentikasi dan mengakses fitur scan.
3. Sistem memvalidasi data siswa dan konteks tanggal/jam.
4. Data kehadiran disimpan ke tabel presensi.
5. Siswa dan admin dapat melihat hasil presensi secara near real-time.

## 6. Model Data (Konseptual)

- `students`: Menyimpan profil siswa.
- `classes`: Menyimpan data kelas.
- `attendances`: Menyimpan transaksi kehadiran.
- `holidays`: Menyimpan tanggal non-sekolah/libur.
- `users`: Menyimpan akun autentikasi.

Relasi utama:
- Satu kelas memiliki banyak siswa.
- Satu siswa memiliki banyak catatan kehadiran.

## 7. Kebutuhan Non-Fungsional

### 7.1 Performa
- Waktu respon halaman utama ditargetkan < 2 detik pada jaringan lokal sekolah.

### 7.2 Keamanan
- Validasi input di sisi server.
- Proteksi route menggunakan middleware autentikasi.
- Pengelolaan sesi sesuai standar Laravel.

### 7.3 Reliabilitas
- Gunakan backup database berkala.
- Logging aplikasi aktif untuk audit masalah operasional.

## 8. Rencana Pengembangan

1. Finalisasi requirement dan kebijakan presensi sekolah.
2. Penyempurnaan UX alur scan dan dashboard.
3. Implementasi laporan PDF/Excel lebih lengkap.
4. Hardening keamanan dan uji beban ringan.
5. Deployment ke server produksi dan SOP operasional.

## 9. Risiko dan Mitigasi

- Risiko: QR tidak terbaca karena kualitas kamera/perangkat.
	Mitigasi: Sediakan fallback absensi manual oleh admin.

- Risiko: Duplikasi scan pada waktu berdekatan.
	Mitigasi: Terapkan validasi presensi per siswa per sesi waktu.

- Risiko: Koneksi internet tidak stabil.
	Mitigasi: Optimasi aplikasi intranet lokal sekolah.

## 10. Indikator Keberhasilan

- Waktu proses absensi lebih cepat dibanding metode manual.
- Penurunan kesalahan input data presensi.
- Admin dapat mengakses rekap kehadiran harian dengan mudah.
- Siswa dapat melihat riwayat hadir secara mandiri.
