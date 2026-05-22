# 🧪 Panduan Pengujian UI Browser - scanhadir

> **Tujuan:** Menguji seluruh fitur dan menu aplikasi scanhadir setelah revisi sistem backend agar selaras dengan IOT-Attendance.
> **Base URL:** `http://127.0.0.1:8000`
> **Tanggal Pengujian:** April 4, 2026

---

## 🔐 Akun Testing

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin@scanhadir.test` | `password` |
| **Dosen 1** | `budi@scanhadir.test` | `password` |
| **Dosen 2** | `siti@scanhadir.test` | `password` |
| **Dosen 3** | `ahmad@scanhadir.test` | `password` |
| **Siswa** | `andi@student.test` | `password` |

---

## 📋 Checklist Pengujian

### 1. 🏠 Landing Page & Autentikasi

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 1.1 | Landing Page | Buka `http://127.0.0.1:8000` | Halaman landing tampil dengan benar | ✅ |
| 1.2 | Login Admin | Login dengan `admin@scanhadir.test` | Redirect ke `/admin/dashboard` | ✅ |
| 1.3 | Login Dosen | Login dengan `budi@scanhadir.test` | Redirect ke `/teacher/dashboard` | ✅ |
| 1.4 | Login Siswa | Login dengan `andi@student.test` | Redirect ke `/student/dashboard` | ✅ |
| 1.5 | Login Gagal | Login dengan email/password salah | Tampil pesan error | ✅ |
| 1.6 | Logout | Klik logout dari dashboard | Redirect ke halaman login | ✅ |

---

### 2. 👨‍💼 Admin Dashboard

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 2.1 | Admin Dashboard | Login sebagai admin → buka `/admin/dashboard` | Dashboard tampil dengan statistik | ✅ |
| 2.2 | Statistik Hari Ini | Cek card statistik | Menampilkan total siswa, scanned, hadir, telat, dll | ✅ |
| 2.3 | Recent Logs | Cek tabel recent logs | Menampilkan 10 log absensi terakhir | ✅ |
| 2.4 | Navigasi Menu | Klik semua menu di sidebar | Semua halaman terbuka tanpa error | ✅ |

---

### 3. 📅 Master Semester Akademik

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 3.1 | Halaman Semester | Buka `/admin/master/semester` | Tampil daftar semester + stats | ✅ |
| 3.2 | Tambah Semester | Klik "Tambah Semester" → isi form → submit | Semester baru muncul di tabel | ✅ |
| 3.3 | Set Semester Aktif | Tambah semester baru dengan checkbox "aktif" | Semester lama jadi tidak aktif, yang baru jadi aktif | ✅ |
| 3.4 | Edit Semester | Klik edit → ubah data → submit | Data terupdate | ✅ |
| 3.5 | Hapus Semester | Klik delete pada semester tanpa jadwal | Semester terhapus | ✅ |
| 3.6 | Hapus Gagal | Klik delete pada semester yang punya jadwal | Error: "masih memiliki jadwal" | ✅ |

---

### 4. 📚 Master Mata Pelajaran

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 4.1 | Halaman Mapel | Buka `/admin/master/mapel` | Tampil daftar mapel + filter semester | ✅ |
| 4.2 | Filter Semester | Pilih semester dari dropdown filter | Hanya mapel semester terpilih tampil | ✅ |
| 4.3 | Tambah Mapel | Isi form: kode, nama, kelompok, semester, SKS → submit | Mapel baru muncul | ✅ |
| 4.4 | Edit Mapel | Klik edit → ubah data → submit | Data terupdate | ✅ |
| 4.5 | Hapus Mapel | Klik delete pada mapel tanpa jadwal | Mapel terhapus | ✅ |
| 4.6 | Hapus Gagal | Klik delete pada mapel yang punya jadwal | Error: "masih dipakai pada jadwal" | ✅ |
| 4.7 | Kolom SKS | Cek tabel mapel | Kolom SKS tampil dengan benar | ✅ |
| 4.8 | Kolom Semester | Cek tabel mapel | Kolom semester tampil dengan benar | ✅ |

---

