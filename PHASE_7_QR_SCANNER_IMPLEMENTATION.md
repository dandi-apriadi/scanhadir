# Phase 7: QR Scanner & Attendance Tracking - Implementation Guide

## 🎯 Overview

Phase 7 implements the core attendance scanning functionality using QR codes. This is the critical operational feature that allows students/teachers to record attendance via QR code scanning.

### Features Implemented

✅ **QR Code Scanner Component**
- Real-time QR code reading from device camera
- HTML5-based scanner (html5-qrcode library)
- Check-in/check-out dual functionality
- Audio/visual feedback

✅ **Attendance Processing Logic**
- Automatic status detection (present vs late)
- Check-in/check-out time tracking
- Holiday validation
- Duplicate scan prevention

✅ **Enhanced UI/UX**
- Real-time scan statistics display
- Separate success overlays for check-in vs check-out
- Live time clock
- Status icons and animations
- Sound feedback (success/error)

✅ **Comprehensive Testing**
- 26+ Feature tests for scanner functionality
- Tests for check-in/check-out logic
- Holiday and validation testing
- Security and edge case testing

---

## 📁 Files Modified/Created

### Backend Components
- **`app/Livewire/AttendanceScanner.php`** (Enhanced)
  - Added detailed status detection (present vs late)
  - Improved check-in/check-out logic
  - Better error handling
  - Scan statistics tracking
  - Configurable time thresholds

- **`resources/views/livewire/attendance-scanner.blade.php`** (Enhanced)
  - Modern dark-theme UI with glassmorphism
  - Separate overlays for check-in and check-out
  - Real-time clock display
  - Scan statistics sidebar
  - Improved visual feedback

### Testing
- **`tests/Feature/AttendanceScannerTest.php`** (New)
  - 26 comprehensive test cases
  - Scanner access control
  - QR validation
  - Check-in/check-out detection
  - Holiday prevention
  - Late time detection
  - Data persistence

---

## 🔧 Configuration

**Late Time Threshold** (configurable in AttendanceScanner.php)
```php
protected const SCHOOL_START_TIME = '07:00:00';      // School starts
protected const LATE_THRESHOLD = '07:30:00';          // Late after this
protected const SCHOOL_END_TIME = '14:00:00';        // School ends
protected const SCAN_THROTTLE_SECONDS = 2;            // Cooldown between scans
```

---

## 🚀 Usage

### For Teachers/Admins (Who operate the scanner)

**1. Navigate to Scanner**
```
http://127.0.0.1:8000/admin/scanner
```

**2. Scanner Interface**
- Position QR code in front of camera
- System automatically detects and processes scan
- Success/error message displays immediately
- Audio feedback confirms action

**3. Check-in Process**
- First scan of the day = **Check-in**
- System records check-in time
- Automatically marks as "present" or "late" based on time

**4. Check-out Process**
- Second scan of the same day = **Check-out**
- System records check-out time
- Calculates session duration

---

## 🔍 How It Works

### Attendance Flow
```
Student QR Scan
    ↓
Validate QR Code Format
    ↓
Find Student in Database
    ↓
Check if Holiday
    ├─ YES → Error: "Hari libur"
    └─ NO ↓
Check Time (Determine Status)
    ├─ Before 07:30 → "present"
    └─ After 07:30 → "late"
    ↓
Check Today's Attendance Exists
    ├─ YES (Check-in exists, check-out empty) → Update Check-out
    │   └─ Success: "Absen Pulang"
    │   └─ Record duration
    │
    └─ NO → Create New Attendance
        └─ Success: "Absen Masuk"
        └─ Record check-in time
```

### Status Detection Logic
```php
function determineStatus($timeString): string {
    $checkInTime = parse($timeString);
    $lateThreshold = parse('07:30:00');
    
    if ($checkInTime > $lateThreshold) {
        return 'late';  // Terlambat
    }
    return 'present';   // Hadir
}
```

---

## 🧪 Testing

### Run Scanner Tests
```bash
php artisan test tests/Feature/AttendanceScannerTest.php
```

### Test Coverage (26 tests)

**Access & Security (3)**
- ✅ Teacher can access scanner
- ✅ Non-teacher cannot access
- ✅ Unauthenticated user redirects to login

**QR Validation (4)**
- ✅ Accepts valid QR code
- ✅ Rejects invalid QR code
- ✅ Handles empty QR code
- ✅ Handles null QR code

**Attendance Recording (5)**
- ✅ Creates attendance record on valid scan
- ✅ Records check-in time
- ✅ Records check-out time
- ✅ Only one attendance per day per student
- ✅ Multiple students can scan

**Check-in/Check-out (4)**
- ✅ Detects check-in on first scan
- ✅ Detects check-out on second scan
- ✅ Shows "Absen Masuk" on first scan
- ✅ Shows "Absen Pulang" on second scan

**Status Detection (2)**
- ✅ Marks late after 07:30
- ✅ Marks present before 07:30

**Holiday & Validation (2)**
- ✅ Prevents scanning on holidays
- ✅ Cannot scan deleted student

**Events & Feedback (4)**
- ✅ Fires success event on valid scan
- ✅ Fires error event on invalid scan
- ✅ Shows student name in success
- ✅ Shows class name in success

**Edge Cases (2)**
- ✅ Different days create different records
- ✅ Increments scan counter

---

## 🎨 UI Components

