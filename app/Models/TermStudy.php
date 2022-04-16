<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermStudy extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'year', 'month', 'course', 'module'];

    public function getDescriptionAttribute(): string
    {
        $singularOrPluralYearWord = $this->year == 1 ? 'рік' : 'роки'; 
        $singularOrPluralMonthWord = $this->month == 1 ? 'місяць' : 'місяців';

        return
            "{$this->year} {$singularOrPluralYearWord} {$this->month} {$singularOrPluralMonthWord} ({$this->title})";
    }
}
