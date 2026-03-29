<?php

namespace App\Filament\Resources\StudentClassResource\Pages;

use App\Filament\Resources\StudentClassResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStudentClasses extends ManageRecords
{
    protected static string $resource = StudentClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
