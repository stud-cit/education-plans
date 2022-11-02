<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ExternalServices\Asu\Worker;
use App\ExternalServices\Asu\Subjects;
use App\ExternalServices\Asu\Department;
use App\ExternalServices\Asu\Profession;
use App\Http\Resources\FacultiesResource;
use App\Http\Resources\Asu\WorkerResource;
use App\Http\Resources\Asu\SubjectResource;
use App\Http\Resources\DepartmentsResource;
use App\Http\Resources\ProfessionsResource;
use App\Http\Resources\Asu\DepartmentResource;

class AsuController extends Controller
{
    public function faculties()
    {
        $department = new Department();

        return FacultiesResource::collection($department->getFaculties());
    }

    public function departmentById(Request $request)
    {
        $department = new Department();

        $data = $department->getDepartmentsByStructuralId($request->id);

        return DepartmentsResource::collection($data);
    }

    public function getSpecializations(Request $request, int $id)
    {
        $professions = new Profession();

        return ProfessionsResource::collection($professions->getSpecializations($id));
    }

    public function getSpecialities(Request $request, $id)
    {
        $professions = new Profession();

        return ProfessionsResource::collection($professions->getSpecialties($id));
    }

    public function getEducationPrograms(Request $request, $id)
    {
        $professions = new Profession();

        return ProfessionsResource::collection($professions->getEducationPrograms($id));
    }

    public function getSubjects()
    {
        $subjects = new Subjects();
        return SubjectResource::collection($subjects->getSubjects());
    }

    public function getDepartments()
    {
        $subject = new Department();
        return DepartmentResource::collection($subject->getStructuralDepartment());
    }

    public function getWorkers()
    {
        $worker = new Worker();
        // ->sortBy('full_name')->values()
        return WorkerResource::collection($worker->getAllWorkers());
    }
}
