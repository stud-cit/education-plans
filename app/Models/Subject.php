<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'asu_id', 'credits', 'hours', 'practices', 'laboratories'];

    public function selective_discipline()
    {
        return $this->belongsTo(SelectiveDisciline::class);
    }

    public function cycle()
    {
        return $this->belongsTo(Cycle::class);
    }

    public function hours_weeks()
    {
        return $this->hasMany(HoursWeek::class);
    }
}
