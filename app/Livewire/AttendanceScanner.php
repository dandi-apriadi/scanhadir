<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Student;
use Livewire\Component;

class AttendanceScanner extends Component
{
    public $message = 'Siap memindai...';
    public $status = 'info'; // success, error, info

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
