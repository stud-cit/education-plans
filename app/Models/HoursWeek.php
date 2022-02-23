<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoursWeek extends Model
{
    use HasFactory;

    protected $fillable = ['hour'];

    public function formControl()
    {
        return $this->belongsTo(FormControl::class);
    }

    public function individualTask()
    {
        return $this->belongsTo(IndividualTask::class);
    }

    
}
