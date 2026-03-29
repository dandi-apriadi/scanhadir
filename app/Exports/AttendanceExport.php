<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class AttendanceExport
{
    public function __construct(private readonly Collection $rows)
    {
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Siswa',
            'Kelas',
            'Check In',
            'Check Out',
            'Status',
            'Catatan',
        ];
    }

    public function rows(): Collection
    {
        return $this->rows;
    }

    public function toCsvString(): string
    {
        $stream = fopen('php://temp', 'r+');
        fputcsv($stream, $this->headings());

        foreach ($this->rows as $row) {
            fputcsv($stream, [
                $row['date'] ?? '',
                $row['student_name'] ?? '',
                $row['class_name'] ?? '',
                $row['check_in'] ?? '',
                $row['check_out'] ?? '',
                $row['status'] ?? '',
                $row['notes'] ?? '',
            ]);
        }

        rewind($stream);
        $csv = stream_get_contents($stream) ?: '';
        fclose($stream);

        return $csv;
    }
}
