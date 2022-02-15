<?php

namespace App\Models;

use App\ExternalServices\ASU;
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
        return Carbon::parse($value)->format('d.m.Y H:m');
    }

    public function getFacultyAttribute()
    {
        $asu = new ASU();
        return $asu->getNameFacultyById($this->faculty_id);
    }

    public function getDepartmentAttribute()
    {
        $asu = new ASU();
        return $asu->getNameDepartmentById($this->department_id);
    }

    public function formStudy()
    {
        return $this->belongsTo(FormStudy::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }
}
