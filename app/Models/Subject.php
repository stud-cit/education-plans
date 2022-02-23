<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'asu_id', 'credits', 'hours', 'practices', 'laboratories'];

    public function selectiveDiscipline()
    {
        return $this->belongsTo(SelectiveDisciline::class);
    }

    public function cycle()
    {
        return $this->belongsTo(Cycle::class);
    }

    public function hoursWeeks()
    {
        return $this->hasMany(HoursWeek::class);
    }
}
