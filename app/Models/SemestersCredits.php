<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemestersCredits extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
      'course',
      'credit', 
      'subject_id', 
      'semester'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
