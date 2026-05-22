<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliahDosenAssignment extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah_dosen_assignments';

    protected $fillable = [
        'subject_id',
        'user_id',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}