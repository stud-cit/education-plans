<?php

namespace App\Models;

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
}
