# ScanHadir Project - Blade Files & Components Analysis

**Project Date:** March 29, 2026  
**Analysis Scope:** Complete Blade files, Livewire components, and Controller connections

---

## 📊 Summary Statistics

**Total Blade Files:** 21
- Root/Public: 2
- Student area: 4
- Admin area: 7  
- Admin Master Data: 4
- Auth: 2
- Components: 1
- Livewire: 3
- Reports: 1

**Livewire Components:** 3
- StudentDashboard (Livewire\StudentDashboard.php)
- AttendanceScanner (Livewire\AttendanceScanner.php)
- StudentLogin (Livewire\StudentLogin.php)

**Controllers:** 1 main (DashboardController)

---

## 📁 Complete Blade Files Directory Structure

```
resources/views/
├── landing.blade.php                          [Public]
├── welcome.blade.php                          [Public]
├── student/
│   ├── dashboard.blade.php                    [Student Dashboard]
│   ├── izin.blade.php                         [Permission Request]
│   ├── profil.blade.php                       [Student Profile]
│   └── manual_attendance.blade.php            [Manual Attendance Entry]
├── admin/
│   ├── dashboard.blade.php                    [Admin Dashboard]
│   ├── analytics.blade.php                    [Analytics & Reports]
│   ├── izin_approval.blade.php                [Permission Approval]
│   ├── logs.blade.php                         [System Logs]
│   ├── settings.blade.php                     [Admin Settings]
│   ├── scanner.blade.php                      [Scanner Control]
│   ├── report_pdf.blade.php                   [PDF Report Export]
│   └── master/
│       ├── siswa.blade.php                    [Student Master Data]
│       ├── guru.blade.php                     [Teacher Master Data]
│       ├── kelas.blade.php                    [Class Master Data]
│       └── jadwal.blade.php                   [Schedule Master Data]
├── auth/
│   ├── login_student.blade.php                [Student Login]
│   └── login_admin.blade.php                  [Admin Login]
├── components/
│   └── layouts/
│       └── app.blade.php                      [App Layout Component]
├── livewire/
│   ├── student-dashboard.blade.php            [Livewire: Student Dashboard]
│   ├── student-login.blade.php                [Livewire: Student Login Form]
│   └── attendance-scanner.blade.php           [Livewire: QR Scanner]
├── layouts/
│   ├── admin.blade.php                        [Admin Layout]
│   ├── student.blade.php                      [Student Layout]
│   └── guest.blade.php                        [Guest Layout]
└── reports/
    └── attendance.blade.php                   [Attendance Report]
```

---

## 📋 Detailed Blade File Analysis

### 1. **Public/Guest Pages**

#### `landing.blade.php`
- **Type:** Landing page / Marketing page
- **Layout:** `layouts.guest`
- **Data Requirements:** None (static)
- **Features:**
  - Navigation with links to login portals
  - Hero section with app description
  - Call-to-action buttons
- **Database Needs:** None

#### `welcome.blade.php`
- **Type:** Welcome/Home page
- **Layout:** None (standalone HTML)
- **Data Requirements:** None (dynamic year only: `{{ date('Y') }}`)
- **Features:**
  - Modern welcome screen
  - Portal access buttons
  - Version indicator
- **Database Needs:** None

---

### 2. **Student Area**

#### `student/dashboard.blade.php` ⚠️ PARTIALLY CONNECTED
- **Type:** Student dashboard (controller-based, NOT Livewire)
- **Layout:** `layouts.student`
- **Route:** `GET /student/dashboard` → `DashboardController@studentDashboard`
- **Data Requirements:**
  - `$student_name` - Student's full name
  - `$class` - Student's class name
  - `$attendance_this_month` - Count of attended days this month
  - `$total_days` - Total school days this month
  - `$late_count` - Number of late arrivals
  - `$today_status` - Today's attendance status
  - `$nisn` - National Student ID
  - `$recent_history` - Array of recent attendance records
- **Database Connections:**
  - **User model** → name (student_name)
  - **Student model** → nisn, class_id
  - **StudentClass model** → name (class)
  - **Attendance model** → date, status, check_in, check_out
- **Status:** ✅ Connected to DashboardController
- **Note:** Currently uses dummy data from controller; should pull from database

#### `student/izin.blade.php` ⚠️ NOT PROPERLY CONNECTED
- **Type:** Permission/Leave request form
- **Layout:** `layouts.student`
- **Route:** `GET /student/izin` → `DashboardController@studentIzin`
- **Data Requirements:**
  - None passed from controller (form only)
  - Form needs to POST to an endpoint
