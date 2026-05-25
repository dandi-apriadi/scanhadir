# Backend Readiness Analysis - ScanHadir

**Date**: 29 March 2026  
**Status**: Mostly ready, with a few important gaps still open
**Recommendation**: Continue backend polish in parallel with frontend; prioritize access-control verification, export coverage, and backlog cleanup

---

## 📊 Summary Status

| Component | Status | Completeness | Priority |
|-----------|--------|--------------|----------|
| **Models & Relationships** | ✅ Ready | 95% | - |
| **Migrations & Schema** | ⚠️ Incomplete | 80% | 🔴 HIGH |
| **Routes** | ⚠️ Partial | 75% | 🟡 MEDIUM |
| **Controllers** | ❌ Missing | 0% | 🟡 MEDIUM |
| **Livewire Components** | ✅ Ready | 90% | - |
| **Filament Admin Panel** | ⚠️ Incomplete | 70% | 🟡 MEDIUM |
| **Authorization/Policies** | ✅ Registered, needs verification | 80% | 🔴 HIGH |
| **API/Export Features** | ⚠️ Partially implemented | 70% | 🟡 MEDIUM |
| **Logging & Monitoring** | ❌ Missing | 0% | 🟢 LOW |

---

## 🔴 Critical Issues (MUST Fix)

### 1. **Holiday Management Incomplete**

**Status**: Migration exists but empty; model bare; no Filament resource

**Impact**: Cannot validate attendance against school holidays

**Files Affected**:
- `database/migrations/2026_03_29_035148_create_holidays_table.php`
- `app/Models/Holiday.php`
- Missing: `app/Filament/Resources/HolidayResource.php`

**Action Items**:
1. Update migration to add proper columns:
   ```php
   $table->string('name'); // e.g., "Lebaran", "Cuti Semester"
   $table->date('start_date');
   $table->date('end_date');
   $table->text('description')->nullable();
   $table->string('type')->default('national'); // national, school, emergency
   ```

2. Update Model with fillable attributes and define relationships

3. Create HolidayResource in Filament for admin CRUD

4. Add logic to deny attendance if date falls within holiday range

**Estimated Time**: 30 minutes

---

### 2. **Authorization & Policies Need Consistency Checks**

**Status**: Policy classes and registration already exist, but resource access and forbidden-path coverage still need to be verified end-to-end

**Impact**: Access-control regressions can slip through when resources or routes are added later

**Files Involved**:
- `app/Policies/*.php`
- `app/Providers/AppServiceProvider.php`
- `app/Filament/Resources/*.php`

**Action Items**:
1. Keep resource access aligned with policies instead of hard-coded role checks.
2. Add feature tests for denied CRUD and denied export/report access.
3. Re-run the authorization suite whenever a new role or resource is introduced.

---

## 🟡 High Priority Issues (Should Address This Week)

### 3. **Attendance Report & Export Missing**

**Status**: No reporting views or export functionality

**Impact**: Admin cannot generate compliance reports, cannot export data to Excel/CSV

**Action Items**:
1. Create report generation methods in a new `ReportService`:
   - Daily attendance summary
   - Per-student attendance history
   - Class-wide attendance statistics
   - Absenteeism trends

2. Create Excel export using `maatwebsite/excel` package:
   ```bash
   composer require maatwebsite/excel
   ```

3. Add routes for:
   - `/admin/reports/attendance` - View attendance reports
   - `/admin/reports/attendance/export` - Export to Excel
   - `/admin/reports/attendance/pdf` - Export to PDF (dompdf already installed)

4. Create Filament page or action for export functionality

**Estimated Time**: 2-3 hours

---

### 4. **Missing Teacher/Guru Resources**

**Status**: User role includes "teacher" but no resources/dashboard for teachers

**Impact**: Teacher functionality incomplete; system is student + admin only

**Action Items**:
1. Create `TeacherResource` in Filament (manage teacher accounts, class assignments)

2. Create Teacher dashboard showing:
   - Assigned classes
   - Recent attendance records for their classes
   - Class-level statistics

3. Add route `/teacher/dashboard`

4. Create Livewire component `TeacherDashboard.php`

**Estimated Time**: 1.5-2 hours

---

### 5. **QR Code Visual Generation Missing**

**Status**: QR code stored as string (e.g., "SH-ABC12345") but not as visual QR image

**Impact**: Cannot print/display QR codes for students; reduces accessibility

**Issue in Code**:
```php
// Student.php - creates string, not image
static::creating(function ($student) {
    $student->qr_code = 'SH-' . strtoupper(Str::random(8));
});
```

**Action Items**:
1. Create command to generate visual QR codes:
   ```bash
   php artisan make:command GenerateStudentQRCodes
   ```

