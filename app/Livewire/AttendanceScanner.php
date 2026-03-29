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

            // Process check-in/check-out
            $this->processAttendance($student, $today);

        } catch (\Exception $e) {
            Log::error('Attendance scan error', [
                'code' => $code,
                'error' => $e->getMessage()
            ]);
            $this->handleError('Terjadi kesalahan sistem. Coba lagi.');
        }
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
