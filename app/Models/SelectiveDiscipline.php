<?php

namespace App\Models;

use App\Observers\SelectiveDisciplineObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectiveDiscipline extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    protected static function booted()
    {
        SelectiveDiscipline::observe(SelectiveDisciplineObserver::class);
    }
}
