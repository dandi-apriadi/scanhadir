# Frontend-Backend Integration Contract

**Version**: 1.0  
**Date**: March 29, 2026  
**Status**: Ready for Frontend Development  

---

## 📋 Overview

Dokumen ini mendefinisikan interface/contract antara frontend dan backend untuk ScanHadir. Frontend team dapat mulai development dengan confidence mengetahui data dan response format yang akan diterima.

---

## 🔐 Authentication

### Login Route
```
POST /login
GET /login (StudentLogin Livewire component)
```

**Request** (Form submission):
```
email: string (required)
password: string (required)
```

**Response** (Success):
- Redirect to `/dashboard` (student) atau `/admin` (admin)
- Session established with `auth()->user()`

**Response** (Error):
- Display validation error message in Livewire component
- Form resets, message shown: "Email atau password salah"

**User Object** after login:
```php
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "role": "student", // or 'admin', 'teacher'
  "email_verified_at": "2026-03-29",
  "created_at": "2026-03-29T12:00:00Z",
  "updated_at": "2026-03-29T12:00:00Z"
}
```

### Logout
```
POST /logout
```

**Response**: Redirect to `/login`

### Validation Error Contract

Backend menggunakan format error terstruktur agar frontend bisa render pesan secara konsisten:

```json
{
  "success": false,
  "code": "VALIDATION_ERROR",
  "message": "Input tidak valid.",
  "errors": {
    "identifier": ["Email atau NISN harus diisi."],
    "password": ["Password minimal 6 karakter."],
    "code": ["Format kode QR tidak valid."],
    "nisn": ["NISN harus terdiri dari 13 digit angka."]
  }
}
```

Catatan implementasi:
- `identifier` digunakan untuk login (email atau NISN).
- `code` digunakan pada proses scan QR.
- `nisn` wajib 13 digit.

---

## 📱 Student Endpoints

### GET /dashboard
**Protected**: Yes (auth middleware)  
**Accessible by**: Students only

**Response** (StudentDashboard Livewire component):
```php
{
  "student": {
    "id": 1,
    "user": {
      "id": 1,
      "name": "Budi Santoso",
      "email": "budi@school.com"
    },
    "nisn": "1234567890123",
    "class": {
      "id": 1,
      "name": "X-A",
      "level": "X",
      "major": "IPA"
    },
    "qr_code": "SH-ABC12345",
    "photo_path": "/storage/students/1/photo.jpg"
  },
  "recentAttendances": [
    {
      "id": 1,
      "date": "2026-03-29",
      "check_in": "07:00:00",
      "check_out": "14:30:00",
      "status": "present",
      "notes": null
    },
    // ... up to 10 records
  ],
  "attendanceStats": {
    "present": 18,
    "late": 2,
    "sick": 1,
    "excused": 1,
    "absent": 0
  }
}
```

---

### GET /scan
**Protected**: Yes (auth middleware)  
**Accessible by**: Students/Teachers

**Page Type**: Livewire AttendanceScanner component  
**Functionality**: 
- Display camera stream for QR scanning
- Handle scan events via JavaScript
- Show success/error messages

**Message States**:
```
status: 'info' | 'success' | 'error'
message: string
```

**Scan Success Response**:
```js
dispatch('scan-success', {
  name: 'Budi Santoso',
  class: 'X-A',
  timestamp: '2026-03-29 07:00:00'
})
```

**Scan Error Response**:
```js
dispatch('scan-failed', {
  message: 'Kartu tidak terdaftar'
})
```

**Attendance Record Created**:
```php
{
  "id": 123,
  "student_id": 1,
  "date": "2026-03-29",
  "check_in": "07:00:00",
  "check_out": null, // Set on second scan
  "status": "present",
  "notes": null,
  "created_at": "2026-03-29T07:00:00Z"
}
```

---

## 🛡️ Admin Panel (/admin)

All Filament resources available at `/admin/[resource-name]`

### Student Resource
```
GET /admin/students - List all students
POST /admin/students - Create student
GET /admin/students/{id}/edit - Edit student form
POST /admin/students/{id} - Update student
DELETE /admin/students/{id} - Delete student
```

**Student Record**:
```php
{
  "id": 1,
  "user_id": 1,
  "class_id": 1,
  "nisn": "1234567890123",
  "qr_code": "SH-ABC12345",
  "photo_path": "/storage/students/1/photo.jpg",
  "user": {
    "id": 1,
    "name": "Budi Santoso",
    "email": "budi@school.com"
  },
  "class": {
    "id": 1,
    "name": "X-A"
  }
}
```

### Attendance Resource
```
GET /admin/attendances - List all attendance
POST /admin/attendances - Create attendance record
GET /admin/attendances/{id}/edit - Edit attendance form
POST /admin/attendances/{id} - Update attendance
DELETE /admin/attendances/{id} - Delete attendance
GET /admin/attendances/export - Export to Excel (coming soon)
```

**Attendance Record**:
```php
{
  "id": 123,
  "student_id": 1,
  "date": "2026-03-29",
  "check_in": "07:00:00",
  "check_out": "14:30:00",
  "status": "present", // present, late, sick, excused, absent
  "notes": null,
  "student": {
    "id": 1,
    "user": { "name": "Budi Santoso" },
    "nisn": "1234567890123"
  }
}
```

