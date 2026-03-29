# 🔗 Blade-Backend Integration Roadmap

**Status**: ⚠️ Partially Connected (30% integrated, 70% to do)  
**Last Updated**: 2026-03-29

---

## 📊 Current Integration Status

### ✅ Fully Connected (3/21 Blades)
1. **Login** - StudentLogin Livewire ↔ User Model
2. **Attendance Scanner** - AttendanceScanner Livewire ↔ Attendance Model
3. **Student Dashboard** - StudentDashboard Livewire ↔ Student/Attendance Models

### ⚠️ Partially Connected (7/21 Blades)
- Auth pages (design only, routed to DashboardController with dummy data)
- Admin dashboard (hardcoded stats)
- Student pages (forms without handlers)

### ❌ Not Connected (11/21 Blades)
- All admin CRUD pages
- Permission/Leave forms
- Master data pages
- Report pages

---

## 🚀 PHASE 1: CRITICAL (2-3 Hours)

### Step 1.1: Fix Authentication Routes
**Problem**: Routes point to DashboardController instead of Livewire components

**Action**:
```bash
# Create Auth Livewire Components
php artisan make:livewire Auth/AdminLogin
php artisan make:livewire Auth/StudentLogin  # Update existing
php artisan make:livewire Auth/ForgotPassword
```

**Update [routes/web.php](routes/web.php)**:
```php
// Replace DashboardController routes with Livewire
Route::get('/login/student', StudentLogin::class)->name('login.student');
Route::get('/login/admin', AdminLogin::class)->name('login.admin');
Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
```

**Files to Create/Update**:
- [ ] `app/Livewire/Auth/AdminLogin.php`
- [ ] `app/Livewire/Auth/ForgotPassword.php`
- [ ] `resources/views/livewire/auth/admin-login.blade.php`
- [ ] `resources/views/livewire/auth/forgot-password.blade.php`

---

### Step 1.2: Fix Student Routes (Replace Dummy Data with Livewire)
**Problem**: Student routes return dummy data from DashboardController

**Action**:
```bash
php artisan make:livewire Student/StudentDashboard-Enhanced
php artisan make:livewire Student/StudentIzin
php artisan make:livewire Student/StudentProfil
php artisan make:livewire Student/StudentManualAttendance
```

**Update [routes/web.php](routes/web.php)**:
```php
Route::middleware(['auth', 'role:student'])->prefix('student')->group(function () {
    Route::get('/dashboard', StudentDashboard::class)->name('dashboard');
    Route::get('/izin', StudentIzin::class)->name('izin');
    Route::get('/profil', StudentProfil::class)->name('profil');
    Route::get('/manual-attendance', StudentManualAttendance::class)->name('manual');
});
```

**Files to Create/Update**:
- [ ] `app/Livewire/Student/StudentIzin.php`
- [ ] `app/Livewire/Student/StudentProfil.php`
- [ ] `app/Livewire/Student/StudentManualAttendance.php`
- [ ] Create corresponding blade files in `resources/views/livewire/student/`

---

### Step 1.3: Add Missing Database Models
**Problem**: Some features need models that don't exist

**Action**:
```bash
php artisan make:model Teacher -m
php artisan make:model Permission -m
php artisan make:model Schedule -m
php artisan make:model ActivityLog -m
```

**Migration Fields**:

**Teacher**:
```php
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->string('nip')->unique();
$table->string('specialization')->nullable(); // Mata pelajaran
$table->timestamps();
```

**Permission**:
```php
$table->id();
$table->foreignId('student_id')->constrained()->cascadeOnDelete();
$table->date('date');
$table->string('reason'); // Sakit, Izin, Dinas, dll
$table->text('notes')->nullable();
$table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
$table->foreignId('approved_by')->nullable()->constrained('users');
$table->timestamps();
```

