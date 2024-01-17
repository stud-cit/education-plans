<?php

namespace App\Models;

use App\Helpers\Filters\FilterBuilder;
use App\Observers\UserObserver;
use Laravel\Sanctum\HasApiTokens;
use App\ExternalServices\Asu\Worker;
use App\ExternalServices\Asu\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'faculty_id' => 'int',
        'department_id' => 'int'
    ];

    protected $attributes = [
        'role_id' => self::GUEST,
    ];

    public const ADMIN = 1;
    public const TRAINING_DEPARTMENT = 2;
    public const PRACTICE_DEPARTMENT = 3;
    public const EDUCATIONAL_DEPARTMENT_DEPUTY = 4;
    public const EDUCATIONAL_DEPARTMENT_CHIEF = 5;
    public const FACULTY_INSTITUTE = 6;
    public const DEPARTMENT = 7;
    public const ROOT = 8;
    public const GUEST = 9;

    public const ALL_ROLES = [
        self::ADMIN,
        self::TRAINING_DEPARTMENT,
        self::PRACTICE_DEPARTMENT,
        self::EDUCATIONAL_DEPARTMENT_DEPUTY,
        self::EDUCATIONAL_DEPARTMENT_CHIEF,
        self::FACULTY_INSTITUTE,
        self::DEPARTMENT,
        self::ROOT,
        self::GUEST,
    ];

    /**
     * Includes roles:
     * TRAINING_DEPARTMENT, PRACTICE_DEPARTMENT,
     * EDUCATIONAL_DEPARTMENT_DEPUTY, EDUCATIONAL_DEPARTMENT_CHIEF
     */
    public const REPRESENTATIVE_DEPARTMENT_ROLES = [
        self::TRAINING_DEPARTMENT,
        self::PRACTICE_DEPARTMENT,
        self::EDUCATIONAL_DEPARTMENT_DEPUTY,
        self::EDUCATIONAL_DEPARTMENT_CHIEF
    ];

    /**
     * Includes roles:
     * ADMIN, ROOT
     */
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
        if (gettype($roles) === 'array') {
            return in_array($this->role_id, $roles);
        } else if (gettype($roles) === 'integer') {
            return  $this->role_id === $roles;
        }

        return false;
    }

    public static function except($roleId, $role): bool
    {
        $roles = array_filter(self::ALL_ROLES, fn ($r) => $r != $role);

        return in_array($roleId, $roles);
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
        return $this->name;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function assignRole($role)
    {
        return $this->role()->associate($role)->save();
    }

    public function isFacultyMine(?int $faculty_id): bool
    {
        return $this->faculty_id === $faculty_id;
    }

    public function isDepartmentMine(?int $department_id): bool
    {
        return $this->department_id === $department_id;
    }

    public function isPlanMine(?int $plan_id): bool
    {
        return $this->id === $plan_id ? true : false;
    }

    public function isOwner($user_id): bool
    {
        return $this->id === $user_id;
    }

    protected static function booted()
    {
        User::observe(UserObserver::class);
    }

    public function scopeFilterBy($query, $filters)
    {
        $namespace = 'App\Helpers\Filters\Users';
        $filter = new FilterBuilder($query, $filters, $namespace);

        return $filter->apply();
    }
}
