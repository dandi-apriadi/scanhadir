# Teacher Portal Enhancement - Phase 2 Planning
**Date**: 29 Maret 2026  
**Status**: PLANNING  
**Priority**: Teacher Features Improvement & Missing Functionality

---

## 📊 Current Teacher Portal Status

### ✅ Existing Components
1. **TeacherDashboard** - Main Dashboard
   - Real-time attendance overview
   - Class statistics with polling
   - Date selection
   - Status breakdown (present, late, sick, excused, absent)

2. **AttendanceScanner** - Livewire Scanner
   - QR code scanning capability
   - Auto check-in/check-out
   - Holiday detection
   - Throttle mechanism

3. **BulkAttendanceUpdate** - Bulk Operations
   - Update multiple students at once
   - Class & date filtering
   - Confirmation dialog

4. **AttendanceAnalytics** - Analytics View
   - Attendance trends
   - Per-class analysis
   - Date range analysis

5. **AttendanceReports** - Reporting
   - Export functionality
   - Report generation

### ⚠️ Identified Issues & Gaps

1. **Student Verification During Scanning**
   - Current: Only scans QR code
   - Missing: Student photo/identification during scan
   - Recommendation: Show student avatar during scan confirmation

2. **Live Student List in Scanner**
   - Current: Scanner runs but no real-time student list visible
   - Missing: Display list of who already scanned, who's still pending
   - Recommendation: Add real-time checklist during scanning

3. **Manual Absensi (Manual Attendance)**
   - Current: Tab exists but minimal functionality
   - Missing: Search student + mark status quickly
   - Recommendation: Implement quick manual mark interface

4. **Class Management**
   - Current: Teacher can see assigned classes
   - Missing: Cannot switch between classes easily during scanning
   - Recommendation: Add class selector in scanner interface

5. **Attendance Syncing Issues**
   - Current: Polling every 3 seconds
   - Missing: Real-time feedback on scan completion
   - Recommendation: Add Livewire event dispatch for instant updates

6. **Student Details Popup**
   - Current: No way to see student details during testing
   - Missing: Click student -> see details modal
   - Recommendation: Add detail modal with student info

---

## 🎯 Phase 2 Enhancement Priorities

### Priority 1: Student Verification During Scanner (HIGH)
**Why**: Critical for accuracy & preventing cheating
**What to Add**:
- [ ] Show student photo during/after scan
- [ ] Confirm "Is this the correct student?" before recording
- [ ] Allow quick cancel if wrong scan
- [ ] Add student avatar in scan results

**Files to Modify**:
- `app/Livewire/AttendanceScanner.php`
- `resources/views/livewire/attendance-scanner.blade.php`

**Estimated Complexity**: Medium
**Tests to Add**: 3-4 tests for student verification flow

---

### Priority 2: Real-Time Student Checklist in Scanner (HIGH)
**Why**: Teachers need visibility of who's scanned vs who's pending
**What to Add**:
- [ ] Left sidebar showing current class students
- [ ] Real-time checklist with checkmarks for scanned students
- [ ] Pending count vs scanned count
- [ ] Search student in list
- [ ] Sort by status (scanned first, pending)

**Files to Modify**:
- `resources/views/livewire/attendance-scanner.blade.php`
- `app/Livewire/AttendanceScanner.php`

**Estimated Complexity**: Medium
**Tests to Add**: 4-5 tests for checklist functionality

---

### Priority 3: Quick Manual Attendance (MEDIUM)
**Why**: For students who can't use QR (forgot card, technical issues)
**What to Add**:
- [ ] Search bar in scanner interface
- [ ] Click student name -> mark status (present/late/sick/absent)
- [ ] Quick confirm dialog
- [ ] Add to scanned list immediately
- [ ] Keyboard shortcuts for faster marking

**Files to Modify**:
- `resources/views/livewire/attendance-scanner.blade.php`
- `app/Livewire/AttendanceScanner.php`

**Estimated Complexity**: Medium
**Tests to Add**: 3-4 tests for manual marking

---

### Priority 4: Class Switching in Scanner (MEDIUM)
**Why**: Teachers may teach multiple classes throughout the day
**What to Add**:
- [ ] Dropdown selector for switching classes
- [ ] Clear checklist when switching
- [ ] Save session per class
- [ ] Show current class name prominently

**Files to Modify**:
- `resources/views/livewire/attendance-scanner.blade.php`
- `app/Livewire/AttendanceScanner.php`

**Estimated Complexity**: Low
**Tests to Add**: 2-3 tests for class switching

---

### Priority 5: Session Log & Export (MEDIUM)
**Why**: Teacher needs record of which students were scanned when
**What to Add**:
- [ ] Log all scans with timestamp
- [ ] Export session log as CSV/Excel
- [ ] Session history (past scanning sessions)
- [ ] Mark session as complete

**Files to Modify**:
- `app/Livewire/AttendanceScanner.php`
- Create new `resources/views/livewire/scanner-session-log.blade.php`

**Estimated Complexity**: Medium
**Tests to Add**: 3-4 tests for session logging

---

### Priority 6: Attendance Reason/Notes (LOW)
**Why**: Context for late/absent reasons
**What to Add**:
- [ ] Add notes field when marking late/absent
- [ ] Predefined reasons (sakit, izin, dll)
- [ ] Save to attendance.notes column
- [ ] Display notes in reports

**Files to Modify**:
- `resources/views/livewire/attendance-scanner.blade.php`
- `resources/views/livewire/bulk-attendance-update.blade.php`
- `app/Livewire/AttendanceScanner.php`
- `app/Livewire/BulkAttendanceUpdate.php`

**Estimated Complexity**: Low
**Tests to Add**: 2-3 tests for reason notes

---

## 📋 Recommended Implementation Order

1. **Phase 2.1**: Student Verification + Real-Time Checklist (Priority 1+2)
   - Estimated: 3-4 days
   - Tests: 7-9 new tests
   - Impact: High (Core user experience)

2. **Phase 2.2**: Class Switching + Quick Manual (Priority 3+4)
   - Estimated: 2-3 days
   - Tests: 4-6 new tests
   - Impact: High (Workflow efficiency)

3. **Phase 2.3**: Session Log + Export (Priority 5)
   - Estimated: 2 days
   - Tests: 3-4 new tests
   - Impact: Medium (Reporting/audit)

4. **Phase 2.4**: Notes/Reasons (Priority 6)
   - Estimated: 1 day
   - Tests: 2-3 new tests
   - Impact: Low (Nice to have)

---

## 🔧 Technical Requirements

### Database Changes (if needed)
- `attendances` table already has `notes` column - no migration needed
- No new tables required for current planning

### UI/UX Updates
- Split scanner view into 2-column layout (students list + scanner)
- Add modal for student confirmation
- Add quick manual entry modal
- Add session completion modal

### API/Endpoints
- May need to add endpoint for quick checklist fetch (optimize polling)
- May need batch update for manual marking

### Performance Considerations
- Real-time polling might need optimization
- Large class lists (100+ students) need lazy loading or pagination
- Consider Livewire lazy hydration for scanner view

---

## 👥 Which Priority Should We Start?

Recommended: **Priority 1 (Student Verification)**
- Highest impact on data accuracy
- Improves teacher confidence in system
- Builds foundation for other enhancements

Following: **Priority 2 (Real-Time Checklist)**
- Most requested feature (visibility of who's scanned)
- Works well with Priority 1
- Improves workflow

---

## Next Steps

1. Choose starting priority from above
2. Create feature branch for development
3. Implement with TDD approach
4. Add feature tests
5. Update UI components
6. Merge and test in staging

Which priority would you like to start with?