### StudentClass Resource
```
GET /admin/student-classes - List all classes
POST /admin/student-classes - Create class
GET /admin/student-classes/{id}/edit - Edit class form
POST /admin/student-classes/{id} - Update class
DELETE /admin/student-classes/{id} - Delete class
```

**Class Record**:
```php
{
  "id": 1,
  "name": "X-A",
  "level": "X", // X, XI, XII
  "major": "IPA", // IPA, IPS, BAHASA
  "students_count": 35,
  "created_at": "2026-03-29T12:00:00Z"
}
```

### Holiday Resource (Coming This Week)
```
GET /admin/holidays - List all holidays
POST /admin/holidays - Create holiday
GET /admin/holidays/{id}/edit - Edit holiday form
POST /admin/holidays/{id} - Update holiday
DELETE /admin/holidays/{id} - Delete holiday
```

**Holiday Record**:
```php
{
  "id": 1,
  "name": "Lebaran",
  "start_date": "2026-06-15",
  "end_date": "2026-06-25",
  "type": "national", // national, school, emergency
  "description": "Cuti bersama Hari Raya Lebaran",
  "created_at": "2026-03-29T12:00:00Z"
}
```

---

## 🎯 QR Code Handling

### Visual QR Code Download
```
GET /student/{student_id}/qrcode
```

**Response**: PNG image (200x200px minimum)

**Usage**: Display in student card, print on ID card

**Frontend Implementation**:
```html
<img src="/student/{{ student.id }}/qrcode" alt="QR Code" />
```

### QR Code Scanner Integration
- Use `@vueuse/core` or `jsQR` library for web
- Capture QR string format: `SH-ABC12345`
- Send to POST /scan endpoint via Livewire `processScan()`

---

## 📊 Data Models Reference

### User Model
```php
id: integer
name: string
email: string (unique)
email_verified_at: timestamp nullable
password: string (hashed)
role: string (enum: admin, student, teacher)
created_at: timestamp
updated_at: timestamp
```

### Student Model
```php
id: integer
user_id: integer (FK → users)
class_id: integer (FK → student_classes)
nisn: string (unique, 13 digits)
qr_code: string (unique, format: SH-XXXXXXXX)
photo_path: string nullable
created_at: timestamp
updated_at: timestamp

Relationships:
- user: belongsTo(User)
- class: belongsTo(StudentClass)
- attendances: hasMany(Attendance)
```

### Attendance Model
```php
id: integer
student_id: integer (FK → students)
date: date
check_in: time nullable
check_out: time nullable
status: string (enum: present, late, sick, excused, absent)
notes: text nullable
created_at: timestamp
updated_at: timestamp

Relationships:
- student: belongsTo(Student)

Enum Status:
- 'present' (hadir)
- 'late' (terlambat)
- 'sick' (sakit)
- 'excused' (izin)
- 'absent' (alpa)
```

### StudentClass Model
```php
id: integer
name: string (unique)
level: string (enum: X, XI, XII)
major: string (enum: IPA, IPS, BAHASA)
created_at: timestamp
updated_at: timestamp

Relationships:
- students: hasMany(Student)
```

### Holiday Model (Coming This Week)
```php
id: integer
name: string
start_date: date
end_date: date
type: string (enum: national, school, emergency)
description: text nullable
created_at: timestamp
updated_at: timestamp
```

---

## ⚠️ Known Limitations

1. **QR Code Visual Generation**: Currently generates string only, visual PNG images generated via artisan command (not real-time)
2. **Export Feature**: Not yet available in Filament; coming in Phase 2
3. **Teacher Dashboard**: Placeholder only; full implementation coming
4. **API Routes**: Not yet available; REST API planned for v1.1
5. **Real-time Sync**: Uses Livewire polling only; no WebSockets yet

---

## 🔄 Future Extensions (v1.1+)

These are planned but not yet implemented:

```
GET /api/student/{id}/attendance - REST API
POST /api/scan - Mobile API endpoint
GET /admin/reports/attendance - Attendance reports page
POST /admin/attendances/export - Excel export
GET /admin/holidays/calendar - Holiday calendar view
POST /admin/attendances/import - Bulk import from CSV
```

---

## 📲 Frontend Checklist

Before starting frontend development, verify:

- [ ] Can login with valid credentials
- [ ] Can see student dashboard after login
- [ ] Can access scan page
- [ ] Can see QR code image for student
- [ ] Admin can access /admin panel
- [ ] Admin can see StudentResource with data
- [ ] Admin can see AttendanceResource with data
- [ ] Admin can see StudentClassResource with data
- [ ] Messages display correctly in Livewire components
- [ ] Form validations work and show errors
- [ ] Logout works and returns to login

---

## 🚀 Integration Timeline

- **Week 1**: Critical backend fixes + Frontend skeleton setup
- **Week 2**: Frontend integrates with working login/scan/dashboard
- **Week 3**: Frontend admin panel styling
- **Week 4**: Testing and bug fixes

---

## 📧 Contact & Questions

For clarification on any integration point:
- Check `docs/backend-readiness-analysis.md` for full status
- Check `docs/backend-implementation-quickstart.md` for implementation details
- Check Filament resources in `/admin` for current data structure
- Check Livewire components in `app/Livewire/` for component contracts

---

**Document Version**: 1.0  
**Last Updated**: March 29, 2026  
**Status**: Ready for Frontend Development ✅
