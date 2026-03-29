<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentClassResource\Pages;
use App\Models\StudentClass;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentClassResource extends Resource
{
    protected static ?string $model = StudentClass::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Kelas';
    protected static ?string $pluralModelLabel = 'Data Kelas';
    public static function canAccess(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isTeacher());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kelas')
                    ->description('Kelola data kelas dan jenjang pendidikan.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Kelas')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: XII RPL 1')
                            ->maxLength(255),
                        Forms\Components\Select::make('level')
                            ->label('Tingkat')
                            ->options([
                                'X' => 'X (Sepuluh)',
                                'XI' => 'XI (Sebelas)',
                                'XII' => 'XII (Dua Belas)',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('major')
                            ->label('Jurusan')
                            ->required()
                            ->placeholder('Contoh: Rekayasa Perangkat Lunak')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->label('Tingkat')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'X' => 'gray',
                        'XI' => 'warning',
                        'XII' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('major')
                    ->label('Jurusan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('students_count')
                    ->label('Jumlah Siswa')
                    ->counts('students')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('level')
                    ->options([
                        'X' => 'Tingkat X',
                        'XI' => 'Tingkat XI',
                        'XII' => 'Tingkat XII',
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
            'index' => Pages\ManageStudentClasses::route('/'),
        ];
    }
}
