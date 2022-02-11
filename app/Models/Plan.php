<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{

    use HasFactory;
    protected $asu;

    function __construct()
    {
        parent::__construct();
        $this->asu = new \App\ExternalServices\ASU();
    }
    

    protected $casts = [
        // 'created_at' => 'datetime:Y-m-d',
        'year' => 'int'
    ];

   protected $dates = [
       'created_at',
        'updated_at',
   ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d.m.Y');
    }

    public function getFacultyAttribute()
    {
        return $this->asu->getNameFacultyById($this->faculty_id);
    }

    public function getDepartmentAttribute()
    {
        return $this->asu->getNameDepartmentById($this->department_id);
    }
}
