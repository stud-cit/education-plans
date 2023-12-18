<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cycle extends Model
{
    use HasFactory;

    protected $fillable = ['cycle_id', 'list_cycle_id', 'credit', 'plan_id', 'has_discipline'];

    protected $casts = [
        'credit' => 'int',
        'has_discipline' => 'boolean'
    ];

    protected $appends = ['title'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $touches = ['listCycle'];

    const PRACTICAL_TRAINING = 9;
    const ATTESTATION = 10;

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'cycle_id')->with('selectiveDiscipline')->whereNull('subject_id');
    }

    public function cycles()
    {
        return $this->hasMany(Cycle::class, 'cycle_id')->with([
            'cycles',
            'subjects.semestersCredits',
            'subjects.hoursModules.formControl',
            'subjects.exams',
            'subjects.test',
            'subjects.individualTasks',
            'subjects.hoursModules.individualTask'
        ]);
    }

    public function listCycle()
    {
        return $this->belongsTo(ListCycle::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function getTitleAttribute()
    {
        return $this->listCycle->title;
    }
}
