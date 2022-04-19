<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyTerm extends Model
{
    use HasFactory;

    protected $table = 'study_terms';
    
    protected $fillable = [
        'title',
        'year',
        'month',
        'course',
        'module',
        'number_semesters'
    ];

    public function getDescriptionAttribute(): string
    {
        $singularOrPluralYearWord = $this->year == 1 ? 'рік' : 'роки'; 
        $singularOrPluralMonthWord = $this->month == 1 ? 'місяць' : 'місяців';

        return
            "{$this->year} {$singularOrPluralYearWord} {$this->month} {$singularOrPluralMonthWord} ({$this->title})";
    }
}