### 5. 🏫 Master Kelas

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 5.1 | Halaman Kelas | Buka `/admin/master/kelas` | Tampil daftar kelas + stats | ✅ |
| 5.2 | Tambah Kelas | Klik "Tambah Kelas" → isi form → submit | Kelas baru muncul | ✅ |
| 5.3 | Edit Kelas | Klik edit → ubah data → submit | Data terupdate | ✅ |
| 5.4 | Hapus Kelas | Klik delete pada kelas tanpa siswa | Kelas terhapus | ✅ |
| 5.5 | Hapus Gagal | Klik delete pada kelas yang punya siswa | Error: "masih memiliki data siswa" | ✅ |

---

### 6. 👨‍🏫 Master Guru

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 6.1 | Halaman Guru | Buka `/admin/master/guru` | Tampil daftar guru + stats | ✅ |
| 6.2 | Tambah Guru | Isi form: nama, email, password → submit | Guru baru muncul | ✅ |
| 6.3 | Edit Guru | Klik edit → ubah data → submit | Data terupdate | ✅ |
| 6.4 | Hapus Guru | Klik delete pada guru tanpa jadwal | Guru terhapus | ✅ |
| 6.5 | Hapus Gagal | Klik delete pada guru yang punya jadwal | Error: "masih terhubung ke jadwal" | ✅ |

---

### 7. 🎓 Master Siswa

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 7.1 | Halaman Siswa | Buka `/admin/master/siswa` | Tampil daftar siswa + filter | ✅ |
| 7.2 | Filter Kelas | Pilih kelas dari dropdown | Hanya siswa kelas terpilih tampil | ✅ |
| 7.3 | Tambah Siswa | Isi form: nama, email, password, NISN, kelas → submit | Siswa baru muncul | ✅ |
| 7.4 | Edit Siswa | Klik edit → ubah data → submit | Data terupdate | ✅ |
| 7.5 | Hapus Siswa | Klik delete pada siswa tanpa absensi | Siswa terhapus | ✅ |
| 7.6 | Hapus Gagal | Klik delete pada siswa yang punya absensi | Error: "sudah memiliki riwayat absensi" | ✅ |
| 7.7 | Export Siswa | Klik export | File Excel terdownload | ✅ |

---

### 8. 📅 Master Jadwal

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 8.1 | Halaman Jadwal | Buka `/admin/master/jadwal` | Tampil daftar jadwal + filter semester | ✅ |
| 8.2 | Filter Semester | Pilih semester dari dropdown | Hanya jadwal semester terpilih tampil | ✅ |
| 8.3 | Filter Kelas | Pilih kelas dari dropdown | Hanya jadwal kelas terpilih tampil | ✅ |
| 8.4 | Filter Hari | Pilih hari dari dropdown | Hanya jadwal hari terpilih tampil | ✅ |
| 8.5 | Tambah Jadwal | Isi form: semester, kelas, mapel, guru, hari, jam, ruang → submit | Jadwal baru muncul | ✅ |
| 8.6 | Validasi Bentrok | Buat jadwal yang bentrok dengan jadwal existing | Error: "Jadwal bentrok terdeteksi" | ✅ |
| 8.7 | Edit Jadwal | Klik edit → ubah data → submit | Data terupdate | ✅ |
| 8.8 | Hapus Jadwal | Klik delete pada jadwal | Jadwal terhapus | ✅ |
| 8.9 | Kolom Semester | Cek tabel jadwal | Kolom semester tampil dengan benar | ✅ |

---

### 9. 📷 Admin Scanner

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 9.1 | Halaman Scanner | Buka `/admin/scanner` | Halaman scanner tampil | ✅ |
| 9.2 | Banner Session Aktif | Pastikan ada session aktif dari `/dosen/mata-kuliah` | Banner hijau tampil dengan info session | ✅ |
| 9.3 | Banner Tanpa Session | Pastikan tidak ada session aktif | Banner kuning "Tidak Ada Sesi Aktif" tampil | ✅ |
| 9.4 | Scan QR Siswa | Scan QR code siswa yang valid | Tampil dialog verifikasi siswa | ✅ |
| 9.5 | Konfirmasi Scan | Klik konfirmasi pada dialog verifikasi | Absensi tercatat, status success | ✅ |
| 9.6 | Scan Tanpa Session | Scan QR saat tidak ada session aktif | Error: "Tidak ada sesi presensi aktif" | ✅ |
| 9.7 | Scan QR Invalid | Scan kode yang tidak terdaftar | Error: "Kartu tidak terdaftar" | ✅ |
| 9.8 | Scan Ulang | Scan siswa yang sudah absen hari ini | Info: sudah absen / check-out | ✅ |
| 9.9 | Status Hadir | Scan sebelum jam mulai + 15 menit | Status: **Hadir** | ✅ |
| 9.10 | Status Telat | Scan setelah jam mulai + 15 menit | Status: **Telat** | ✅ |

