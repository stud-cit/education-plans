<?php

namespace App\Http\Controllers;

use App\ExternalServices\Asu\Department;
use App\ExternalServices\Asu\Profession;
use App\ExternalServices\Asu\Subject;
use App\Http\Resources\DepartmentsResource;
use Illuminate\Http\Request;

class AsuController extends Controller
{
    private $asu;

    public function __construct() {
        $this->asu  = new Department();
    }

    public function faculties()
    {
        return response()->json(['data' => $this->asu->getFaculties()]);
    }

    public function departmentById(Request $request)
    {
        $data = $this->asu->getDepartmentsByStructuralId($request->id);

        return DepartmentsResource::collection($data);
    }

    public function getSpecialization(Request $request, $id)
    {
        $professions = new Profession();

        return response()->json(['data' => $professions->getSpecialization($id)]);
    }
    public function getSubjects()
    {
        $subjects = new Subject();
        return response()->json(['data' => $subjects->getSubjects()]);
    }
}
