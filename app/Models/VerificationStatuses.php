<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationStatuses extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['title'];

    public const OP = 1;
    public const NOT_CHECKED = 1;
    public const VERIFIED = 2;
    public const REJECTED = 3;

    public function getDivisionStatuses()
    {
        return [
            ['id' => self::NOT_CHECKED, 'title' => __('variables.NotChecked')],
            ['id' => self::VERIFIED, 'title' => __('variables.Verified')],
            ['id' => self::REJECTED, 'title' => __('variables.Rejected')],
        ];
    }

    public static function fullSubjectVerification()
    {
        return VerificationStatuses::select('id', 'title')->where('type', 'subject')->count();
    }

    public static function fullCatalogSpecialityVerification()
    {
        return VerificationStatuses::select('id', 'title')->where('type', 'speciality')->count();
    }

    public static function fullCatalogEducationProgramVerification()
    {
        return VerificationStatuses::select('id', 'title')->where('type', 'education-program')->count();
    }

    public static function fullPlanVerification()
    {
        return VerificationStatuses::select('id')
            ->where('type', 'plan')
            ->count() - 1;
    }
}
