<?php

namespace App\Livewire;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentDashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        
        if (!$user || $user->role !== 'student') {
            return redirect()->route('login');
        }

        $student = Student::with('class')->where('user_id', $user->id)->first();
        $history = Attendance::where('student_id', $student->id)
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get();

        return view('livewire.student-dashboard', [
            'student' => $student,
            'history' => $history,
        ])->layout('layouts.app');
    }
}
