<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShortenedPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'plan_id',
        'parent_id',
        'shortened_by_year',
        'year'
    ];
}
