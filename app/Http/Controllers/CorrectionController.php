<?php

namespace App\Http\Controllers;

use App\Models\Correction;
use App\Models\Schedule;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CorrectionController extends Controller
{
    /**
     * Display a listing of corrections.
     */
    public function index(Request $request): View
    {
        $approvalStatusOptions = (array) config('attendance.correction_approval_statuses', []);
        $approvalStatusKeys = array_keys($approvalStatusOptions);

        $query = Correction::with(['student.user', 'schedule.subject', 'schedule.class']);

        $selectedStatus = (string) $request->input('status', '');
        if ($selectedStatus !== '' && in_array($selectedStatus, $approvalStatusKeys, true)) {
            $query->where('approval_status', $selectedStatus);
        } else {
            $selectedStatus = '';
        }

        $corrections = $query->latest()->paginate(10);

        $summaryCounts = [
            'total' => Correction::query()->count(),
        ];

        foreach ($approvalStatusKeys as $statusKey) {
            $summaryCounts[$statusKey] = Correction::query()
                ->where('approval_status', $statusKey)
                ->count();
        }

        return view('reports.correction', compact('corrections', 'approvalStatusOptions', 'summaryCounts', 'selectedStatus'));
    }

    /**
     * Show the form for creating a new correction.
     */
    public function create(): View
    {
        $statusOptions = (array) config('attendance.correction_statuses', []);
        $approvalStatusOptions = (array) config('attendance.correction_approval_statuses', []);

        $students = Student::with(['user', 'class'])->orderBy('nisn')->get();
        $schedules = Schedule::with(['class', 'subject'])
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return view('reports.correction-edit', compact('students', 'schedules', 'statusOptions', 'approvalStatusOptions'));
    }

    /**
     * Store a newly created correction.
     */
    public function store(Request $request): RedirectResponse
    {
        $statusKeys = array_keys((array) config('attendance.correction_statuses', []));

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'schedule_id' => 'required|exists:schedules,id',
            'tanggal' => 'required|date',
            'status_lama' => ['required', Rule::in($statusKeys)],
            'status_baru' => ['required', Rule::in($statusKeys)],
            'alasan' => 'required|string|min:10',
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('dokumen')) {
            $validated['dokumen'] = $request->file('dokumen')->store('corrections', 'public');
        }

        $validated['status'] = 'pending';
        $validated['approval_status'] = 'pending';
        $validated['user_id'] = auth()->id();

        Correction::create($validated);

        return redirect()
            ->route('correction')
            ->with('success', 'Permintaan koreksi berhasil dibuat');
    }

    /**
     * Show the form for editing the specified correction.
     */
    public function edit(Correction $correction): View
    {
        $statusOptions = (array) config('attendance.correction_statuses', []);
        $approvalStatusOptions = (array) config('attendance.correction_approval_statuses', []);

        $students = Student::with(['user', 'class'])->orderBy('nisn')->get();
        $schedules = Schedule::with(['class', 'subject'])
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return view('reports.correction-edit', compact('correction', 'students', 'schedules', 'statusOptions', 'approvalStatusOptions'));
    }

    /**
     * Update the specified correction.
     */
    public function update(Request $request, Correction $correction): RedirectResponse
    {
        $statusKeys = array_keys((array) config('attendance.correction_statuses', []));

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'schedule_id' => 'required|exists:schedules,id',
            'tanggal' => 'required|date',
            'status_lama' => ['required', Rule::in($statusKeys)],
            'status_baru' => ['required', Rule::in($statusKeys)],
            'alasan' => 'required|string|min:10',
            'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('dokumen')) {
            $validated['dokumen'] = $request->file('dokumen')->store('corrections', 'public');
        }

        $correction->update($validated);

        return redirect()
            ->route('correction')
            ->with('success', 'Permintaan koreksi berhasil diperbarui');
    }

    /**
     * Approve the correction.
     */
    public function approve(Correction $correction): RedirectResponse
    {
        DB::transaction(function () use ($correction) {
            $correction->update([
                'approval_status' => 'approved',
                'approved_by' => auth()->id(),
            ]);

            // Apply the correction to attendance
            $mapping = config('attendance.correction_to_absensi', []);
            $newStatus = $mapping[$correction->status_baru] ?? $correction->status_baru;

            $correction->student->attendances()
                ->where('date', $correction->tanggal)
                ->where('schedule_id', $correction->schedule_id)
                ->update(['status' => $newStatus]);
        });

        return redirect()
            ->route('correction')
            ->with('success', 'Koreksi berhasil disetujui dan diterapkan');
    }

    /**
     * Reject the correction.
     */
    public function reject(Request $request, Correction $correction): RedirectResponse
    {
        $correction->update([
            'approval_status' => 'rejected',
            'rejected_by' => auth()->id(),
        ]);

        return redirect()
            ->route('correction')
            ->with('success', 'Koreksi berhasil ditolak');
    }
}
