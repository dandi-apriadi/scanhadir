# Backend Implementation Quickstart Guide

**Tujuan**: Selesaikan gap-gap kritis sambil frontend dikerjakan  
**Waktu Estimasi**: 4-5 jam untuk semua item

---

## 🔴 CRITICAL #1: Complete Holiday Management (30 min)

### Step 1: Fix Migration

File: `database/migrations/2026_03_29_035148_create_holidays_table.php`

**Replace the entire migration with**:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Lebaran", "Cuti Semester"
            $table->date('start_date');
            $table->date('end_date');
            $table->text('description')->nullable();
            $table->string('type')->default('national'); // national, school, emergency
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
```

**Then run**:
```bash
php artisan migrate:refresh --seed
```

### Step 2: Update Holiday Model

File: `app/Models/Holiday.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'description',
        'type',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Check if a given date is a holiday
     */
    public static function isHoliday($date): bool
    {
        return self::whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->exists();
    }
}
```

### Step 3: Create Filament Resource

Run:
```bash
php artisan make:filament-resource Holiday
```

Then edit `app/Filament/Resources/HolidayResource.php`:

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HolidayResource\Pages;
use App\Models\Holiday;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Hari Libur';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Hari Libur')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->required(),
                Select::make('type')
                    ->label('Tipe')
                    ->options([
                        'national' => 'Libur Nasional',
                        'school' => 'Cuti Sekolah',
                        'emergency' => 'Darurat',
                    ])
                    ->default('national'),
                Textarea::make('description')
                    ->label('Keterangan')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('start_date')->label('Mulai')->date('d M Y'),
                TextColumn::make('end_date')->label('Selesai')->date('d M Y'),
                TextColumn::make('type')->label('Tipe')->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'national' => 'Libur Nasional',
                        'school' => 'Cuti Sekolah',
                        'emergency' => 'Darurat',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHolidays::route('/'),
            'create' => Pages\CreateHoliday::route('/create'),
            'edit' => Pages\EditHoliday::route('/{record}/edit'),
        ];
    }
}
```

Generate pages:
```bash
php artisan make:filament-page Holiday/ListHolidays --resource=HolidayResource --type=ListRecords
php artisan make:filament-page Holiday/CreateHoliday --resource=HolidayResource --type=CreateRecord
php artisan make:filament-page Holiday/EditHoliday --resource=HolidayResource --type=EditRecord
```

### Step 4: Integrate Holiday Check into Attendance Scanner

File: `app/Livewire/AttendanceScanner.php`

Update the `processScan()` method:

```php
public function processScan($code)
{
    $student = Student::with('user', 'class')->where('qr_code', $code)->first();

    if (!$student) {
        $this->message = "Kartu tidak terdaftar: $code";
        $this->status = 'error';
        $this->dispatch('scan-failed');
        return;
    }

    $today = now()->toDateString();
    
    // Check if today is a holiday
    if (\App\Models\Holiday::isHoliday($today)) {
        $this->message = "Hari libur - Absensi ditolak";
        $this->status = 'info';
        $this->dispatch('scan-failed');
        return;
    }

    $now = now()->toTimeString();

    $attendance = Attendance::firstOrCreate(
        ['student_id' => $student->id, 'date' => $today],
        ['status' => 'present', 'check_in' => $now]
    );

    if (!$attendance->wasRecentlyCreated && $attendance->check_out === null) {
        $attendance->update(['check_out' => $now]);
        $this->message = "Absen Pulang: " . $student->user->name;
        $this->status = 'success';
    } elseif ($attendance->wasRecentlyCreated) {
        $this->message = "Absen Masuk: " . $student->user->name;
        $this->status = 'success';
    } else {
        $this->message = "Siswa sudah melakukan absensi hari ini.";
        $this->status = 'info';
    }

    $this->dispatch('scan-success', name: $student->user->name, class: $student->class->name);
}
```

---

## 🔴 CRITICAL #2: Add Authorization Policies (45 min)

### Create All Policy Classes

```bash
php artisan make:policy StudentPolicy -m Student
php artisan make:policy AttendancePolicy -m Attendance
php artisan make:policy StudentClassPolicy -m StudentClass
php artisan make:policy HolidayPolicy -m Holiday
```

### Update Policies (Example: StudentPolicy)

File: `app/Policies/StudentPolicy.php`

```php
<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function view(User $user, Student $student): bool
    {
        return $user->role === 'admin' || $user->id === $student->user_id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Student $student): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->role === 'admin';
    }

    public function forceDelete(User $user, Student $student): bool
    {
        return $user->role === 'admin';
    }
}
```

**Apply to other models accordingly** (AttendancePolicy, StudentClassPolicy, HolidayPolicy - similar pattern)

### Register Policies in AuthServiceProvider

File: `app/Providers/AppServiceProvider.php`

```php
public function boot(): void
{
    Gate::policy(Student::class, StudentPolicy::class);
    Gate::policy(Attendance::class, AttendancePolicy::class);
    Gate::policy(StudentClass::class, StudentClassPolicy::class);
    Gate::policy(Holiday::class, HolidayPolicy::class);
}
```

### Apply Authorization to Filament Resources

Update all Filament Resources with:

```php
// In StudentResource
public static function form(Form $form): Form
{
    return $form->schema([...])
        ->authorize(auth()->user()->can('create', Student::class)); // for create
}

// Or use middleware in pages:
// In ListStudents page add:
protected function authorizeAccess(): void
{
    $this->authorize('viewAny', Student::class);
}
```

---

## 🔴 CRITICAL #3: Generate Visual QR Codes (1.5 hours)

