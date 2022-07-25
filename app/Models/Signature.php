<?php

namespace App\Models;

use App\ExternalServices\Asu\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;

    protected $fillable = ['plan_id', 'position_id', 'manual_position', 'asu_id'];


    public function getNameAttribute(): string
    {
        $worker = new Worker();

        return $worker->getWorkerAttribute($this->asu_id, 'first_name');
    }

    public function getSurnameAttribute(): string
    {
        $worker = new Worker();

        return $worker->getWorkerAttribute($this->asu_id, 'last_name');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
