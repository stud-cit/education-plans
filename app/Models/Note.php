<?php

namespace App\Models;

use App\Observers\NoteObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['abbreviation', 'explanation'];

    protected static function booted()
    {
        Note::observe(NoteObserver::class);
    }
}
