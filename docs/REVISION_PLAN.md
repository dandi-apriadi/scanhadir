# 📋 Pemetaan Perbedaan & Rencana Revisi: scanhadir → IOT-Attendance

> **Tujuan:** Menyelaraskan sistem backend `scanhadir` agar 99% sama dengan `IOT-Attendance`, tanpa mengubah tema desain frontend.

---

## 🔍 RINGKASAN PERBEDAAN UTAMA

| Aspek | IOT-Attendance (Referensi) | scanhadir (Perlu Revisi) |
|-------|---------------------------|--------------------------|
| **Domain** | Perguruan Tinggi (Politeknik) | Sekolah (SMA/SMK) |
| **Entitas Utama** | Mahasiswa, Mata Kuliah, Kelas, Jadwal, Semester Akademik | Siswa, Subject, Kelas, Jadwal, Holiday |
| **Pengelompokan Jadwal** | Per Semester Akademik + Mata Kuliah + Dosen | Per Kelas + Subject + Guru (tanpa semester) |
| **Penugasan Dosen** | `mata_kuliah_dosen_assignments` (1 mata kuliah = 1 dosen) | `class_teacher` (many-to-many: guru ↔ kelas) |
| **Absensi** | Tercatat per `jadwal_id` (sesi kuliah spesifik) | Tercatat per `date` (harian, tanpa relasi jadwal) |
| **Status Kehadiran** | Hadir, Telat, Sakit, Izin, Alpa | present, late, sick, excused, absent |
| **Metode Absensi** | RFID, Fingerprint, Face Recognition, Barcode | QR Code scan |
| **Session Management** | Cache-based `active_attendance_session` dengan auto-open/close | Tidak ada session management |
| **Semester** | `semester_akademik` table dengan periode aktif | Tidak ada konsep semester |
| **Correction System** | Permohonan koreksi status kehadiran dengan approval workflow | Approval untuk sick/excused saja |
| **Monitoring** | Live monitoring real-time dengan polling | Tidak ada live monitoring |
| **Performance Metrics** | Tracking performa endpoint API | Tidak ada |
| **Audit Logs** | Log aktivitas pengguna | Tidak ada |
| **Devices** | Manajemen perangkat IoT | Tidak ada |
| **Reporting** | Per mata kuliah, per semester, per mahasiswa | Per tanggal, per kelas |

---

## 📊 PERBEDAAN DETAIL PER KOMPONEN

### 1. DATABASE SCHEMA

#### Tabel yang ADA di IOT-Attendance tapi TIDAK ADA di scanhadir:

| Tabel | Fungsi | Prioritas |
|-------|--------|-----------|
| `semester_akademik` | Periode semester akademik (Ganjil/Genap) | 🔴 KRITIS |
| `mata_kuliah_dosen_assignments` | Penugasan dosen ke mata kuliah (1:1) | 🔴 KRITIS |
| `devices` | Manajemen perangkat IoT | 🟡 Sedang (bisa di-skip) |
| `device_enrollment_jobs` | Job pendaftaran biometrik | 🟡 Sedang (bisa di-skip) |
| `corrections` | Permohonan koreksi kehadiran | 🟡 Sedang |
| `audit_logs` | Log aktivitas pengguna | 🟢 Rendah |
| `performance_metrics` | Metrik performa API | 🟢 Rendah |

#### Tabel yang PERLU DIMODIFIKASI di scanhadir:

| Tabel | Perubahan yang Diperlukan |
|-------|--------------------------|
| `schedules` | Tambah kolom `semester_akademik_id` |
| `subjects` | Tambah kolom `semester_akademik_id`, `sks` |
| `attendances` | Tambah kolom `schedule_id` (FK ke schedules), `metode_absensi`, ubah enum status ke Bahasa Indonesia |
| `students` | Tambah kolom `rfid_uid`, `fingerprint_data`, `face_model_data`, `barcode_id` (opsional untuk kompatibilitas) |
| `users` | Ubah role dari `teacher` → `dosen`, tambah relasi ke `mata_kuliah_dosen_assignments` |

#### Tabel yang SUDAH COCOK (hanya rename):

| scanhadir | IOT-Attendance | Catatan |
|-----------|---------------|---------|
| `classes` | `kelas` | Hanya nama tabel |
| `students` | `mahasiswa` | Hanya nama tabel |
| `subjects` | `mata_kuliah` | Hanya nama tabel |
| `schedules` | `jadwal` | Hanya nama tabel |
| `attendances` | `absensi` | Hanya nama tabel |

---

### 2. MODEL & RELASI

