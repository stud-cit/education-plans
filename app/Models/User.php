<?php

namespace App\Models;

use App\ExternalServices\Asu\Department;
use App\ExternalServices\Asu\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    // /**
    //  * The attributes that should be cast.
    //  *
    //  * @var array<string, string>
    //  */
    // protected $casts = [
    //     'email_verified_at' => 'datetime',
    // ];

    public function getFacultyNameAttribute(): string
    {
        $asu = new Department();
        return $asu->getDivisionName($this->faculty_id);
    }

    public function getDepartmentNameAttribute(): string
    {
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
