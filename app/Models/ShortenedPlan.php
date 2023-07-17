<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShortenedPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'parent_id',
        'shortened_by_year'
    ];
}