### Scanner Interface
```
┌─────────────────────────────────────┐
│         ScanHadir                   │
│  Sistem Presensi QR Sekolah Pintar │
│  📍 Tanggal: 29 Mar 2026            │
│  ⏰ 10:30:45                        │
├─────────────────────────────────────┤
│                                     │
│     [QR Scanner Area]               │
│     (Live Camera Feed)              │
│                                     │
├─────────────────────────────────────┤
│  ✓ Absen Masuk Berhasil             │
├─────────────────────────────────────┤
│  Scan Hari Ini: 5                   │
│  Scan Terakhir: 10:30:45            │
└─────────────────────────────────────┘
```

### Success Overlay (Check-in)
```
┌─────────────────────────────┐
│  ✓          (emerald glow)  │
│                             │
│  NAMA SISWA                 │
│  Kelas X-A                  │
│                             │
│  ✓ Absen Masuk Berhasil     │
│  Waktu: 10:30:45            │
└─────────────────────────────┘
```

### Success Overlay (Check-out)
```
┌─────────────────────────────┐
│  ↗          (blue glow)     │
│                             │
│  NAMA SISWA                 │
│  Kelas X-A                  │
│                             │
│  ✓ Absen Pulang Berhasil    │
│  Waktu: 14:30:20            │
└─────────────────────────────┘
```

---

## 📊 Data Model

### Attendance Record
```php
$attendance = [
    'student_id' => 1,
    'date' => '2026-03-29',
    'status' => 'present', // or 'late', 'absent', 'sick', 'excused'
    'check_in' => '07:15:30',
    'check_out' => '14:30:45',
    'notes' => 'Check-out at 14:30:45'
];
```

### Status Values
- `present` - On time (before 07:30)
- `late` - Checked in after 07:30
- `absent` - No check-in (marked by system)
- `sick` - Manual entry (with note)
- `excused` - Manual entry (with reason)

---

## 🔐 Security Features

1. **QR Code Validation**
   - Format validation (13+ characters)
   - Existence check in database

2. **Student Verification**
   - QR must belong to valid student
   - Soft-deleted students cannot scan

3. **Holiday Protection**
   - Scanning blocked on school holidays
   - Holiday dates configured in database

4. **Role-based Access**
   - Only teachers/admins can access scanner
   - Students make scans, don't operate scanner

5. **Time Boundaries**
   - School operating hours enforced
   - Late threshold configurable

---

## 🐛 Error Handling

| Scenario | Error Message | Action |
|----------|--------------|--------|
| Invalid QR Code | "Kartu tidak terdaftar: XXXXX" | Returns to scanner |
| Non-existent Student | "Kartu tidak terdaftar: XXXXX" | Returns to scanner |
| Holiday Date | "Hari libur - Absensi ditutup" | Returns to scanner |
| Already Checked Out | "Sudah absen pulang. Durasi: X jam Y menit" | Info message |
| Scan Cooldown | (Scanner paused for 2s) | Automatic cooldown |
| System Error | "Terjadi kesalahan sistem. Coba lagi." | Logged to error log |

---

## 📱 Browser Compatibility

| Browser | Status | Notes |
|---------|--------|-------|
| Chrome | ✅ Full Support | Recommended |
| Firefox | ✅ Full Support | Works well |
| Safari | ✅ Partial | May need HTTPS for camera |
| Mobile Chrome | ✅ Full Support | Back camera recommended |
| Mobile Safari | ⚠️ Limited | Camera permission required |

---

## 🎯 Next Steps (Phase 8)

After Phase 7 testing is complete:

1. **Mobile Optimization**
   - Landscape mode for tablets
   - Full-screen scanner mode
   - Gesture controls

2. **Advanced Features**
   - Bulk scan history export
   - Daily scan reports
   - Error log management
   - Network fallback (offline scanning)

3. **Teacher Dashboard Integration**
   - Live attendance display
   - Class-specific scanning
   - Attendance verification workflow

4. **Analytics**
   - Scan statistics per teacher
   - Daily scan volume metrics
   - Error rate tracking

---

## 📋 Deployment Checklist

- [ ] Test on production server
- [ ] Verify camera permissions configured
- [ ] Test with actual QR codes
- [ ] Backup scanner method configured
- [ ] Error logs configured
- [ ] Sound assets accessible
- [ ] API rate limiting set
- [ ] Database queries optimized

---

## 🔗 Integration Points

### Routes
```
GET  /admin/scanner        → DashboardController@adminScanner
POST /livewire/message/... → Livewire websocket
```

### Livewire Component
```
attendance-scanner
├── processScan($code)
├── determinStatus($time)
├── processAttendance($student, $date)
└── getScanStats()

Events:
→ scan-success (data: name, class, action)
→ scan-failed ()
→ scan-info (action)
```

### Models
```
Student
├── qr_code
├── user_id
├── class_id
└── attendances

Attendance
├── student_id
├── date
├── status
├── check_in
├── check_out
└── notes

Holiday
├── start_date
├── end_date
├── name
└── type
```

---

## 📈 Performance Metrics

- Scanner load time: < 2 seconds
- Scan processing: < 500ms
- Overlay display: 3.5 seconds
- Cooldown before next scan: 2 seconds

---

## 📚 Documentation Links

- [HTML5 QRCode Library](https://github.com/mebjas/html5-qrcode)
- [Livewire Events](https://livewire.laravel.com/docs/events)
- [Attendance Model](../app/Models/Attendance.php)
- [Feature Tests](../tests/Feature/AttendanceScannerTest.php)

