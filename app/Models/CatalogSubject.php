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
    const SPECIALITY = 2;
    const EDUCATION_PROGRAM = 3;

    protected $fillable = [
        'year',
        'education_program_id',
        'speciality_id',
        'faculty_id',
        'department_id',
        'selective_discipline_id',
        'catalog_education_level_id',
        'group_id',
        'user_id',
    ];

    // TODO: MOVE TO TRAIT?
    public function getSpecialityIdNameAttribute()
    {
        if (!$this->speciality_id) return null;

        $professions = new Profession();

        $code = $professions->getTitle($this->speciality_id, 'code');
        return "{$code} {$professions->getTitle($this->speciality_id, 'title')}";
    }

    public function educationLevel()
    {
        return $this->belongsTo(CatalogEducationLevel::class, 'catalog_education_level_id');
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
