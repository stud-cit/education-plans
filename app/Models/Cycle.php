<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cycle extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'cycle_id', 'credit', 'template'];

    protected $casts = [
        'credit' => 'int'
    ];

    protected $hidden = ['created_at', 'updated_at', 'plan_id'];
}
