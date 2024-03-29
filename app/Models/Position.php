<?php

namespace App\Models;

use App\Observers\PositionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = ['position', 'agreed'];

    protected $casts = [
        'agreed' => 'boolean'
    ];

    protected static function booted()
    {
        Position::observe(PositionObserver::class);
    }
}
