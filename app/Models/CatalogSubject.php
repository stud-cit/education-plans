<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'education_program_id',
        'specialization_id',
        'faculty_id',
        'department_id',
        'selective_discipline_id',
        'group_id',
        'user_id',
    ];

    public function group()
    {
        return $this->belongsTo(CatalogGroup::class, 'group_id', 'id');
    }

    public function subjects()
    {
        return $this->hasMany(CatalogSelectiveSubject::class);
    }

    protected static function booted()
    {
        static::saving(function ($catalog) {
            $catalog->user_id = 1; // Auth::id();
        });
    }
}
