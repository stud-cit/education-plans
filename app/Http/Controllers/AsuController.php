<?php

namespace App\Http\Controllers;

use App\ExternalServices\Asu\Department;
use App\ExternalServices\Asu\Profession;
use App\ExternalServices\Asu\Subjects;
use App\Http\Resources\DepartmentsResource;
use App\Http\Resources\ProfessionsResource;
use Illuminate\Http\Request;

class AsuController extends Controller
{
    public function faculties()
    {
        $department = new Department();

        return response()->json(['data' => $department->getFaculties()]);
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
//        return response()->json(['data' => $professions->getEducationPrograms($id)]);
    }

    public function getSubjects()
    {
        $subjects = new Subjects();
        return response()->json(['data' => $subjects->getSubjects()]);
    }
}
