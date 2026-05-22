<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\StudentClass;
use Illuminate\Support\Carbon;
use OpenSpout\Writer\XLSX\Writer;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Fill;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\Common\Entity\Style\CellAlignment;

class AttendanceExportService
{
    /**
     * Export attendance records to XLSX format
     *
     * @param \Illuminate\Support\Collection $attendances
     * @param array $options
     * @return string Path to generated file
     */
    public function exportToXLSX($attendances, array $options = [])
    {
        $fileName = 'attendance_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        $filePath = storage_path('app/exports/' . $fileName);

        // Ensure directory exists
        if (!file_exists(dirname($filePath))) {
            mkdir(dirname($filePath), 0755, true);
        }

        $writer = new Writer();
        $writer->openToFile($filePath);

        // Add title and metadata sheet
        $titleSheet = $writer->getCurrentSheet();
        $titleSheet->setName('Attendance Report');
        $this->writeHeader($writer, $options);

        // Add attendance data
        $this->writeAttendanceData($writer, $attendances);

        // Add summary sheet
        $summarySheet = $writer->addNewSheetAndMakeItCurrent();
        $summarySheet->setName('Summary');
        $this->writeSummary($writer, $attendances);

        $writer->close();

        return $filePath;
    }

    /**
     * Write header and metadata to sheet
     */
    private function writeHeader($writer, array $options)
    {
        // Title row
        $writer->addRow(Row::fromValues(['ATTENDANCE REPORT']));

        // Metadata rows
        $writer->addRow(Row::fromValues(["Report Date: " . now()->format('Y-m-d H:i:s')]));
        if (isset($options['date_from']) && isset($options['date_to'])) {
            $writer->addRow(Row::fromValues(["Period: " . $options['date_from'] . " to " . $options['date_to']]));
        }
        if (isset($options['class_name'])) {
            $writer->addRow(Row::fromValues(["Class: " . $options['class_name']]));
        }

        // Empty row
        $writer->addRow(Row::fromValues([]));

        // Column headers
        $headers = [
            'Student Name',
            'NISN',
            'Class',
            'Date',
            'Check-in Time',
            'Check-out Time',
            'Status',
            'Created At',
        ];
        $writer->addRow(Row::fromValues($headers));
    }

    /**
     * Write attendance data rows
     */
    private function writeAttendanceData($writer, $attendances)
    {
        $i = 0;
        foreach ($attendances as $attendance) {
            $i++;
            $data = [
                $attendance->student?->user?->name ?? 'N/A',
                $attendance->student?->nisn ?? 'N/A',
                $attendance->student?->class?->name ?? 'N/A',
                $attendance->date ? ($attendance->date instanceof \Carbon\Carbon ? $attendance->date->format('Y-m-d') : $attendance->date) : 'N/A',
                $attendance->check_in ? ($attendance->check_in instanceof \Carbon\Carbon ? $attendance->check_in->format('H:i:s') : $attendance->check_in) : '-',
                $attendance->check_out ? ($attendance->check_out instanceof \Carbon\Carbon ? $attendance->check_out->format('H:i:s') : $attendance->check_out) : '-',
                strtoupper($attendance->status),
                $attendance->created_at ? $attendance->created_at->format('Y-m-d H:i:s') : 'N/A',
            ];
            $writer->addRow(Row::fromValues($data));

            // Limit rows per batch to prevent memory issues
            if ($i % 1000 === 0) {
                // No need for empty row in batch but kept for spacing if desired
                // $writer->addRow(Row::fromValues([]));
            }
        }
    }

