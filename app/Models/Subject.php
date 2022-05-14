<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = ['title', 'asu_id', 'cycle_id', 'selective_discipline_id', 'credits', 'hours', 'practices', 'laboratories'];

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
}
