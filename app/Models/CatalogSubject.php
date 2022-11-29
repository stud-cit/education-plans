<?php

namespace App\Models;

use App\Helpers\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Model;
use App\ExternalServices\Asu\Profession;
use App\Traits\HasAsuDivisionsNameTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogSubject extends Model
{
    use HasFactory, HasAsuDivisionsNameTrait;

    const SUBJECT = 1;
    const SPECIALIZATION = 2;
    const EDUCATION_PROGRAM = 3;

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

    // TODO: MOVE TO TRAIT?
    public function getSpecializationIdNameAttribute()
    {
        if (!$this->specialization_id) return null;

        $professions = new Profession();
        return $professions->getTitle($this->specialization_id, 'title');
    }

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
            $catalog->user_id = 1; // TODO: Auth::id();
        });
    }

    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Helpers\Filters\CatalogSubjectFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }
}
