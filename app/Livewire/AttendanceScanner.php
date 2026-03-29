<?php

namespace App\Livewire;

use App\Http\Requests\ScanAttendanceRequest;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class AttendanceScanner extends Component
{
    public $message = 'Siap memindai...';
    public $status = 'info'; // success, error, info

    public function processScan($code)
    {
        $validation = Validator::make(
            ['code' => $code],
            (new ScanAttendanceRequest())->rules(),
            (new ScanAttendanceRequest())->messages()
        );

        if ($validation->fails()) {
            $this->message = $validation->errors()->first('code');
            $this->status = 'error';
            $this->dispatch('scan-failed');

            return;
        }

        $student = Student::with('user', 'class')->where('qr_code', $code)->first();

        if (!$student) {
            $this->message = "Kartu tidak terdaftar: $code";
            $this->status = 'error';
            $this->dispatch('scan-failed');
            return;
        }

        $today = now()->toDateString();

        // Check if today is a holiday
        if (Holiday::isHoliday($today)) {
            $this->message = "Hari libur - Absensi ditutup";
            $this->status = 'error';
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

    public function render()
    {
        return view('livewire.attendance-scanner')->layout('layouts.app');
    }
}
