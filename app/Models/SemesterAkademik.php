<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterAkademik extends Model
{
    use HasFactory;

    protected $table = 'semester_akademik';

    protected $fillable = [
        'nama_semester',
        'tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function jadwal()
    {
        return $this->hasMany(Schedule::class, 'semester_akademik_id');
    }

    public function mataKuliah()
    {
        return $this->hasMany(Subject::class, 'semester_akademik_id');
    }

    public function getDisplayNameAttribute(): string
    {
        return trim($this->nama_semester . ' ' . $this->tahun_ajaran);
    }

    /**
     * Derive the semester number: Ganjil => 1, Genap => 2.
     * Falls back to the first digit found in nama_semester.
     */
    public function getSemesterNumberAttribute(): int
    {
        $name = strtolower((string) $this->nama_semester);

        if (str_contains($name, 'ganjil')) {
            return 1;
        }

        if (str_contains($name, 'genap')) {
            return 2;
        }

        if (preg_match('/\d+/', $this->nama_semester, $matches)) {
            return (int) $matches[0];
        }

        return 1;
    }

    /** Ganjil / Genap label. */
    public function getSemesterTypeAttribute(): string
    {
        return $this->semester_number === 1 ? 'Ganjil' : 'Genap';
    }

    /** Short badge label, e.g. "Semester 1". */
    public function getBadgeLabelAttribute(): string
    {
        return 'Semester ' . $this->semester_number;
    }
}