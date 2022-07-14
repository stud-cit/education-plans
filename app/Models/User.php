<?php

namespace App\Models;

use App\ExternalServices\Asu\Department;
use App\ExternalServices\Asu\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'asu_id',
        'name',
        'faculty_id',
        'faculty_name',
        'department_id',
        'department_name',
        'email',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at'
    ];

    /**
     * @var string[]
     */
    protected $appends = ['full_name'];

    public const ADMIN = 1;
    public const TRAINING_DEPARTMENT = 2;
    public const PRACTICE_DEPARTMENT = 3;
    public const EDUCATIONAL_DEPARTMENT = 4;
    public const FACULTY_INSTITUTE = 5;
    public const DEPARTMENT = 6;
    public const ROOT = 7;

    public const ROLE_LIST = [
        self::ADMIN,
        self::TRAINING_DEPARTMENT,
        self::PRACTICE_DEPARTMENT,
        self::EDUCATIONAL_DEPARTMENT,
        self::FACULTY_INSTITUTE,
        self::DEPARTMENT,
        self::ROOT
    ];

    public const PRIVILEGED_ROLES = [
        self::ADMIN,
        self::ROOT
    ];

    public function getFacultyNameAttribute(): string
    {
        if ($this->faculty_id === null) {
            return '';
        }

        $asu = new Department();
        return $asu->getDivisionName($this->faculty_id);
    }

    public function getDepartmentNameAttribute(): string
    {
        if ($this->department_id === null) {
            return '';
        };

        $asu = new Department();
        return $asu->getDivisionName($this->department_id);
    }

    public function getFullNameAttribute(): string
    {
        $asu = new Worker();
        return $asu->getFullNameWorker($this->asu_id);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function assignRole($role)
    {
        return $this->role()->associate($role)->save();
    }

}