#### IOT-Attendance (Referensi):
```
User (dosen) ──< MataKuliahDosenAssignment ──> MataKuliah
                                          └──< Jadwal ──< Absensi
Kelas ──< Mahasiswa ──< Absensi
SemesterAkademik ──< MataKuliah
SemesterAkademik ──< Jadwal
```

#### scanhadir (Saat Ini):
```
User (teacher) ──< class_teacher (M:N) ──> StudentClass
                                          └──< Schedule ── (tidak terhubung ke Attendance)
StudentClass ──< Student ──< Attendance (per date, tanpa schedule_id)
```

#### Perubahan yang Diperlukan:
1. **Hapus** relasi many-to-many `class_teacher` (guru ↔ kelas)
2. **Buat** relasi `mata_kuliah_dosen_assignments` (1 mata kuliah = 1 dosen)
3. **Tambah** `schedule_id` di tabel `attendances`
4. **Tambah** model `SemesterAkademik`
5. **Ubah** relasi `Schedule` agar terhubung ke `SemesterAkademik`

---

### 3. BUSINESS LOGIC

#### A. Pengelompokan Jadwal Dosen

**IOT-Attendance:**
- Jadwal dikelompokkan per **Semester Akademik** → **Mata Kuliah** → **Kelas**
- Dosen hanya melihat mata kuliah yang di-assign ke mereka via `mata_kuliah_dosen_assignments`
- Auto-open session: jika jadwal sedang dalam waktu aktif, session otomatis dibuka
- Auto-close session: jika waktu jadwal sudah lewat, session otomatis ditutup

**scanhadir (Saat Ini):**
- Jadwal dikelompokkan per **Kelas** saja
- Guru melihat semua kelas yang di-assign via `class_teacher` (many-to-many)
- Tidak ada session management
- Tidak ada auto-open/close

**Yang Perlu Diubah:**
1. Ganti pengelompokan dari per-kelas → per-semester → per-mata-kuliah → per-kelas
2. Implementasi `mata_kuliah_dosen_assignments` untuk penugasan dosen
3. Implementasi session management dengan cache
4. Implementasi auto-open/close berdasarkan waktu jadwal

#### B. Sistem Absensi

**IOT-Attendance:**
- Absensi tercatat per `jadwal_id` (sesi kuliah spesifik)
- Status: Hadir, Telat, Sakit, Izin, Alpa
- Metode: RFID, Fingerprint, Face Recognition, Barcode
- Unique constraint: 1 mahasiswa + 1 jadwal + 1 tanggal = 1 record

**scanhadir (Saat Ini):**
- Absensi tercatat per `date` (harian)
- Status: present, late, sick, excused, absent
- Metode: QR Code saja
- Tidak ada unique constraint per jadwal

**Yang Perlu Diubah:**
1. Tambah `schedule_id` di tabel attendances
2. Ubah enum status ke Bahasa Indonesia
3. Tambah kolom `metode_absensi`
4. Tambah unique constraint per mahasiswa + jadwal + tanggal
5. Ubah logic scanner agar mencatat ke jadwal yang aktif

#### C. Session Management

**IOT-Attendance:**
```php
Cache::put('active_attendance_session', [
    'mata_kuliah_id' => $id,
    'kelas_id' => $id,
    'jadwal_id' => $id,
    'started_at' => now(),
    'user_id' => $userId,
    'source' => 'auto_schedule', // atau 'manual'
], now()->addHours(3));
```

**scanhadir (Saat Ini):** Tidak ada

**Yang Perlu Ditambahkan:**
1. Cache-based session management
2. Auto-open berdasarkan waktu jadwal
3. Auto-close berdasarkan waktu jadwal
4. Manual open/close dari UI

---

### 4. CONTROLLER & ROUTES

#### Routes yang ADA di IOT-Attendance tapi TIDAK ADA di scanhadir:

| Route | Fungsi |
|-------|--------|
| `/dosen/mata-kuliah` | Halaman mata kuliah saya (dosen) |
| `/dosen/schedule/detail` | Detail sesi presensi |
| `/dosen/schedule/start` | Mulai sesi presensi |
| `/dosen/schedule/stop` | Hentikan sesi presensi |
| `/monitoring/live` | Monitoring real-time |
| `/monitoring/live/data` | API data monitoring |
| `/monitoring/health` | Health check perangkat IoT |
| `/monitoring/performance/reports` | Laporan performa |
| `/correction` | Permohonan koreksi kehadiran |
| `/master/semester` | Manajemen semester akademik |
| `/api/absensi` | API endpoint untuk IoT devices |

#### Routes yang PERLU DIMODIFIKASI di scanhadir:

