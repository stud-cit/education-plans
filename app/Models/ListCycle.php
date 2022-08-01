<?php

namespace App\Models;

use App\Observers\ListCycleObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListCycle extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'general'];

    protected $casts = [
        'general' => 'boolean'
    ];

    protected $attributes = [
        'general' => false,
    ];

    protected static function booted()
    {
        ListCycle::observe(ListCycleObserver::class);
    }
}
