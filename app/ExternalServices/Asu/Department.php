<?php

namespace App\ExternalServices\Asu;

use Illuminate\Support\Collection;

class Department extends ASU
{
    public function getFaculties(): Collection
    {
        $filtered = $this->getDepartments()->filter(function ($value) {
            return $value['unit_type'] == self::ID_FACULTY || $value['unit_type'] == self::ID_INSTITUTE;
        });

        return $filtered->values();
    }

    /**
     * @param int $id
     * @return string
     */
    public function getFacultyName(int $id): string
    {
        $faculties = $this->getFaculties();

        return $this->getName($faculties, $id);
    }

    /**
     * @param int $id
     * @return string
     */
    public function getShortFacultyName(int $id): string
    {
        $faculties = $this->getFaculties();

        return $this->getShortName($faculties, $id);
    }

    /**
     * @param int $id
     * @return string
     */
    public function getDepartmentName(int $id): string
    {
        $departments = $this->getStructuralDepartment();

        return $this->getName($departments, $id);
    }

    /**
     * @param int $id
     * @return string
     */
    public function getShortDepartmentName(int $id): string
    {
        $departments = $this->getStructuralDepartment();

        return $this->getShortName($departments, $id);
    }

    /**
     * @param $collection
     * @param $id
     * @return string
     */
    private function getName($collection, $id): string
    {
        $isExists = $collection->contains('id', $id);

        return $isExists ? $collection->firstWhere('id', $id)['name'] : self::NOT_FOUND;
    }

    /**
     * @param int $id
     * @return string
     */

    public function getDivisionName(int $id): string
    {
        $divisions = $this->getDepartments();

        return $this->getName($divisions, $id);
    }

    /**
     * @param $collection
     * @param $id
     * @return string
     */
    private function getShortName($collection, $id): string
    {
        $isExists = $collection->contains('id', $id);

        return $isExists ? $collection->firstWhere('id', $id)['short_name'] : self::NOT_FOUND;
    }

    public function getStructuralDepartment(): Collection
    {
        $filtered = $this->getDepartments()->filter(function ($value) {
            return $value['unit_type'] == self::ID_DEPARTMENT;
        });

        return $filtered->values();
    }

    public function getDepartmentsByStructuralId($structuralId): Collection
    {
        $filtered = $this->getDepartments()->filter(function ($value) use ($structuralId) {
            return $value['faculty_id'] == $structuralId && $value['unit_type'] == self::ID_DEPARTMENT;
        });

        return $filtered->sortBy('department')->values();
    }

    public function searchFacultyByDepartmentId($department_id)
    {
        $divisions = $this->getDepartments();

        foreach ($divisions as $division)  {
            if ($division['id'] == $department_id) {
                if ($division['unit_type'] == self::ID_FACULTY || $division['unit_type'] == self::ID_INSTITUTE) {
                    return $division;
                } else {
                    return $this->searchFacultyByDepartmentId($division['faculty_id']);
                }
            }
        }
    }

    public function getDepartmentInfoByUser($user): array
    {
        $departments = $this->getDepartments()->toArray();
        $departmentId = 0;
        $departmentName = '';
        $facultyName = '';
        // TODO: KOD_SYMP 1,5

        if(isset($user['info2'])){
            foreach ($user['info2'] as $k => $v) {
                if(($v['KOD_STATE'] == 1) && ($v['KOD_SYMP'] == 1 || $v['KOD_SYMP'] == 5)) {
                    $departmentId = $v['KOD_DIV'];
                }
            }
        } else {
            return response()->json(['message' => 'User is not sumdu worker']);
        }

        $divIndex = array_search($departmentId, array_column($departments, 'id'));
        $divType = $departments[$divIndex]['unit_type'];
        $facultyId = $departments[$divIndex]['faculty_id'];

        if($divType == self::ID_FACULTY || $divType == self::ID_INSTITUTE){
            $facultyId = $departmentId;
            $facultyName = $departments[$divIndex]['name'];
            $departmentId = 0;
        } else if($facultyId != 0) {
            $facultyId = $departments[$divIndex]['faculty_id'];
            $facultyIndex = array_search($facultyId, array_column($departments, 'id'));
            $facultyName = $departments[$facultyIndex]['name'];
            $departmentName = $departments[$divIndex]['name'];
        } else {
            $departmentName = $departments[$divIndex]['name'];
        }

        return [
            'department_id' => $departmentId,
            'department_name' => $departmentName,
            'faculty_id' => $facultyId,
            'faculty_name' => $facultyName
        ];
    }

    public function getDepartments(): Collection
    {
        $keys = [
            "ID_DIV" => "id",
            "ID_PAR" => "faculty_id",
            "KOD_TYPE" => "unit_type",
            "KOD_DIV" => "department_id",
            "NAME_DIV" => "name",
            "ABBR_DIV" => "short_name",
        ];

        return  $this->getAsuData($this->url('getDivisions'), [], 'departments', $keys);
    }

}
