<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'asu_id',
        'catalog_selective_id',
        'type',
    ];

    const LECTOR = 'lector';
    const PRACTICE = 'practice';
}