| Route Saat Ini | Perubahan |
|---------------|-----------|
| `/teacher/dashboard` | Ubah logic: tampilkan jadwal per semester + mata kuliah |
| `/teacher/analytics` | Ubah filter: tambah semester + mata kuliah |
| `/teacher/reports` | Ubah filter: tambah semester + mata kuliah |
| `/admin/master/jadwal` | Tambah field: semester_akademik_id |
| `/admin/master/mapel` | Tambah field: semester_akademik_id, sks |
| `/admin/scanner` | Ubah logic: scan ke jadwal aktif, bukan per tanggal |

---

### 5. FITUR YANG PERLU DITAMBAHKAN

#### 🔴 KRITIS (Wajib):

1. **Tabel `semester_akademik`**
   - Migration baru
   - Model `SemesterAkademik`
   - Seeder default semester aktif
   - UI manajemen semester di admin

2. **Tabel `mata_kuliah_dosen_assignments`**
   - Migration baru
   - Model `MataKuliahDosenAssignment`
   - Ubah relasi User → MataKuliah dari many-to-many ke 1:1 via assignment
   - UI penugasan dosen di admin

3. **Kolom `schedule_id` di tabel `attendances`**
   - Migration baru
   - Ubah logic scanner agar mencatat ke jadwal aktif
   - Ubah semua query attendance agar filter by schedule_id

4. **Session Management**
   - Cache-based active session
   - Auto-open/close logic
   - UI untuk start/stop session

5. **Pengelompokan Jadwal per Semester**
   - Ubah semua query jadwal agar group by semester
   - Ubah UI dosen untuk menampilkan jadwal per semester

#### 🟡 SEDANG (Disarankan):

6. **Tabel `corrections`**
   - Migration baru
   - Model `Correction`
   - UI permohonan koreksi
   - Workflow approval

7. **Ubah Status Kehadiran ke Bahasa Indonesia**
   - present → Hadir
   - late → Telat
   - sick → Sakit
   - excused → Izin
   - absent → Alpa

8. **Kolom `metode_absensi` di tabel `attendances`**
   - Default: 'Barcode' (untuk QR code)
   - Siap untuk ekspansi ke RFID/Fingerprint/Face

#### 🟢 RENDAH (Opsional):

9. **Audit Logs**
10. **Performance Metrics**
11. **Device Management** (bisa di-skip karena scanhadir tidak pakai IoT)

---

## 📝 RENCANA IMPLEMENTASI

### Phase 1: Database & Models (Prioritas Tertinggi)

#### Step 1.1: Buat Migration Baru
```
1. create_semester_akademik_table
2. create_mata_kuliah_dosen_assignments_table
3. add_semester_akademik_id_to_subjects_table
4. add_semester_akademik_id_to_schedules_table
5. add_schedule_id_to_attendances_table
6. add_metode_absensi_to_attendances_table
7. create_corrections_table (opsional)
8. create_audit_logs_table (opsional)
```

#### Step 1.2: Buat/Ubah Models
```
1. Buat model SemesterAkademik
2. Buat model MataKuliahDosenAssignment
3. Ubah model Subject → tambah relasi semesterAkademik, dosenAssignment
4. Ubah model Schedule → tambah relasi semesterAkademik
5. Ubah model Attendance → tambah relasi schedule, kolom metode_absensi
6. Ubah model User → hapus assignedClasses, tambah mataKuliahAssignments
7. Hapus model StudentClass (atau rename ke Kelas)
```

#### Step 1.3: Ubah Config
```
1. Buat config/attendance.php (copy dari IOT-Attendance)
2. Definisikan status kehadiran dalam Bahasa Indonesia
3. Definisikan correction statuses
```

### Phase 2: Business Logic

#### Step 2.1: Session Management
```php
// Implementasi di Controller atau Service
- active_attendance_session cache
- auto-open logic (cek jadwal aktif)
- auto-close logic (cek waktu lewat)
- manual start/stop
```

#### Step 2.2: Pengelompokan Jadwal
```php
// Ubah query di semua controller yang menampilkan jadwal
- Group by semester_akademik
- Filter by mata_kuliah_dosen_assignments
- Sort by semester → mata_kuliah → kelas → hari → jam
```

#### Step 2.3: Scanner Logic
```php
// Ubah AttendanceScanner Livewire component
- Scan → cari jadwal aktif → catat ke schedule_id
- Fallback: jika tidak ada jadwal aktif, tampilkan error
- Support metode_absensi = 'Barcode'
```

### Phase 3: Controllers & Routes

#### Step 3.1: Ubah Routes
```php
// routes/web.php
- Tambah route /dosen/mata-kuliah
- Tambah route /dosen/schedule/detail
- Tambah route /dosen/schedule/start
- Tambah route /dosen/schedule/stop
- Tambah route /monitoring/live (opsional)
- Tambah route /master/semester
- Ubah route teacher untuk filter semester
```

