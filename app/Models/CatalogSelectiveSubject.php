<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogSelectiveSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'education_program_id',
        'specialization_id',
        'faculty_id',
        'department_id',
        'selective_discipline_id',
        'user_id',
    ];

    public function educationLevel()
    {
        return $this->belongsTo(CatalogEducationLevel::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }
}