- **Features:**
  - Permission type selector (Izin/Sakit/Keperluan Mendesak)
  - Date range picker
  - Description textarea
- **Missing Components:**
  - No POST route defined
  - No associated model for submissions
  - No Livewire component for form handling
- **Status:** ❌ Form structure only, no backend implementation

#### `student/profil.blade.php` ⚠️ PARTIALLY CONNECTED
- **Type:** Student profile page
- **Layout:** `layouts.student`
- **Route:** `GET /student/profil` → `DashboardController@studentProfil`
- **Data Requirements:**
  - `$student_name` - Student's full name
  - `$class` - Student's class
  - `$nisn` - National Student ID
  - `$student` object with photo_path and QR code
- **Database Connections:**
  - **Student model** → nisn, qr_code, photo_path
  - **StudentClass model** → name
  - **User model** → name
- **Status:** ⚠️ Data not passed from controller
- **Note:** Page displays user data but data source not implemented

#### `student/manual_attendance.blade.php` ⚠️ NOT CONNECTED
- **Type:** Manual attendance entry form
- **Layout:** `layouts.student`
- **Route:** `GET /student/manual-attendance` → `DashboardController@studentManual`
- **Data Requirements:**
  - `$student_name` - (for display)
  - `$class` - (for display)
  - Status options (Hadir/Terlambat)
  - Time input fields
- **Missing Components:**
  - No POST handler
  - Form submission endpoint not defined
  - No attendance creation logic
- **Status:** ❌ Form structure only

---

### 3. **Admin Area**

#### `admin/dashboard.blade.php` ⚠️ PARTIALLY CONNECTED
- **Type:** Admin system overview dashboard
- **Layout:** `layouts.admin`
- **Route:** `GET /admin/dashboard` → `DashboardController@adminDashboard`
- **Data Requirements:**
  - Attendance percentage today
  - Total active students count
  - Late arrival count
  - Absent students count
  - Real-time attendance log
- **Database Connections Needed:**
  - **Attendance model** → date, status, student_id
  - **Student model** → user_id, class_id
  - **User model** → name
  - **StudentClass model** → name
- **Status:** ⚠️ Static placeholder data visible
- **Note:** Dashboard shows hardcoded statistics; needs real data queries

#### `admin/analytics.blade.php` ⚠️ NOT CONNECTED
- **Type:** Statistical analysis and reports
- **Layout:** `layouts.admin`
- **Route:** `GET /admin/analytics` → `DashboardController@adminAnalytics`
- **Data Requirements:**
  - Weekly attendance trend data
  - Attendance distribution statistics
  - Comparison metrics
  - Date range filtering
- **Status:** ❌ Placeholder charts only
- **Note:** Visual structure complete, data binding needed

#### `admin/izin_approval.blade.php` ⚠️ NOT IMPLEMENTED
- **Type:** Permission/Leave approval management
- **Layout:** `layouts.admin`
- **Route:** `GET /admin/izin-approval` → `DashboardController@adminIzinApproval`
- **Database Connections Needed:**
  - Permission/Leave model (not yet created)
  - **Student model** → student_id
  - **User model** → name
- **Status:** ❌ Not found or empty

#### `admin/logs.blade.php` ⚠️ NOT CONNECTED
- **Type:** System activity logs
- **Layout:** `layouts.admin`
- **Route:** `GET /admin/logs` → `DashboardController@adminLogs`
- **Status:** ⚠️ File not examined (likely placeholder)

#### `admin/settings.blade.php` ⚠️ NOT CONNECTED
- **Type:** Admin configuration settings
- **Layout:** `layouts.admin`
- **Route:** `GET /admin/settings` → `DashboardController@adminSettings`
- **Status:** ⚠️ File not examined

#### `admin/scanner.blade.php` ⚠️ NOT MAPPED
- **Type:** Scanner control/management
- **Layout:** `layouts.admin`
- **Route:** `GET /admin/scanner` → `DashboardController@adminScanner`
- **Status:** ⚠️ File not examined

#### `admin/report_pdf.blade.php` ⚠️ NOT CONNECTED
- **Type:** PDF report generation
- **Layout:** `layouts.admin`
- **Route:** `GET /admin/report-pdf` → `DashboardController@adminReportPdf`
- **Database Connections Needed:**
  - **Attendance model**
  - **Student model**
  - **StudentClass model**
- **Status:** ❌ File not examined

---

### 4. **Admin Master Data Management**

