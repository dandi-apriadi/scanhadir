<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HolidayResource\Pages;
use App\Models\Holiday;
use App\Rules\DateRangeValid;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Libur';
    protected static ?string $pluralModelLabel = 'Daftar Libur';

    public static function canAccess(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isTeacher());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Libur')
                    ->description('Kelola tanggal libur sekolah dan hari raya.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Libur')
                            ->required()
                            ->placeholder('Contoh: Lebaran, Cuti Semester')
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Tipe Libur')
                            ->options([
                                'national' => 'Hari Raya Nasional',
                                'school' => 'Libur Sekolah',
                                'exam_break' => 'Jeda Ujian',
                            ])
                            ->required()
                            ->default('school'),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Berakhir')
                            ->required()
                            ->rule(new DateRangeValid('start_date'))
                            ->minDate(fn (Forms\Get $get) => $get('start_date')),
                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan')
                            ->placeholder('Deskripsi opsional tentang libur ini')
                            ->maxLength(500),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Libur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'national' => 'danger',
                        'school' => 'info',
                        'exam_break' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'national' => 'Hari Raya',
                        'school' => 'Libur Sekolah',
                        'exam_break' => 'Jeda Ujian',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Tanggal Berakhir')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Filter Tipe')
                    ->options([
                        'national' => 'Hari Raya Nasional',
                        'school' => 'Libur Sekolah',
                        'exam_break' => 'Jeda Ujian',
                    ]),
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
            'index' => Pages\ManageHolidays::route('/'),
        ];
    }
}
