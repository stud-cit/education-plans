<?php

namespace App\Models;

use App\Observers\VerificationObserver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanVerification extends Model
{
    use HasFactory;

    protected $fillable = ['plan_id', 'user_id', 'verification_statuses_id', 'status', 'comment'];

    protected $hidden = ['created_at', 'updated_at'];

    const FULL_VERIFICATION = 4;
    const PROJECT_VERIFICATION = 1;

    public function role()
    {
        return $this->hasOne(VerificationStatuses::class, 'id', 'verification_statuses_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    protected static function booted()
    {
        PlanVerification::observe(VerificationObserver::class);
    }
}