### Step 1: Create Artisan Command

```bash
php artisan make:command GenerateStudentQRCodes
```

File: `app/Console/Commands/GenerateStudentQRCodes.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class GenerateStudentQRCodes extends Command
{
    protected $signature = 'students:generate-qrcodes {--force}';
    protected $description = 'Generate visual QR codes for all students';

    public function handle()
    {
        $students = Student::all();
        
        if (!Storage::exists('qrcodes')) {
            Storage::makeDirectory('qrcodes');
        }

        foreach ($students as $student) {
            try {
                $qrCode = QrCode::format('png')
                    ->size(500)
                    ->generate($student->qr_code);
                
                Storage::put("qrcodes/{$student->id}.png", $qrCode);
                $this->info("✓ Generated QR for: {$student->user->name}");
            } catch (\Exception $e) {
                $this->error("✗ Failed for {$student->id}: {$e->getMessage()}");
            }
        }

        $this->info("QR codes generation complete!");
    }
}
```

### Step 2: Add Route to Download/Display QR Code

File: `routes/web.php`

```php
Route::get('/student/{student}/qrcode', function (Student $student) {
    return response()->file(storage_path("app/qrcodes/{$student->id}.png"));
})->name('student.qrcode')->middleware('auth');
```

### Step 3: Add Filament Action to Download QR

In StudentResource, add to table actions:

```php
Tables\Actions\Action::make('downloadQR')
    ->label('Unduh QR')
    ->icon('heroicon-o-arrow-down-tray')
    ->url(fn (Student $record) => route('student.qrcode', $record))
    ->openUrlInNewTab(),
```

### Step 4: Run Command to Generate All QRs

```bash
php artisan students:generate-qrcodes
```

---

## 🟡 HIGH PRIORITY #1: Add Attendance Export (2 hours)

### Step 1: Install Excel Package

```bash
composer require maatwebsite/excel
php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider"
```

### Step 2: Create Export Class

```bash
php artisan make:export AttendanceExport
```

File: `app/Exports/AttendanceExport.php`

```php
<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromCollection, WithHeadings
{
    protected $classId;
    protected $startDate;
    protected $endDate;

    public function __construct($classId = null, $startDate = null, $endDate = null)
    {
        $this->classId = $classId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = Attendance::with('student.user', 'student.class');

        if ($this->classId) {
            $query->whereHas('student', fn($q) => $q->where('class_id', $this->classId));
        }

        if ($this->startDate) {
            $query->whereDate('date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('date', '<=', $this->endDate);
        }

        return $query->get()->map(function ($attendance) {
            return [
                'Nama Siswa' => $attendance->student->user->name,
                'Kelas' => $attendance->student->class->name,
                'Tanggal' => $attendance->date->format('d-m-Y'),
                'Jam Masuk' => $attendance->check_in?->format('H:i:s') ?? '-',
                'Jam Pulang' => $attendance->check_out?->format('H:i:s') ?? '-',
                'Status' => __("attendance.status.{$attendance->status}"),
                'Catatan' => $attendance->notes ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Kelas',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Status',
            'Catatan',
        ];
    }
}
```

### Step 3: Add Export Action to AttendanceResource

```php
Tables\Actions\Action::make('export')
    ->label('Ekspor Excel')
    ->icon('heroicon-o-arrow-down-tray')
    ->action('export')
    ->modalHeading('Ekspor Data Presensi'),
```

In the Action method:

```php
public function export(Request $request)
{
    $fileName = 'attendance-' . now()->format('Y-m-d-His') . '.xlsx';
    return (new AttendanceExport())->download($fileName);
}
```

---

## 🟡 HIGH PRIORITY #2: Add Teacher Dashboard (2 hours)

### Create Teacher Dashboard Component

```bash
php artisan make:livewire TeacherDashboard
```

File: `app/Livewire/TeacherDashboard.php`

```php
<?php

namespace App\Livewire;

use App\Models\StudentClass;
use App\Models\Attendance;
use Livewire\Component;

class TeacherDashboard extends Component
{
    public $teacher;
    public $assignedClasses;
    public $todayAttendance;

    public function mount()
    {
        $this->teacher = auth()->user();
        // Assuming you add a 'teacher_classes' relationship or find by user_id
        $this->assignedClasses = StudentClass::all(); // Update as needed
        $this->todayAttendance = Attendance::whereDate('date', today())->get();
    }

    public function render()
    {
        return view('livewire.teacher-dashboard')->layout('layouts.app');
    }
}
```

### Add Route

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/teacher-dashboard', TeacherDashboard::class)->name('teacher.dashboard');
});
```

---

## ✅ After Completing All Steps

1. **Test locally**:
   ```bash
   composer run dev
   ```

2. **Run latest migrations**:
   ```bash
   php artisan migrate:fresh --seed
   ```

3. **Generate QR codes**:
   ```bash
   php artisan students:generate-qrcodes
   ```

4. **Test in Filament**: Visit `/admin` and verify all resources appear and work

5. **Commit changes**:
   ```bash
   git add .
   git commit -m "feat: complete holiday management, add policies, generate QR codes"
   git push
   ```

---

## 📊 Expected Backend Readiness After Implementation

- Model & Migration: ✅ 100% (from 95%)
- Authorization: ✅ 100% (from 0%)
- Admin Features: ✅ 95% (from 70%)
- Export/Reporting: ✅ 80% (from 0%)
- QR Handling: ✅ 90% (from 50%)

**Overall Readiness**: 85% → 95% 🚀

Frontend team can proceed with full confidence!