    /**
     * Write summary statistics
     */
    private function writeSummary($writer, $attendances)
    {
        // Summary title
        $writer->addRow(Row::fromValues(['SUMMARY STATISTICS']));
        $writer->addRow(Row::fromValues([]));

        // Total counts
        $totalAttendances = $attendances->count();
        $presentCount = $attendances->where('status', 'Hadir')->count();
        $lateCount = $attendances->where('status', 'Telat')->count();
        $sickCount = $attendances->where('status', 'Sakit')->count();
        $excusedCount = $attendances->where('status', 'Izin')->count();
        $absentCount = $attendances->where('status', 'Alpa')->count();

        // Summary rows
        $writer->addRow(Row::fromValues(['Metric', 'Count', 'Percentage']));
        $writer->addRow(Row::fromValues(['Total', $totalAttendances, '100%']));
        $writer->addRow(Row::fromValues(['Hadir', $presentCount, $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100, 2) . '%' : '0%']));
        $writer->addRow(Row::fromValues(['Telat', $lateCount, $totalAttendances > 0 ? round(($lateCount / $totalAttendances) * 100, 2) . '%' : '0%']));
        $writer->addRow(Row::fromValues(['Sakit', $sickCount, $totalAttendances > 0 ? round(($sickCount / $totalAttendances) * 100, 2) . '%' : '0%']));
        $writer->addRow(Row::fromValues(['Izin', $excusedCount, $totalAttendances > 0 ? round(($excusedCount / $totalAttendances) * 100, 2) . '%' : '0%']));
        $writer->addRow(Row::fromValues(['Alpa', $absentCount, $totalAttendances > 0 ? round(($absentCount / $totalAttendances) * 100, 2) . '%' : '0%']));

        // Group by date
        $writer->addRow(Row::fromValues([]));
        $writer->addRow(Row::fromValues(['KEHADIRAN PER TANGGAL']));
        $writer->addRow(Row::fromValues(['Tanggal', 'Hadir', 'Telat', 'Alpa', 'Sakit', 'Izin']));

        $dateGroups = $attendances->groupBy('date')->sortBy(function($item, $key) {
            return $key;
        });

        foreach ($dateGroups as $date => $records) {
            $writer->addRow(Row::fromValues([
                $date,
                $records->where('status', 'Hadir')->count(),
                $records->where('status', 'Telat')->count(),
                $records->where('status', 'Alpa')->count(),
                $records->where('status', 'Sakit')->count(),
                $records->where('status', 'Izin')->count(),
            ]));
        }

        // Group by class (if multiple classes)
        $classes = $attendances->groupBy('student.class_id');
        if ($classes->count() > 1) {
            $writer->addRow(Row::fromValues([]));
            $writer->addRow(Row::fromValues(['KEHADIRAN PER KELAS']));
            $writer->addRow(Row::fromValues(['Kelas', 'Total', 'Hadir', 'Telat', 'Alpa', 'Sakit', 'Izin']));

            foreach ($classes as $classRecords) {
                $className = $classRecords->first()?->student?->class?->name ?? 'N/A';
                $writer->addRow(Row::fromValues([
                    $className,
                    $classRecords->count(),
                    $classRecords->where('status', 'Hadir')->count(),
                    $classRecords->where('status', 'Telat')->count(),
                    $classRecords->where('status', 'Alpa')->count(),
                    $classRecords->where('status', 'Sakit')->count(),
                    $classRecords->where('status', 'Izin')->count(),
                ]));
            }
        }
    }

    /**
     * Export class-specific attendance with student details
     */
    public function exportClassAttendanceXLSX(StudentClass $class, $dateFrom, $dateTo)
    {
        $attendances = Attendance::query()
            ->with(['student.user', 'student.class'])
            ->whereHas('student', fn($q) => $q->where('class_id', $class->id))
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->orderBy('date')
            ->orderBy('student_id')
            ->get();

        $options = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'class_name' => $class->name,
        ];

        return $this->exportToXLSX($attendances, $options);
    }

    /**
     * Get download URL for exported file
     */
    public function getDownloadUrl($filePath)
    {
        $fileName = basename($filePath);
        return route('attendance.download', ['file' => $fileName]);
    }
}
