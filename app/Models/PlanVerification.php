<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanVerification extends Model
{
    use HasFactory;

    protected $fillable = ['plan_id', 'user_id', 'verification_statuses_id', 'status', 'comment'];

    protected $hidden = ['created_at', 'updated_at'];

    public function role()
    {
        return $this->hasOne(VerificationStatuses::class, 'id', 'verification_statuses_id');
    }
}
