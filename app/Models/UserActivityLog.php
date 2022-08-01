<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'asu_id', 'name', 'role', 'operation', 'ip', 'model', 'data'
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d.m.Y H:i');
    }
}
