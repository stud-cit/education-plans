<?php

namespace App\Models;

use App\Observers\SettingObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'title', 'value'];

    protected $casts = [
        'value' => 'float',
    ];

    protected static function booted()
    {
        Setting::observe(SettingObserver::class);
    }
}
