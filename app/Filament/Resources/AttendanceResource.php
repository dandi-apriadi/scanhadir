<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentClass;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Sistem Presensi';
    protected static ?string $modelLabel = 'Presensi';
    protected static ?string $pluralModelLabel = 'Data Presensi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Absensi')
                    ->schema([
                        Forms\Components\Select::make('student_id')
                            ->label('Nama Siswa')
                            ->relationship('student.user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DatePicker::make('date')
                            ->label('Tanggal')
                            ->default(now())
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'present' => 'Hadir',
                                'late' => 'Terlambat',
                                'sick' => 'Sakit',
                                'excused' => 'Izin',
                                'absent' => 'Alpa',
                            ])
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Waktu & Catatan')
                    ->schema([
                        Forms\Components\TimePicker::make('check_in')
                            ->label('Scan Masuk'),
                        Forms\Components\TimePicker::make('check_out')
                            ->label('Scan Pulang'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Opsional: Alasan izin/terlambat')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('student.class.name')
                    ->label('Kelas')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in')
                    ->label('Masuk')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('check_out')
                    ->label('Pulang')
                    ->time('H:i'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'late' => 'warning',
                        'sick' => 'info',
                        'excused' => 'primary',
                        'absent' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'present' => 'Hadir',
                        'late' => 'Terlambat',
                        'sick' => 'Sakit',
                        'excused' => 'Izin',
                        'absent' => 'Alpa',
                    ]),
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
