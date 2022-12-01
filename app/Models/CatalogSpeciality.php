<?php

namespace App\Models;

use App\Traits\Catalog;
use Illuminate\Database\Eloquent\Model;
use App\ExternalServices\Asu\Profession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class CatalogSpeciality extends Model
{
    use HasFactory, Catalog;

    const SPECIALITY = 2;

    protected $table = 'catalog_subjects';

    protected $fillable = [
        'selective_discipline_id',
        'catalog_education_level_id',
        'user_id',
        'speciality_id',
        'faculty_id',
        'department_id',
        'year',
        'need_verification',
    ];

    public function getSpecialityIdNameAttribute()
    {
        if (!$this->speciality_id) return null;

        $professions = new Profession();

        $code = $professions->getTitle($this->speciality_id, 'code');
        return "{$code} {$professions->getTitle($this->speciality_id, 'title')}";
    }

    public function getSpecialityCatalogNameAttribute()
    {
        $nextYear = $this->year + 1;
        return "Каталог {$this->year}-{$nextYear}р. за спеціальністю {$this->getSpecialityIdNameAttribute()}";
    }

    protected static function booted()
    {
        static::saving(function ($catalog) {
            $catalog->user_id = 1; // TODO: Auth::id();
        });

        static::addGlobalScope('selective_discipline', function (Builder $builder) {
            $builder->where('selective_discipline_id', self::SPECIALITY);
        });
    }
}
