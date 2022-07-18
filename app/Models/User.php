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
    protected $casts = [
        'department_id' => 'integer',
        'faculty_id' => 'integer',
    ];
    /**
     * @var string[]
     */
    protected $appends = ['full_name'];

    public const ADMIN = 1;
    public const TRAINING_DEPARTMENT = 2;
    public const PRACTICE_DEPARTMENT = 3;
    public const EDUCATIONAL_DEPARTMENT_DEPUTY = 4;
    public const EDUCATIONAL_DEPARTMENT_CHIEF = 5;
    public const FACULTY_INSTITUTE = 6;
    public const DEPARTMENT = 7;
    public const ROOT = 8;

    public const ALL_ROLES = [
        self::ADMIN,
        self::TRAINING_DEPARTMENT,
        self::PRACTICE_DEPARTMENT,
        self::EDUCATIONAL_DEPARTMENT_DEPUTY,
        self::EDUCATIONAL_DEPARTMENT_CHIEF,
        self::FACULTY_INSTITUTE,
        self::DEPARTMENT,
        self::ROOT
    ];

    public const DEPARTMENTS_ROLES = [
        self::TRAINING_DEPARTMENT,
        self::PRACTICE_DEPARTMENT,
        self::EDUCATIONAL_DEPARTMENT_DEPUTY,
        self::EDUCATIONAL_DEPARTMENT_CHIEF
    ];

    public const PRIVILEGED_ROLES = [
        self::ADMIN,
        self::ROOT
    ];

    /**
     * possibility current user something do
     *
     * @param array|integer $roles
     * @return boolean
     */
    public function possibility($roles = self::ALL_ROLES): bool
    {
        if (gettype ($roles) === 'array') {
            return in_array($this->role_id, $roles);
        } else if (gettype ($roles) === 'integer') {
            return  $this->role_id === $roles;
        }

        return false;
    }

    public function getFacultyNameAttribute()
    {
        if (!$this->faculty_id) return null;

        $asu = new Department();
        return $asu->getDivisionName($this->faculty_id);
    }

    public function getDepartmentNameAttribute()
    {
        if (!$this->department_id) return null;

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