**Schedule** (Jadwal Pelajaran):
```php
$table->id();
$table->foreignId('class_id')->constrained()->cascadeOnDelete();
$table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
$table->string('subject');
$table->integer('day'); // 0-6 (Monday-Sunday)
$table->time('start_time');
$table->time('end_time');
$table->timestamps();
```

**Activity Log**:
```php
$table->id();
$table->foreignId('user_id')->nullable()->constrained();
$table->string('action'); // login, create, update, delete
$table->string('model');
$table->unsignedBigInteger('model_id')->nullable();
$table->json('changes')->nullable();
$table->timestamps();
```

**Files to Create/Update**:
- [ ] `app/Models/Teacher.php`
- [ ] `app/Models/Permission.php`
- [ ] `app/Models/Schedule.php`
- [ ] `app/Models/ActivityLog.php`
- [ ] Run migrations: `php artisan migrate`

---

### Step 1.4: Create Missing Livewire Components for Admin
**Problem**: Admin pages don't have backend logic

**Action**:
```bash
php artisan make:livewire Admin/AdminDashboard
php artisan make:livewire Admin/AdminAnalytics
php artisan make:livewire Admin/AdminLogs
php artisan make:livewire Admin/IzinApproval
```

**Files to Create/Update**:
- [ ] `app/Livewire/Admin/AdminDashboard.php`
- [ ] `app/Livewire/Admin/AdminAnalytics.php`
- [ ] `app/Livewire/Admin/AdminLogs.php`
- [ ] `app/Livewire/Admin/IzinApproval.php`
- [ ] Create corresponding blade files

---

## 🏗️ PHASE 2: HIGH PRIORITY (3-4 Hours)

### Step 2.1: Create CRUD Livewire Forms for Master Data
**Problem**: Admin master data pages are forms without handlers

```bash
php artisan make:livewire Admin/Master/GuruManager
php artisan make:livewire Admin/Master/SiswaManager
php artisan make:livewire Admin/Master/KelasManager
php artisan make:livewire Admin/Master/JadwalManager
```

**Data to Handle**:
- **Guru**: Create/Edit/Delete Teacher records linked to User
- **Siswa**: Create/Edit/Delete Student records with User creation
- **Kelas**: Create/Edit/Delete StudentClass records
- **Jadwal**: Create/Edit/Delete Schedule records

**Files to Create/Update**:
- [ ] `app/Livewire/Admin/Master/GuruManager.php`
- [ ] `app/Livewire/Admin/Master/SiswaManager.php`
- [ ] `app/Livewire/Admin/Master/KelasManager.php`
- [ ] `app/Livewire/Admin/Master/JadwalManager.php`
- [ ] Update routes to use these components instead of DashboardController

---

### Step 2.2: Implement Permission/Leave System
**Problem**: Permission form exists but has no backend

```bash
php artisan make:livewire Student/RequestPermission
php artisan make:livewire Admin/ManagePermissions
```

**Features**:
- Student can submit permission requests
- Admin can approve/reject with notes
- Activity logging for audit trail

**Files to Create/Update**:
- [ ] `app/Livewire/Student/RequestPermission.php`
- [ ] `app/Livewire/Admin/ManagePermissions.php`
- [ ] Create blade templates

---

### Step 2.3: Add Activity Logging
**Problem**: No audit trail for admin actions

**Solution**: Add ActivityLog tracking to all Livewire CRUD operations

**Files to Update**:
- [ ] Update all Master Data Livewire components to log actions
- [ ] Create ActivityLog middleware
- [ ] Update AdminLogs Livewire to display activities

---

## 📝 PHASE 3: MEDIUM PRIORITY (2-3 Hours)

### Step 3.1: Implement Report System
**Problem**: Report pages exist but don't generate actual reports

```bash
php artisan make:livewire Reports/AttendanceReport
php artisan make:livewire Reports/PermissionReport
php artisan make:livewire Reports/StudentReport
```

