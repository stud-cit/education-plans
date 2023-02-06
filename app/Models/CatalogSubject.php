<?php

namespace App\Models;

use App\Traits\Catalog;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Filters\FilterBuilder;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasAsuDivisionsNameTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CatalogSubject extends Model
{
    use HasFactory, HasAsuDivisionsNameTrait, Catalog;

    const SUBJECT = 1;

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

    public function group()
    {
        return $this->belongsTo(CatalogGroup::class, 'group_id', 'id')->withTrashed();
    }

    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Helpers\Filters\CatalogSubjectFilters';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }

    protected static function booted()
    {
        static::saving(function ($catalog) {
            $catalog->user_id = Auth::id();
        });
        static::addGlobalScope('selective_discipline', function (Builder $builder) {
            $builder->where('selective_discipline_id', self::SUBJECT);
        });
    }
}
