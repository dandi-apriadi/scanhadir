<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export_pdf')
                ->label('Cetak Laporan PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $attendances = \App\Models\Attendance::with(['student.user', 'student.class'])->get();
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.attendance', [
                        'attendances' => $attendances
                    ]);
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        "laporan-presensi-" . now()->format('Y-m-d') . ".pdf"
                    );
                }),
        ];
    }
}
