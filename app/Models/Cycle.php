<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cycle extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'cycle_id', 'credit', 'plan_id'];

    protected $casts = [
        'credit' => 'int'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'cycle_id')->with('selectiveDiscipline');
    }

    public function cycles()
    {
        return $this->hasMany(Cycle::class, 'cycle_id')->with('cycles', 'subjects.hoursWeeks.formControl', 'subjects.hoursWeeks.individualTask');
    }
}