#### `admin/master/siswa.blade.php` ⚠️ PARTIALLY CONNECTED
- **Type:** Student master data CRUD interface
- **Layout:** `layouts.admin`
- **Route:** `GET /admin/master/siswa` → `DashboardController@masterSiswa`
- **Data Requirements:**
  - Student list with: name, NISN, class, photo
  - Search and filter functionality
  - Pagination
- **Database Connections:**
  - **Student model** → nisn, photo_path, class_id
  - **User model** → name
  - **StudentClass model** → name
- **Features:**
  - Search by name or NISN
  - Filter by class
  - Add new student button
  - Download/export functionality
- **Missing Components:**
  - Create/Edit/Delete endpoints not shown
  - No Livewire component for CRUD
- **Status:** ⚠️ Display only, CRUD endpoints missing

#### `admin/master/guru.blade.php`
- **Type:** Teacher master data CRUD
- **Layout:** `layouts.admin`
- **Route:** `GET /admin/master/guru` → `DashboardController@masterGuru`
- **Status:** ❌ File not examined / May need Teacher model

#### `admin/master/kelas.blade.php`
- **Type:** Class master data CRUD
- **Layout:** `layouts.admin`
- **Route:** `GET /admin/master/kelas` → `DashboardController@masterKelas`
- **Database Connections:**
  - **StudentClass model** → name, level, major
- **Status:** ❌ File not examined

#### `admin/master/jadwal.blade.php`
- **Type:** Schedule master data CRUD
- **Layout:** `layouts.admin`
- **Route:** `GET /admin/master/jadwal` → `DashboardController@masterJadwal`
- **Status:** ❌ File not examined / Needs Schedule model

---

### 5. **Authentication**

#### `auth/login_student.blade.php` ⚠️ NOT CONNECTED TO LIVEWIRE
- **Type:** Student login form
- **Layout:** `layouts.guest`
- **Route:** `GET /login/student` → `DashboardController@loginStudent`
- **Features:**
  - Email input
  - Password input
  - Remember me checkbox
  - Forgot password link
- **Status:** ⚠️ Form uses GET method to `/student/dashboard` but should POST
- **Note:** There's a **StudentLogin Livewire component** that should handle this instead

#### `auth/login_admin.blade.php` ⚠️ NOT CONNECTED
- **Type:** Admin login form
- **Layout:** `layouts.guest`
- **Route:** `GET /login/admin` → `DashboardController@loginAdmin`
- **Features:**
  - Username input
  - Password input
  - Submit button
- **Status:** ❌ Form uses GET method; needs POST authentication
- **Database Connections Needed:**
  - **User model** → email, password, role

---

### 6. **Layout Files**