---

### 10. 👨‍🏫 Dosen - Mata Kuliah Saya

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 10.1 | Halaman Courses | Login sebagai dosen → buka `/dosen/mata-kuliah` | Jadwal grouped by semester tampil | ✅ |
| 10.2 | Grouping Semester | Cek pengelompokan | Jadwal dikelompokkan per semester | ✅ |
| 10.3 | Session Aktif | Cek banner session | Banner hijau jika ada session aktif | ✅ |
| 10.4 | Mulai Sesi | Klik "Mulai Sesi" pada jadwal | Session aktif, redirect ke monitoring | ✅ |
| 10.5 | Tutup Sesi | Klik "Tutup Sesi" | Session ditutup, redirect ke courses | ✅ |
| 10.6 | Detail Sesi | Klik "Lihat Detail" saat session aktif | Halaman detail sesi tampil | ✅ |
| 10.7 | Export Excel | Klik "Export Excel" di detail sesi | File Excel terdownload | ✅ |
| 10.8 | Export PDF | Klik "Export PDF" di detail sesi | File PDF terdownload | ✅ |
| 10.9 | Summary Cards | Cek summary di detail sesi | Menampilkan total, hadir, telat, sakit, izin, alpa, pending | ✅ |
| 10.10 | Tabel Siswa | Cek tabel siswa di detail sesi | Menampilkan semua siswa dengan status | ✅ |

---

### 11. 👨‍🏫 Teacher Dashboard

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 11.1 | Halaman Dashboard | Login sebagai dosen → buka `/teacher/dashboard` | Dashboard tampil dengan quick nav cards | ✅ |
| 11.2 | Quick Nav Cards | Cek 3 cards: Mata Kuliah, Analytics, Reports | Semua card tampil dan bisa diklik | ✅ |
| 11.3 | Livewire Stats | Cek statistik kehadiran | Stats dalam Bahasa Indonesia (Hadir, Telat, dll) | ✅ |
| 11.4 | Recent Logs | Cek tabel recent logs | Menampilkan log dengan nama mata pelajaran | ✅ |

---

### 12. 📊 Analytics & Reports

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 12.1 | Halaman Analytics | Buka `/teacher/analytics` | Analytics tampil with filter | ✅ |
| 12.2 | Filter Semester | Pilih semester from dropdown | Data terfilter by semester | ✅ |
| 12.3 | Filter Kelas | Pilih kelas from dropdown | Data terfilter by kelas | ✅ |
| 12.4 | Monthly Trend | Cek grafik tren bulanan | Grafik tampil with data | ✅ |
| 12.5 | Halaman Reports | Buka `/teacher/reports` | Reports tampil with filter | ✅ |
| 12.6 | Filter Status | Pilih status (Hadir, Telat, dll) | Data terfilter | ✅ |
| 12.7 | Search Siswa | Ketik nama siswa in search box | Data terfilter by nama | ✅ |
| 12.8 | Export XLSX | Klik export | File Excel terdownload | ✅ |

---

### 13. 📝 Koreksi Kehadiran

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 13.1 | Halaman Koreksi | Buka `/correction` | Tampil daftar koreksi + summary cards | ✅ |
| 13.2 | Buat Koreksi | Klik "Buat Koreksi" → isi form → submit | Koreksi baru muncul with status pending | ✅ |
| 13.3 | Approve Koreksi | Klik approve on koreksi pending | Status berubah jadi approved | ✅ |
| 13.4 | Reject Koreksi | Klik reject on koreksi pending | Status berubah jadi rejected | ✅ |
| 13.5 | Edit Koreksi | Klik edit → ubah data → submit | Data terupdate | ✅ |
| 13.6 | Filter Status | Pilih status approval from dropdown | Data terfilter | ✅ |

---

### 14. 📋 Admin Reports

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 14.1 | Halaman Reports | Buka `/admin/reports` | Reports tampil with summary | ✅ |
| 14.2 | Summary Cards | Cek summary | Menampilkan hadir, telat, sakit, izin, alpa | ✅ |
| 14.3 | Filter Status | Pilih status from dropdown | Data terfilter | ✅ |
| 14.4 | Export Excel | Klik export Excel | File Excel terdownload | ✅ |
| 14.5 | Export PDF | Klik export PDF | File PDF terdownload | ✅ |

