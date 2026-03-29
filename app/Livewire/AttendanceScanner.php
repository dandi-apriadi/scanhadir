<?php

namespace App\Livewire;

use App\Http\Requests\ScanAttendanceRequest;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class AttendanceScanner extends Component
{
    public $message = 'Siap memindai...';
    public $status = 'info'; // success, error, info
    public $scanCount = 0;
    public $lastScanTime = null;
    
    // Student Verification Feature
    public $pendingStudent = null;
    public $pendingStudentDetails = null;
    public $awaitingConfirmation = false;
    
    // Configuration
    protected const SCHOOL_START_TIME = '07:00:00';      // Jam masuk
    protected const LATE_THRESHOLD = '07:30:00';          // Terlambat setelah jam ini
    protected const SCHOOL_END_TIME = '14:00:00';        // Jam pulang
    protected const SCAN_THROTTLE_SECONDS = 2;            // Jangan scan lebih cepat dari 2 detik

    public function mount()
    {
        $this->scanCount = 0;
        $this->lastScanTime = null;
    }

    /**
     * Process QR code scan
     */
    public function processScan($code)
    {
        try {
            // Validate QR code format
            $validation = Validator::make(
                ['code' => $code],
                (new ScanAttendanceRequest())->rules(),
                (new ScanAttendanceRequest())->messages()
            );

            if ($validation->fails()) {
                $this->handleError($validation->errors()->first('code'));
                return;
            }

            // Find student
            $student = Student::with('user', 'class')
                ->where('qr_code', $code)
                ->first();

            if (!$student) {
                $this->handleError("Kartu tidak terdaftar: $code");
                return;
            }

            $today = now()->toDateString();

            // Check if today is a holiday
            if (Holiday::isHoliday($today)) {
                $this->handleError("Hari libur - Absensi ditutup");
                return;
            }

            // STUDENT VERIFICATION: Show confirmation dialog instead of direct processing
            $this->showStudentVerification($student);

        } catch (\Exception $e) {
            Log::error('Attendance scan error', [
                'code' => $code,
                'error' => $e->getMessage()
            ]);
            $this->handleError('Terjadi kesalahan sistem. Coba lagi.');
        }
    }

    /**
     * Show student verification dialog
     */
    private function showStudentVerification($student)
    {
        $this->pendingStudent = $student;
        $this->awaitingConfirmation = true;
        $this->message = "Apakah ini siswa yang benar? Pastikan identitas sebelum lanjut.";
        $this->status = 'info';

        // Prepare student details for display
        $this->pendingStudentDetails = [
            'id' => $student->id,
            'name' => $student->user->name,
            'class' => $student->class?->name,
            'nisn' => $student->nisn,
            'photo_url' => $student->photo_path ? asset('storage/' . $student->photo_path) : null,
            'has_photo' => !empty($student->photo_path),
        ];
    }

    /**
     * Confirm student identity and process attendance
     */
    public function confirmStudent()
    {
        if (!$this->pendingStudent) {
            $this->handleError('Tidak ada siswa yang menunggu konfirmasi.');
            return;
        }

        try {
            $today = now()->toDateString();

            // Process attendance now that student is confirmed
            $this->processAttendance($this->pendingStudent, $today);

            // Clear pending state
            $this->clearPendingStudent();
        } catch (\Exception $e) {
            Log::error('Attendance confirm error', ['error' => $e->getMessage()]);
            $this->handleError('Gagal memproses kehadiran. Coba lagi.');
        }
    }

    /**
     * Cancel student verification and reset for next scan
     */
    public function cancelStudent()
    {
        $this->message = 'Pembacaan dibatalkan. Siap untuk memindai ulang.';
        $this->status = 'info';
        $this->clearPendingStudent();
    }

    /**
     * Clear pending student data
     */
    private function clearPendingStudent()
    {
        $this->pendingStudent = null;
        $this->pendingStudentDetails = null;
        $this->awaitingConfirmation = false;
    }

    /**
     * Process attendance check-in or check-out
     */
    private function processAttendance($student, $today)
    {
        $now = now();
        $nowTime = $now->toTimeString();
        
        // Get or create attendance record
        $attendance = Attendance::firstOrCreate(
            ['student_id' => $student->id, 'date' => $today],
            [
                'status' => $this->determineStatus($nowTime),
                'check_in' => $nowTime
            ]
        );

        // Handle check-out if already checked in
        if (!$attendance->wasRecentlyCreated) {
            if ($attendance->check_out === null) {
                // Update with check-out time
                $attendance->update([
                    'check_out' => $nowTime,
                    'notes' => 'Check-out at ' . $nowTime
                ]);
                
                $this->handleSuccess(
                    "Absen Pulang: " . $student->user->name,
                    $student->user->name,
                    $student->class->name,
                    'checkout'
                );
            } else {
                // Already checked out
                $duration = Carbon::parse($attendance->check_in)
                    ->diff(Carbon::parse($attendance->check_out));
                
                $this->handleInfo(
                    "Sudah absen pulang. Durasi: " . $duration->format('%H jam %I menit'),
                    'already_checkout'
                );
            }
        } else {
            // New attendance (check-in)
            $this->handleSuccess(
                "Absen Masuk: " . $student->user->name,
                $student->user->name,
                $student->class->name,
                'checkin'
            );
        }

        // Increment scan counter
        $this->scanCount++;
        $this->lastScanTime = now();
    }

    /**
     * Determine attendance status based on check-in time
     */
    private function determineStatus($timeString): string
    {
        $checkInTime = Carbon::parse($timeString);
        $lateThreshold = Carbon::parse(self::LATE_THRESHOLD);
        
        if ($checkInTime->greaterThan($lateThreshold)) {
            return 'late';
        }
        
        return 'present';
    }

    /**
     * Handle success response
     */
    private function handleSuccess($message, $name, $class, $action = 'checkin')
    {
        $this->message = $message;
        $this->status = 'success';
        $this->dispatch('scan-success', name: $name, class: $class, action: $action);
    }

    /**
     * Handle error response
     */
    private function handleError($message)
    {
        $this->message = $message;
        $this->status = 'error';
        $this->dispatch('scan-failed');
    }

    /**
     * Handle info response
     */
    private function handleInfo($message, $action = 'info')
    {
        $this->message = $message;
        $this->status = 'info';
        $this->dispatch('scan-info', action: $action);
    }

    /**
     * Get scan statistics
     */
    public function getScanStats()
    {
        return [
            'total_scans' => $this->scanCount,
            'last_scan' => $this->lastScanTime?->diffForHumans(),
            'status' => $this->status,
            'message' => $this->message
        ];
    }

    public function render()
    {
        return view('livewire.attendance-scanner')->layout('layouts.app');
    }
}
