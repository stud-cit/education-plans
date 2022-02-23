<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoursWeek extends Model
{
    use HasFactory;

    protected $fillable = ['hour'];

    public function forms_control()
    {
        return $this->belongsTo(FormsControl::class);
    }

    public function individual_task()
    {
        return $this->belongsTo(IndividualTask::class);
    }

    
}