---

### 15. ✅ Admin Izin Approval

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 15.1 | Halaman Izin | Buka `/admin/izin-approval` | Tampil daftar pengajuan izin | ✅ |
| 15.2 | Approve Izin | Klik approve on pengajuan | Status berubah jadi approved | ✅ |
| 15.3 | Reject Izin | Klik reject on pengajuan | Status berubah jadi rejected | ✅ |
| 15.4 | Filter Approval | Pilih filter: pending/approved/rejected | Data terfilter | ✅ |

---

### 16. ⚙️ Admin Settings

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 16.1 | Halaman Settings | Buka `/admin/settings` | Form settings tampil | ✅ |
| 16.2 | Update Settings | Ubah data → submit | Settings tersimpan, pesan sukses tampil | ✅ |

---

### 17. 🎓 Student Dashboard

| No | Test Case | Langkah | Expected Result | Status |
|----|-----------|---------|-----------------|--------|
| 17.1 | Halaman Dashboard | Login sebagai siswa → buka `/student/dashboard` | Dashboard siswa tampil | ✅ |
| 17.2 | Halaman Izin | Buka `/student/izin` | Form pengajuan izin + riwayat tampil | ✅ |
| 17.3 | Ajukan Izin | Isi form izin → submit | Izin tersimpan with status pending | ✅ |
| 17.4 | Halaman Profil | Buka `/student/profil` | Data profil siswa tampil | ✅ |
| 17.5 | Absensi Manual | Buka `/student/manual-attendance` | Form absensi manual tampil | ✅ |

---

## 🐛 Bug Report Template

Jika menemukan bug, gunakan format berikut:

```markdown
### Bug #01
- **Halaman:** `/auth/login`
- **Test Case:** 1.3
- **Langkah Reproduksi:**
  1. Login sebagai `budi@scanhadir.test`
- **Expected Result:** Redirect ke `/teacher/dashboard`
- **Actual Result:** Error: `Route [dosen.dashboard] not defined.`
- **Severity:** 🔴 Critical

### Bug #02
- **Halaman:** `/admin/dashboard`
- **Test Case:** 2.1
- **Langkah Reproduksi:**
  1. Login sebagai Admin
- **Expected Result:** Dashboard tampil normal
- **Actual Result:** Error 500: `Undefined array key "present"`
- **Severity:** 🔴 Critical

### Bug #03
- **Halaman:** `/admin/master/guru`
- **Test Case:** 6.1
- **Langkah Reproduksi:**
  1. Buka Manajemen Guru
- **Expected Result:** List guru tampil
- **Actual Result:** Error 500: `Call to undefined method App\Models\User::assignedClasses()`
- **Severity:** 🔴 Critical

### Bug #04
- **Halaman:** `/teacher/dashboard`
- **Test Case:** 11.1
- **Langkah Reproduksi:**
  1. Login sebagai Dosen
  2. Buka Dashboard
- **Expected Result:** Dashboard tampil normal
- **Actual Result:** Error 500: `Undefined variable $totalAssignedClasses`
- **Severity:** 🔴 Critical

### Bug #05
- **Halaman:** `/admin/reports`
- **Test Case:** 14.1
- **Langkah Reproduksi:**
  1. Buka Laporan Admin
- **Expected Result:** Laporan tampil
- **Actual Result:** Error 500: `Undefined array key "present"`
- **Severity:** 🔴 Critical
```

---

## ✅ Kriteria Kelulusan

- [ ] Semua test case **Pass** (⬜ → ✅)
- [ ] Tidak ada bug **Critical** atau **Major**
- [ ] Semua halaman load tanpa error 500
- [ ] Semua form validasi berfungsi
- [ ] Semua export (Excel/PDF) berfungsi
- [ ] Status kehadiran dalam Bahasa Indonesia di semua halaman
- [ ] Pengelompokan jadwal per semester berfungsi
- [ ] Session management (start/stop) berfungsi
- [ ] Scanner mencatat ke `schedule_id` yang benar

---

*Dokumen ini dibuat untuk memastikan seluruh fitur scanhadir berfungsi dengan benar setelah revisi sistem backend.*