**Files to Create/Update**:
- [ ] `app/Livewire/Reports/AttendanceReport.php`
- [ ] `app/Livewire/Reports/PermissionReport.php`
- [ ] `app/Livewire/Reports/StudentReport.php`
- [ ] Create report blade templates
- [ ] Update PDF export functionality

---

### Step 3.2: Implement Missing Teacher Features
**Problem**: No teacher dashboard/functionality

```bash
php artisan make:livewire Teacher/TeacherDashboard
php artisan make:livewire Teacher/ClassManagement
php artisan make:livewire Teacher/AttendanceInput
```

**Files to Create/Update**:
- [ ] `app/Livewire/Teacher/TeacherDashboard.php`
- [ ] `app/Livewire/Teacher/ClassManagement.php`
- [ ] `app/Livewire/Teacher/AttendanceInput.php`
- [ ] Add teacher routes

---

## 🔧 Model Relationships to Add

### Update User Model:
```php
public function teacher()
{
    return $this->hasOne(Teacher::class);
}

public function permissions()
{
    return $this->hasMany(Permission::class, 'approved_by');
}

public function activityLogs()
{
    return $this->hasMany(ActivityLog::class);
}
```

### Create Teacher Model:
```php
public function user() { return $this->belongsTo(User::class); }
public function schedules() { return $this->hasMany(Schedule::class); }
public function classes() { return $this->belongsToMany(StudentClass::class); }
```

### Create Permission Model:
```php
public function student() { return $this->belongsTo(Student::class); }
public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
```

### Create Schedule Model:
```php
public function class() { return $this->belongsTo(StudentClass::class, 'class_id'); }
public function teacher() { return $this->belongsTo(Teacher::class); }
```

---

## 🛡️ Authorization Policies to Create

```bash
php artisan make:policy TeacherPolicy -m Teacher
php artisan make:policy PermissionPolicy -m Permission
php artisan make:policy SchedulePolicy -m Schedule
```

**Register in AuthServiceProvider**:
```php
Gate::policy(Teacher::class, TeacherPolicy::class);
Gate::policy(Permission::class, PermissionPolicy::class);
Gate::policy(Schedule::class, SchedulePolicy::class);
```

---

## 📋 Implementation Checklist

### Phase 1: Critical
- [ ] Fix authentication routes → Livewire components
- [ ] Create 4 new models (Teacher, Permission, Schedule, ActivityLog)
- [ ] Run migrations
- [ ] Create admin dashboard Livewire components
- [ ] Update 4 student route components
- [ ] Test all login flows with different roles

### Phase 2: High Priority
- [ ] Create CRUD Livewire for master data
- [ ] Implement permission system
- [ ] Add activity logging
- [ ] Test all CRUD operations
- [ ] Verify role-based access control

### Phase 3: Medium Priority
- [ ] Implement report system
- [ ] Create teacher features
- [ ] Improve PDF export
- [ ] Add advanced filtering/searching

---

## 🧪 Testing Checklist (After Each Phase)

- [ ] Login with admin account
- [ ] Login with teacher account
- [ ] Login with student account
- [ ] Test all CRUD operations
- [ ] Verify database records created correctly
- [ ] Check relationships are properly loaded
- [ ] Verify activity logs recorded
- [ ] Test authorization/permissions
- [ ] Test form validation
- [ ] Test error handling

---

## ⚡ Database Connection Verification

Run these commands to verify database connectivity:

```bash
# Test database connection
php artisan tinker
> DB::connection()->getPDO()

# Check models
> User::count()
> Student::count()
> Attendance::count()

# Test a full flow
> $user = User::find(1)
> $user->student
> $user->student->attendances
```

---

## 📚 References

- Filament Admin Panel: `/admin` (already configured)
- Frontend-Backend Contract: `docs/frontend-backend-integration-contract.md`
- Backend Readiness: `docs/backend-readiness-analysis.md`
- Quickstart Guide: `docs/backend-implementation-quickstart.md`

---

**Next Steps**: Start with **Phase 1, Step 1.1** to fix authentication routing!
