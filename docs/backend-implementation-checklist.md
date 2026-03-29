# Backend Implementation Task Checklist

## 🔴 CRITICAL PATH (Complete ASAP - 4-5 hours total)

### Batch 1: Holiday Management (30 min)
- [ ] Update holidays table migration with name, start_date, end_date, description, type
- [ ] Update Holiday model with fillable attributes and isHoliday() static method
- [ ] Create HolidayResource Filament resource with Index/Create/Edit pages
- [ ] Run migration and test in Filament UI
- [ ] Update AttendanceScanner to check isHoliday() before recording attendance
- [ ] Test scan rejection on holiday date

### Batch 2: Authorization & Policies (45 min)
- [ ] Create StudentPolicy with viewAny, view, create, update, delete, forceDelete methods
- [ ] Create AttendancePolicy (same methods)
- [ ] Create StudentClassPolicy (same methods)
- [ ] Create HolidayPolicy (same methods)
- [ ] Register all policies in AuthServiceProvider
- [ ] Test policy checks prevent unauthorized access
- [ ] Apply authorization checks to all Filament Resources

### Batch 3: Visual QR Code Generation (1.5 hours)
- [ ] Create GenerateStudentQRCodes artisan command
- [ ] Command generates .png files in storage/app/qrcodes/{student_id}.png
- [ ] Add route GET /student/{student}/qrcode to serve QR image
- [ ] Add 'downloadQR' action to StudentResource table
- [ ] Run command: php artisan students:generate-qrcodes
- [ ] Verify QR images display correctly in browser
- [ ] Add QR display to student profile/card view

---

## 🟡 HIGH PRIORITY (1-2 weeks)

### Batch 4: Attendance Export to Excel (2 hours)
- [ ] Install maatwebsite/excel: composer require maatwebsite/excel
- [ ] Publish vendor config: php artisan vendor:publish
- [ ] Create AttendanceExport class with collection() and headings()
- [ ] Add 'export' action to AttendanceResource
- [ ] Test export generates .xlsx file with correct columns
- [ ] Add date range filter to export dialog
- [ ] Add class filter to export dialog

### Batch 5: Data Seeding for Development (1.5 hours)
- [ ] Create StudentClassFactory: php artisan make:factory StudentClassFactory
- [ ] Create StudentFactory with QR code generation
- [ ] Create AttendanceFactory with random dates/check_in/check_out
- [ ] Create HolidayFactory with various holiday types
- [ ] Update DatabaseSeeder to call all factories
  - [ ] 5 classes (X, XI, XII with different majors)
  - [ ] 50 students per class (250 total)
  - [ ] 30 days of attendance records
  - [ ] 10 holiday dates throughout year
- [ ] Run db:seed and verify data in Filament

### Batch 6: Teacher Resources & Dashboard (2 hours)
- [ ] Create TeacherPolicy
- [ ] Create Livewire TeacherDashboard component
- [ ] Add route /teacher/dashboard
- [ ] Display assigned classes in dashboard
- [ ] Display today's attendance for teacher's classes
- [ ] Add class attendance statistics (present, absent, late counts)
- [ ] Test role-based redirect (admin → /admin, teacher → /teacher/dashboard, student → /dashboard)

### Batch 7: Attendance Reports (2-3 hours)
- [ ] Create AttendanceReport model or service class
- [ ] Implement methods:
  - [ ] getDailyAttendanceSummary(date)
  - [ ] getStudentAttendanceHistory(student_id, start_date, end_date)
  - [ ] getClassAttendanceStats(class_id, month)
  - [ ] getAbsenteeismTrends(class_id, months)
- [ ] Create Filament report page at /admin/reports/attendance
- [ ] Add filters: date range, class, student
- [ ] Generate PDF report: php artisan make:livewire AttendanceReportPDF

---

## 🟢 MEDIUM PRIORITY (v1.1 and beyond)

### Batch 8: Input Validation Improvements (1 hour)
- [ ] Create FormRequest classes:
  - [ ] StoreStudentRequest
  - [ ] UpdateStudentRequest
  - [ ] ScanAttendanceRequest
  - [ ] UpdateAttendanceRequest
