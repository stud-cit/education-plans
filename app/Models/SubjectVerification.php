<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectVerification extends Model
{
    use HasFactory;

    protected $table = 'subjects_verifications';

    protected $fillable = [
        'user_id',
        'verification_status_id',
        'subject_id',
        'status',
        'comment'
    ];

    public function role()
    {
        return $this->hasOne(VerificationStatuses::class, 'id', 'verification_status_id');
    }
}