2. Use `SimpleSoftwareIO\QrCode\Facades\QrCode` to generate images:
   ```php
   $qrCode = QrCode::format('png')->generate($student->qr_code);
   // Save to storage/app/qrcodes/{student_id}.png
   ```

3. Create route `/student/{id}/qrcode` to serve QR code image

4. Add Filament action to download QR or generate bulk PDF with all student QRs

**Estimated Time**: 1.5 hours

---

### 6. **Logging & Audit Trail Missing**

**Status**: No logging of attendance changes or admin actions

**Impact**: Cannot audit who changed what and when; compliance issue

**Action Items**:
1. Add package for audit logging:
   ```bash
   composer require spatie/laravel-activitylog
   ```

2. Log all attendance changes and admin actions

3. Create view `/admin/logs` to display audit trail

**Estimated Time**: 1 hour

---

## 🟢 Medium Priority (Nice-to-Have for v1.0)

### 7. **API Routes for Mobile/Future Integration**

**Status**: No REST API defined

**Action Items**:
1. Create API routes in `routes/api.php`:
   - `POST /api/scan` - Submit QR scan
   - `GET /api/student/{id}/attendance` - Get student's attendance
   - `GET /api/class/{id}/attendance` - Get class attendance

2. Add API authentication (Sanctum tokens)

3. Add API rate limiting and validation

**Estimated Time**: 2 hours

---

### 8. **Database Seeding for Development**

**Status**: `DatabaseSeeder.php` exists but likely incomplete

**Action Items**:
1. Create factory classes:
   - `StudentClassFactory.php`
   - `StudentFactory.php`
   - `AttendanceFactory.php`
   - `HolidayFactory.php`

2. Add seeding to `DatabaseSeeder.php` for local dev:
   - 5 classes
   - 50-100 students per class
   - 30 days of random attendance data
   - 10 holiday dates

3. Command to seed: `php artisan db:seed`

**Estimated Time**: 1.5 hours

---

### 9. **Input Validation & Error Handling**

**Status**: Basic validation present; could be more robust

**Action Items**:
1. Add FormRequest validation classes:
   - `StoreStudentRequest.php`
   - `UpdateAttendanceRequest.php`
   - `ScanAttendanceRequest.php`

2. Add custom validation rules for:
   - QR code uniqueness
   - NISN format (13 digits)
   - Date ranges for holidays

3. Improve error messages in Livewire components

**Estimated Time**: 1 hour

---

## 📋 Recommended Implementation Order

### Phase 1 (This Week) - CRITICAL
1. Complete Holiday migration + model + Filament resource
2. Add authorization policies for all resources
3. Fix QR code visual generation

### Phase 2 (Next Week) - HIGH PRIORITY
4. Add attendance export/reporting functionality
5. Add teacher resources and dashboard
6. Add logging/audit trail

### Phase 3 (After Frontend) - MEDIUM PRIORITY
7. Database seeders for development
8. Input validation improvements
9. API routes for future integrations
10. Mobile-specific optimizations

---

## ✅ What's Already Good

- ✅ **Models & Relationships**: Well-structured with proper foreign keys and relationships
- ✅ **Core Livewire Components**: StudentLogin, AttendanceScanner, StudentDashboard are functional
- ✅ **Attendance Logic**: Handles check-in/check-out and duplicate detection properly
- ✅ **Basic Filament CRUD**: StudentResource, AttendanceResource, StudentClassResource work well
- ✅ **QR Code Tracking**: String-based tracking functional for attendance matching
- ✅ **Database Schema**: Solid foundation with proper migrations

---

## 🚀 Frontend Requirements Summary

When frontend team starts, they can assume:

1. ✅ Login endpoint (`/login` → StudentLogin component)
2. ✅ Scan page (`/scan` → AttendanceScanner component)
3. ✅ Student dashboard (`/dashboard` → StudentDashboard component)
4. ✅ Attendance data persists and validates correctly
5. ✅ Admin panel (`/admin`) accessible for data management
6. ⏳ **WAITING**: QR code images (visual), export features, detailed reports

---

## 💡 Backend-Frontend Integration Checklist

- [ ] Frontend receives clear QR code image from backend
- [ ] Frontend handles attendance scan success/error responses
- [ ] Frontend displays student attendance history from API
- [ ] Frontend integrates with Filament for admin functionality
- [ ] Frontend respects authorization (students see own data only)
- [ ] Error messages and success toasts properly displayed

---

## 📝 Notes

- Database currently supports SQLite (development) and MySQL (production)
- All dependencies installed: Laravel 11, Livewire, Filament, dompdf, simplesoftwareio/qrcode
- Testing structure in place but no tests written yet
- Ready for immediate development; no blockers for frontend work