- [ ] Add custom validation rules:
  - [ ] QRCodeUnique
  - [ ] NISN format (13 digits)
  - [ ] DateRangeValid for holidays
- [ ] Apply to Livewire components and Filament forms

### Batch 9: Logging & Audit Trail (1.5 hours)
- [ ] Install spatie/laravel-activitylog: composer require spatie/laravel-activitylog
- [ ] Publish config: php artisan vendor:publish --provider="Spatie\ActivityLog\ActivityLogServiceProvider"
- [ ] Add LogsActivity trait to Student, Attendance, Holiday models
- [ ] Create Filament auditLog page to view all changes
- [ ] Log who changed what and when

### Batch 10: API Routes for Mobile (2 hours)
- [ ] Create routes/api.php with auth:sanctum middleware
- [ ] Implement endpoints:
  - [ ] POST /api/scan - Submit QR scan
  - [ ] GET /api/student/{id}/attendance - Get student's attendance
  - [ ] GET /api/class/{id}/attendance - Get class attendance
  - [ ] GET /api/holidays - Get all holidays for calendar
- [ ] Add API token generation for admin panel
- [ ] Test with Postman/Insomnia

---

## 📋 Testing Checklist

After completing each batch:

### Unit Tests
- [ ] Holiday::isHoliday() returns true/false correctly
- [ ] Attendance validation prevents duplicates
- [ ] QR code generation creates unique codes
- [ ] Policies correctly gate access

### Feature Tests
- [ ] Student can scan attendance when logged in
- [ ] Student cannot scan on holiday
- [ ] Admin can export attendance to Excel
- [ ] Teacher sees only assigned classes
- [ ] Unauthorized users cannot access resources

### Integration Tests
- [ ] Migration rollback/rerun works cleanly
- [ ] Seeder creates correct data relationships
- [ ] QR code images exist after generation
- [ ] Filament forms validate and save correctly

---

## 🚀 Deployment Checklist

Before pushing to production:

- [ ] All migrations run successfully: php artisan migrate
- [ ] All seeders run without error: php artisan db:seed
- [ ] All tests pass: php artisan test
- [ ] No console errors: php artisan tinker
- [ ] QR codes are generated: php artisan students:generate-qrcodes
- [ ] Config cached: php artisan config:cache
- [ ] Routes cached: php artisan route:cache
- [ ] Views cached: php artisan view:cache
- [ ] Storage directories have correct permissions
- [ ] Database has backups enabled

---

## 👥 Team Coordination

**Suggested Parallel Work**:
- Backend team: Batches 1-3 (critical path)
- Frontend team: Can start UI/UX design while backend Batches 1-3 complete
- Midway sync: Frontend review API contracts before Batches 4-5

**Sync Points**:
1. After Batch 3 - Frontend integrated with working QR scan & dashboard
2. After Batch 5 - Frontend has test data to work with
3. After Batch 7 - Admin panel fully functional with reporting

---

## 📈 Progress Metrics

Track weekly progress:

| Week | Target | Status | Notes |
|------|--------|--------|-------|
| Week 1 | Batches 1-3 (Critical) | ⏳ Pending | |
| Week 2 | Batches 4-6 (High Priority) | ⏳ Pending | |
| Week 3 | Batches 7-10 (Medium Priority) | ⏳ Pending | |
| Week 4 | Testing & Optimization | ⏳ Pending | |

---

## 💡 Quick Command Reference

```bash
# Generate policies
php artisan make:policy StudentPolicy -m Student

# Generate factories
php artisan make:factory StudentClassFactory

# Generate Filament resources
php artisan make:filament-resource Holiday

# Generate QR codes
php artisan students:generate-qrcodes

# Run tests
php artisan test

# Database commands
php artisan migrate:fresh --seed
php artisan tinker
php artisan db:seed

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

---

**Last Updated**: March 29, 2026  
**Total Estimated Time**: ~30-40 hours (spread across 4 weeks)  
**Recommendation**: Dedicate 1 backend developer full-time to complete critical path (Batches 1-3) this week