#### Step 3.2: Buat Controller Baru
```
1. DosenSessionController (copy dari IOT-Attendance, adaptasi untuk QR)
2. MasterDataController (tambah method semester)
3. CorrectionController (opsional)
4. MonitoringLiveController (opsional)
```

#### Step 3.3: Ubah Controller Existing
```
1. DashboardController → tambah logic semester, session
2. AttendanceAnalytics → tambah filter semester, mata_kuliah
3. AttendanceReports → tambah filter semester, mata_kuliah
4. BulkAttendanceUpdate → ubah logic per schedule, bukan per date
```

### Phase 4: Views (Tanpa Ubah Tema)

#### Step 4.1: Buat View Baru
```
1. dosen/courses.blade.php (mata kuliah saya)
2. dosen/session-detail.blade.php (detail sesi)
3. master/semester.blade.php (manajemen semester)
4. reports/correction.blade.php (opsional)
5. monitoring/live.blade.php (opsional)
```

#### Step 4.2: Ubah View Existing
```
1. teacher dashboard → tampilkan jadwal per semester
2. admin master jadwal → tambah field semester
3. admin master mapel → tambah field semester, sks
4. scanner → tampilkan info jadwal aktif
```

### Phase 5: Testing & Migration Data

#### Step 5.1: Data Migration
```php
// Script untuk migrate data existing
1. Buat semester_akademik default dari data existing
2. Assign mata_kuliah ke dosen berdasarkan schedule existing
3. Link attendance ke schedule berdasarkan date + class
```

#### Step 5.2: Testing
```
1. Test session management (auto-open/close)
2. Test scanner dengan jadwal aktif
3. Test pengelompokan jadwal per semester
4. Test reporting dengan filter semester
5. Test penugasan dosen
```

---

## ⚠️ CATATAN PENTING

1. **Frontend Theme TIDAK Berubah** - Semua perubahan hanya di backend logic, database, dan controller. UI tetap menggunakan tema scanhadir yang sudah ada.

2. **Kompatibilitas QR Code** - Sistem QR code yang sudah ada tetap berfungsi, hanya logic pencatatan yang diubah agar terhubung ke jadwal aktif.

3. **Backward Compatibility** - Selama migration, sistem harus tetap bisa berjalan. Gunakan feature flag atau conditional logic jika diperlukan.

4. **Data Existing** - Data attendance yang sudah ada perlu di-migrate agar terhubung ke schedule yang sesuai.

5. **Naming Convention** - Untuk konsistensi dengan IOT-Attendance, pertimbangkan untuk rename tabel:
   - `classes` → `kelas`
   - `students` → `mahasiswa`
   - `subjects` → `mata_kuliah`
   - `schedules` → `jadwal`
   - `attendances` → `absensi`
   
   **ATAU** tetap gunakan nama English tapi dengan logic yang sama. Pilihan ada di Anda.

---

## 📊 ESTIMASI WORKLOAD

| Phase | Kompleksitas | Estimasi Waktu |
|-------|-------------|----------------|
| Phase 1: Database & Models | Tinggi | 2-3 hari |
| Phase 2: Business Logic | Tinggi | 3-4 hari |
| Phase 3: Controllers & Routes | Sedang | 2-3 hari |
| Phase 4: Views | Rendah | 1-2 hari |
| Phase 5: Testing & Migration | Sedang | 2-3 hari |
| **TOTAL** | | **10-15 hari** |

---

## ✅ CHECKLIST FINAL

- [x] Migration semester_akademik
- [x] Migration mata_kuliah_dosen_assignments
- [x] Migration schedule_id ke attendances
- [x] Migration metode_absensi ke attendances
- [x] Model SemesterAkademik
- [x] Model MataKuliahDosenAssignment
- [x] Ubah model Subject, Schedule, Attendance, User
- [x] Config attendance.php
- [x] Session management (cache-based)
- [x] Auto-open/close logic
- [x] Pengelompokan jadwal per semester
- [x] Scanner logic (schedule-based)
- [x] Routes baru (dosen, monitoring, semester)
- [x] Controller baru (DosenSession, Correction)
- [x] Ubah controller existing (DashboardController)
- [x] View baru (tanpa ubah tema)
- [x] Data migration script (60,000 records migrated)
- [x] Status kehadiran ke Bahasa Indonesia
- [x] Update semua Livewire components
- [ ] Testing menyeluruh (manual)

---

*Dokumen ini dibuat berdasarkan analisis mendalam terhadap kedua project. Semua perubahan dirancang agar sistem scanhadir memiliki logic yang 99% sama dengan IOT-Attendance, sambil mempertahankan tema frontend yang berbeda.*
