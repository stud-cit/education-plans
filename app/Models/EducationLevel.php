<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EducationLevel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title'];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d.m.Y');
    }

    public function getIsTrashedAttribute()
    {
        return (bool) $this->deleted_at;
    }
}
