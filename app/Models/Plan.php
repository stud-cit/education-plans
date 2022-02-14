<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'faculty_id',
        'department_id',
        'year'
    ];

    protected $casts = [
        // 'created_at' => 'datetime:Y-m-d',
        'year' => 'int'
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d.m.Y');
    }

    public function getFacultyAttribute()
    {
        $asu = new \App\ExternalServices\ASU();
        return $asu->getNameFacultyById($this->faculty_id);
    }

    public function getDepartmentAttribute()
    {
        $asu = new \App\ExternalServices\ASU();
        return $asu->getNameDepartmentById($this->department_id);
    }
}
