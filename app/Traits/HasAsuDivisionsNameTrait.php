<?php

namespace App\Traits;

use App\ExternalServices\Asu\Department;

trait  HasAsuDivisionsNameTrait
{
    public function getFacultyNameAttribute(): string
    {
        $asu = new Department();
        return $asu->getFacultyName($this->faculty_id);
    }

    public function getShortFacultyNameAttribute(): string
    {
        $asu = new Department();
        return $asu->getShortFacultyName($this->faculty_id);
    }

    public function getDepartmentNameAttribute(): string
    {
        $asu = new Department();
        return $asu->getDepartmentName($this->department_id);
    }

    public function getShortDepartmentNameAttribute(): string
    {
        $asu = new Department();
        return $asu->getShortDepartmentName($this->department_id);
    }
}
