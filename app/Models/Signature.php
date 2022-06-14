<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;

    protected $fillable = ['plan_id', 'position_id', 'asu_id'];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
