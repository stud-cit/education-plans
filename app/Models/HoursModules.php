<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoursModules extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
      'course',
      'hour', 
      'subject_id', 
      'course', 
      'form_control_id',
      'individual_task_id',
      'module',
      'semester'
    ];

    public function formControl()
    {
        return $this->belongsTo(FormControl::class);
    }

    public function individualTask()
    {
        return $this->belongsTo(IndividualTask::class);
    }

    
}
