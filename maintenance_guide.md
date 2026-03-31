# Panduan Pemeliharaan Sistem ScanHadir

Dokumen ini berisi panduan teknis untuk memastikan sistem ScanHadir berjalan optimal dan cara menangani masalah umum.

## 1. Persyaratan Server (PHP Extensions)
Beberapa fitur memerlukan ekstensi PHP tertentu yang mungkin belum aktif secara default di Laragon/Apache.

### **A. Ekstensi GD (Untuk QR Code & Gambar)**
Jika QR Code tidak muncul sebagai PNG atau pecah:
1. Klik kanan pada menu Laragon -> **PHP** -> **Extensions**.
2. Pastikan `gd` atau `php_gd` sudah tercentang.
3. Jika tidak ada di menu, buka file `php.ini` dan hapus tanda titik koma (`;`) di depan `extension=gd`.
4. **Restart Apache/Nginx.**

### **B. Ekstensi Zip (Untuk Ekspor Excel)**
Jika fitur "Export Excel" error (Class 'ZipArchive' not found):
1. Klik kanan pada menu Laragon -> **PHP** -> **Extensions**.
2. Pastikan `zip` atau `php_zip` sudah tercentang.
3. **Restart Apache/Nginx.**

---

## 2. Fitur Unduhan QR Code (Pretty URL)
Sistem ini menggunakan jalur khusus untuk mengunduh QR Code guna menghindari pemblokiran nama file oleh kebijakan browser ("Managed by Organization").

Jalur unduhan: `/qr-download/{nisn}/{filename}.png`

**Catatan Keamanan:**
Jika Anda menggunakan browser Chrome yang dikelola oleh kantor/sekolah, browser mungkin akan tetap mengganti nama file menjadi kode acak (UUID). Solusinya:
- Gunakan browser standar (tidak dikelola organisasi).
- Atau, klik kanan pada gambar QR dan pilih "Save Image As".

---

## 3. Konfigurasi Jam Masuk & Keterlambatan
Pengaturan ini dapat diubah melalui menu **Pengaturan** di dashboard Admin:
- **Jam Masuk**: Waktu standar siswa harus sudah hadir (Contoh: 07:00).
- **Toleransi Keterlambatan**: Tambahan waktu dalam menit sebelum siswa dianggap terlambat (Contoh: 15 menit).
    - Jika Jam Masuk 07:00 dan Toleransi 15 menit, maka siswa yang scan di jam 07:16 akan otomatis ditandai **Terlambat (Late)**.

---

## 4. Reset Sistem & Cache
Jika Anda melakukan perubahan pada file `.env` atau route, jalankan perintah berikut di terminal root project:
```bash
php artisan optimize:clear
```

---

## 5. Checklist Verifikasi Setelah Maintenance
Gunakan checklist ini untuk memastikan semua konfigurasi benar-benar aktif:

1. Verifikasi ekstensi GD dan Zip aktif:
```bash
php -m | findstr /I "gd zip"
```
Output seharusnya menampilkan `gd` dan `zip`.

2. Verifikasi route unduhan QR tersedia:
```bash
php artisan route:list | findstr /I "qr-download"
```

3. Uji unduhan QR dari browser:
- Buka data siswa, klik tombol unduh QR.
- Pastikan file terunduh sebagai gambar (`.png`) dan bisa dibuka normal.

4. Uji ekspor Excel:
- Buka halaman laporan/admin report.
- Klik Export Excel, pastikan file berhasil terunduh tanpa error `ZipArchive`.

---
Dibuat untuk: Sistem Presensi SMK ScanHadir
Versi: 1.2 (Stable)
