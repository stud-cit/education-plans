<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoursModules extends Model
{
    use HasFactory;

    protected $fillable = ['hour', 'subject_id'];

    public function formControl()
    {
        return $this->belongsTo(FormControl::class);
    }

    public function individualTask()
    {
        return $this->belongsTo(IndividualTask::class);
    }

    
}
