<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageSubject extends Model
{
    use HasFactory;

    public function language()
    {
        return $this->belongsTo(SubjectLanguage::class, 'language_id');
    }
}
