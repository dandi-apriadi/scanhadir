<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $modelLabel = 'Siswa';
    protected static ?string $pluralModelLabel = 'Data Siswa';
    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->can('viewAny', Student::class);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun')
                    ->description('Data login siswa untuk portal.')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Pilih User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique('users', 'email'),
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->required(),
                                Forms\Components\Hidden::make('role')->default('student'),
                            ]),
                    ])->columns(1),

                Forms\Components\Section::make('Data Akademik')
                    ->description('Informasi sekolah siswa.')
                    ->schema([
                        Forms\Components\Select::make('class_id')
                            ->label('Kelas')
                            ->relationship('class', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('nisn')
                            ->label('NISN')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        Forms\Components\TextInput::make('qr_code')
                            ->label('QR Code')
                            ->placeholder('Otomatis jika kosong')
                            ->helperText('Gunakan kode unik untuk kartu QR.')
                            ->unique(ignoreRecord: true),
                    ])->columns(2),

                Forms\Components\Section::make('Profil')
                    ->schema([
                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Foto Siswa')
                            ->image()
                            ->directory('student-photos')
                            ->avatar()
                            ->imageEditor(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('class.name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('nisn')
                    ->label('NISN')
                    ->searchable(),
                Tables\Columns\TextColumn::make('qr_code')
                    ->label('QR Code')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('class_id')
                    ->label('Filter Kelas')
                    ->relationship('class', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('download_qr')
                    ->label('Download QR')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (Student $record): string => route('students.qrcode', ['student' => $record->id, 'download' => 1]))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('generate_qr_pdf')
                        ->label('Generate QR PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function (Collection $records) {
                            $students = $records->map(function (Student $student) {
                                $filename = "qrcodes/student-{$student->id}.svg";

                                if (!Storage::disk('local')->exists($filename)) {
                                    Storage::disk('local')->makeDirectory('qrcodes');
                                    $image = QrCode::format('svg')->size(400)->margin(1)->generate($student->qr_code);
                                    Storage::disk('local')->put($filename, $image);
                                }

                                $imageBinary = Storage::disk('local')->get($filename);

                                return [
                                    'name' => $student->user?->name ?? 'Siswa',
                                    'nisn' => $student->nisn,
                                    'class_name' => $student->class?->name ?? '-',
                                    'qr_code' => $student->qr_code,
                                    'image' => 'data:image/svg+xml;base64,' . base64_encode($imageBinary),
                                ];
                            });

                            $pdf = Pdf::loadView('pdf.student-qr-codes', ['students' => $students]);

                            return response()->streamDownload(
                                static fn () => print($pdf->output()),
                                'student-qr-codes.pdf'
                            );
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
