<?php

namespace App\Models;

use App\ExternalServices\Asu\Subjects;

use App\Http\Constant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
      'asu_id',
      'cycle_id',
      'selective_discipline_id',
      'credits',
      'hours',
      'practices',
      'laboratories',
      'verification',
      'faculty_id',
      'department_id'
    ];

    protected $appends = ['title'];

    protected $casts = [
      'asu_id' => 'string'
    ];

    public function selectiveDiscipline()
    {
        return $this->belongsTo(SelectiveDiscipline::class);
    }

    public function cycle()
    {
        return $this->belongsTo(Cycle::class);
    }

    public function hoursModules()
    {
        return $this->hasMany(HoursModules::class);
    }

    public function exams()
    {
        return $this->getSemestersOnFormControl([Constant::FORM_CONTROL['EXAM']]);
    }

    public function test()
    {
        return $this->getSemestersOnFormControl([
            Constant::FORM_CONTROL['TEST'],
            Constant::FORM_CONTROL['DIFFERENTIAL_TEST']
        ]);
    }

    public function individualTasks()
    {
        return $this->getSemestersOnFormControl([Constant::FORM_CONTROL['PROTECTION']])
                ->orWhereIn('individual_task_id', [
                        Constant::INDIVIDUAL_TASKS['CONTROL_WORK'],
                        Constant::INDIVIDUAL_TASKS['COURSE_WORK']
                ]);
    }


    public function semestersCredits()
    {
        return $this->hasMany(SemestersCredits::class);
    }

    public function getTitleAttribute(): string
    {
        $subjects = new Subjects();
        return $subjects->getTitle($this->asu_id);
    }

    private function getSemestersOnFormControl(array $form_control_ids): HasMany
    {
        return $this->hoursModules()
            ->select(['subject_id'])
            ->selectRaw('(GROUP_CONCAT(DISTINCT semester)) as semester')
            ->whereIn('form_control_id', $form_control_ids)
            ->groupBy('subject_id');
    }
}
