<?php

namespace App\Models;

use App\ExternalServices\Asu\Subjects;

use App\Http\Constant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
      'laboratories'
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
        return $this->hoursModules()
            ->select(['subject_id'])
            ->selectRaw('(GROUP_CONCAT(DISTINCT semester)) as semester')
            ->where('form_control_id', Constant::FORM_CONTROL['EXAM'])
            ->groupBy('subject_id');
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
}
