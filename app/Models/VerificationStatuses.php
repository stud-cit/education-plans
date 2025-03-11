<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VerificationStatuses extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = ['title'];

    public const OP = 1;
    public const NOT_CHECKED = 1;
    public const VERIFIED = 2;
    public const REJECTED = 3;

    public const TYPE_PROJECT = 'project';
    public const TYPE_PLAN = 'plan';
    public const TYPE_SUBJECT = 'subject';
    public const TYPE_SPECIALITY = 'speciality';
    public const TYPE_EDUCATION_PROGRAM = 'education-program';

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

    public static function fullPlanVerification(int $type)
    {
        switch ($type) {
            case Plan::PLAN:
                return VerificationStatuses::select('id')->where('type', self::TYPE_PLAN)->count();
            case Plan::PROJECT:
                return VerificationStatuses::select('id')->where('type', self::TYPE_PROJECT)->count();
            default:
                return VerificationStatuses::select('id')->where('type', self::TYPE_PLAN)->count();
        }
    }
}