#### `layouts/admin.blade.php`
- **Type:** Admin dashboard layout
- **Features:**
  - Fixed sidebar navigation
  - Color scheme: Indigo primary (#2f1bc8)
  - Material Design 3 theming
  - Responsive grid layout
- **Extends:** None (root)
- **Yields:** @yield('content')

#### `layouts/student.blade.php`
- **Type:** Student portal layout
- **Features:**
  - Student-focused design
  - Navigation header
- **Extends:** None (root)
- **Yields:** @yield('content')

#### `layouts/guest.blade.php`
- **Type:** Public/guest layout
- **Features:**
  - Light theme design
  - Minimal navigation
  - Mesh gradient backgrounds
- **Extends:** None (root)
- **Yields:** @yield('content')

#### `components/layouts/app.blade.php`
- **Type:** Application component layout
- **Usage:** Referenced in Livewire components via `->layout('layouts.app')`
- **Status:** ⚠️ File not fully examined

---

### 7. **Livewire Components**

#### `livewire/student-dashboard.blade.php` ✅ CONNECTED
- **Component Class:** `App\Livewire\StudentDashboard`
- **Route Option:** Can be accessed as Livewire component
- **Data Provided by Controller:**
  ```php
  [
    'student' => Student object with relations,
    'history' => Latest 10 attendance records
  ]
  ```
- **Features:**
  - Greeting with student name
  - QR code display and download
  - Recent attendance history
  - Status indicators
- **Database Requirements:**
  - **Student model** → user, class, qr_code, nisn, photo_path
  - **Attendance model** → date, check_in, check_out, status
  - **User model** → name

#### `livewire/student-login.blade.php` ✅ CONNECTED
- **Component Class:** `App\Livewire\StudentLogin`
- **Layout:** `layouts.app`
- **Features:**
  - Email/password authentication
  - Form validation
  - Error handling
  - Role-based redirection
- **Database Requirements:**
  - **User model** → email, password, role
- **Validation Rules:**
  - email: required, email format
  - password: required, min 6 chars
- **Status:** ✅ Fully implemented with error handling

#### `livewire/attendance-scanner.blade.php` ✅ CONNECTED
- **Component Class:** `App\Livewire\AttendanceScanner`
- **Layout:** `layouts.app`
- **Features:**
  - HTML5 QR code scanner
  - Real-time processing
  - Check-in/Check-out logic
  - Success/error feedback
- **Database Requirements:**
  - **Student model** → qr_code, user_id, class_id
  - **User model** → name
  - **StudentClass model** → name
  - **Attendance model** → student_id, date, status, check_in, check_out
- **Logic:**
  - On first scan of day: Creates attendance record with check_in time
  - On second scan of day: Updates check_out time
  - Multiple scans: Shows "already scanned" message
- **Status:** ✅ Fully implemented with QR processing

---

### 8. **Reports**

#### `reports/attendance.blade.php` ⚠️ NOT CONNECTED
- **Type:** Attendance report view
- **Layout:** Unknown
- **Route:** Not defined
- **Status:** ❌ Orphaned / needs connection

---

## 🗄️ Database Models Status

### ✅ Models Currently Defined

1. **User** (app/Models/User.php)
   - Fields: name, email, password, role
   - Relations: hasOne(Student)
   - Helper methods: isAdmin(), isTeacher(), isStudent()
   - **Status:** ✅ Complete

2. **Student** (app/Models/Student.php)
   - Fields: user_id, class_id, nisn, qr_code, photo_path
   - Relations: belongsTo(User), belongsTo(StudentClass), hasMany(Attendance)
   - Auto-generates QR code on creation
   - **Status:** ✅ Complete

3. **StudentClass** (app/Models/StudentClass.php)
   - Fields: name, level, major
   - Relations: hasMany(Student)
   - **Status:** ✅ Complete

4. **Attendance** (app/Models/Attendance.php)
   - Fields: student_id, date, check_in, check_out, status, notes
   - Relations: belongsTo(Student)
   - **Status:** ✅ Complete

5. **Holiday** (app/Models/Holiday.php)
   - Fields: None defined
   - Relations: None
   - **Status:** ❌ Empty/Not implemented

### ❌ Models Missing

1. **Teacher/Guru** - No model exists
   - Needed for: admin/master/guru.blade.php
   - Fields needed: name, nip, subject, class_id

2. **Leave/Permission** - No model exists
   - Needed for: student/izin.blade.php, admin/izin_approval.blade.php
   - Fields needed: student_id, type, start_date, end_date, reason, status, approved_by

3. **Schedule/Jadwal** - No model exists
   - Needed for: admin/master/jadwal.blade.php
   - Fields needed: class_id, day, start_time, end_time, subject

4. **Log/ActivityLog** - No model exists
   - Needed for: admin/logs.blade.php

---

## 📡 Route-to-Blade Mapping

| Route | Controller Method | Blade File | Status |
|-------|-------------------|-----------|--------|
| `/` | `landing()` | `landing.blade.php` | ✅ |
| `/login/student` | `loginStudent()` | `auth/login_student.blade.php` | ⚠️ |
| `/login/admin` | `loginAdmin()` | `auth/login_admin.blade.php` | ⚠️ |
| `/student/dashboard` | `studentDashboard()` | `student/dashboard.blade.php` | ✅ |
| `/student/izin` | `studentIzin()` | `student/izin.blade.php` | ❌ |
| `/student/profil` | `studentProfil()` | `student/profil.blade.php` | ⚠️ |
| `/student/manual-attendance` | `studentManual()` | `student/manual_attendance.blade.php` | ❌ |
| `/admin/dashboard` | `adminDashboard()` | `admin/dashboard.blade.php` | ⚠️ |
| `/admin/analytics` | `adminAnalytics()` | `admin/analytics.blade.php` | ❌ |
| `/admin/izin-approval` | `adminIzinApproval()` | `admin/izin_approval.blade.php` | ❌ |
| `/admin/logs` | `adminLogs()` | `admin/logs.blade.php` | ❓ |
| `/admin/settings` | `adminSettings()` | `admin/settings.blade.php` | ❓ |
| `/admin/scanner` | `adminScanner()` | `admin/scanner.blade.php` | ❓ |
| `/admin/report-pdf` | `adminReportPdf()` | `admin/report_pdf.blade.php` | ❌ |
| `/admin/master/guru` | `masterGuru()` | `admin/master/guru.blade.php` | ❓ |
| `/admin/master/siswa` | `masterSiswa()` | `admin/master/siswa.blade.php` | ⚠️ |
| `/admin/master/kelas` | `masterKelas()` | `admin/master/kelas.blade.php` | ❓ |
| `/admin/master/jadwal` | `masterJadwal()` | `admin/master/jadwal.blade.php` | ❓ |

**Legend:**
- ✅ = Fully implemented and connected
- ⚠️ = Partially implemented / needs data binding
- ❌ = Structure only / no backend logic
- ❓ = Not examined

---

## 🔗 Livewire Components Status

| Component | Location | Route | Layout | Database Queries | Status |
|-----------|----------|-------|--------|------------------|--------|
| StudentDashboard | `app/Livewire/StudentDashboard.php` | Route-less | layouts.app | Student, Attendance, StudentClass | ✅ Implemented |
| StudentLogin | `app/Livewire/StudentLogin.php` | Route-less | layouts.app | User (via Auth::attempt) | ✅ Implemented |
| AttendanceScanner | `app/Livewire/AttendanceScanner.php` | Route-less | layouts.app | Student, Attendance, StudentClass | ✅ Implemented |

**Issue:** Livewire components are not registered in routes. They may need explicit routes or wire:navigate directive usage.

---

## ⚠️ Critical Issues Found

### 1. **Missing Authentication System**
- Login forms exist but don't properly authenticate
- Livewire StudentLogin component exists but not used
- No middleware protecting authenticated routes
- No session/token handling visible

### 2. **Disconnected Forms**
- `student/izin.blade.php` - No POST handler
- `student/manual_attendance.blade.php` - No submission endpoint
- Auth forms use GET method instead of POST

### 3. **Missing Models**
- Holiday model exists but empty
- Teacher/Guru model missing
- Leave/Permission model missing
- Schedule model missing
- ActivityLog model missing

### 4. **Hardcoded Data**
- Student dashboard uses dummy data from controller
- Admin dashboard shows placeholder statistics
- No real database queries in most controllers

### 5. **Livewire Routes Not Defined**
- StudentDashboard, StudentLogin, AttendanceScanner not in routes
- May require explicit route registration or wire component directives

### 6. **Orphaned Blade Files**
- `reports/attendance.blade.php` - Not referenced anywhere
- `admin/logs.blade.php`, `admin/settings.blade.php`, `admin/scanner.blade.php` - Not examined

---

## 📊 Data Flow Requirements

### Student Login Flow
```
Form Input (email/password)
  ↓
StudentLogin Livewire Component
  ↓
Auth::attempt() checks User table
  ↓
Redirect to appropriate dashboard based on role
```

**Missing:** Proper routing for StudentLogin component

### QR Scanner Flow
```
Camera Input (QR Code)
  ↓
AttendanceScanner Livewire Component
  ↓
processScan() method processes QR code
  ↓
Student table lookup by qr_code
  ↓
Attendance table create/update with timestamp
  ↓
Display success/error feedback
```

**Status:** ✅ Complete flow implemented

### Student Dashboard Flow
```
StudentDashboard Livewire Component
  ↓
Fetch Student record with relations
  ↓
Fetch last 10 Attendance records
  ↓
Display in blade template
```

**Status:** ✅ Complete flow implemented

---

## 🎯 Recommendations

### Priority 1: Complete Authentication
1. Connect StudentLogin Livewire component to `/login/student` route
2. Add authentication middleware to protected routes
3. Implement proper logout functionality

### Priority 2: Implement Missing Models
1. Create Teacher model
2. Create Leave/Permission model
3. Complete Holiday model
4. Create Schedule model

### Priority 3: Connect Admin Dashboard
1. Replace hardcoded data with real database queries
2. Implement analytics calculations
3. Create real-time activity log

### Priority 4: Complete Form Handlers
1. Add POST routes for izin.blade.php
2. Add POST routes for manual_attendance.blade.php
3. Create appropriate controllers/Livewire components

### Priority 5: Master Data CRUD
1. Implement full CRUD operations for Student, Class, Teacher, Schedule
2. Add form validation
3. Add success/error notifications

---

## 📝 Notes

- All layouts use Material Design 3 with Tailwind CSS
- Color scheme: Indigo primary (#2f1bc8)
- Responsive design implemented across all pages
- QR code generation is automatic for students
- Attendance logic includes check-in/check-out tracking
- Most administrative features are UI-only without backend logic
