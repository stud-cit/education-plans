<?php


namespace App\Traits;


use App\ExternalServices\ASU;

trait  HasAsuDivisionsNameTrait
{
    public function getFacultyNameAttribute(): string
    {
        $asu = new ASU();
        return $asu->getFacultyName($this->faculty_id);
    }

    public function getShortFacultyNameAttribute(): string
    {
        $asu = new ASU();
        return $asu->getShortFacultyName($this->faculty_id);
    }

    public function getDepartmentNameAttribute(): string
    {
        $asu = new ASU();
        return $asu->getDepartmentName($this->department_id);
    }

    public function getShortDepartmentNameAttribute(): string
    {
        $asu = new ASU();
        return $asu->getShortDepartmentName($this->department_id);
    }
}
