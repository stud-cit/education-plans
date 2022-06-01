<?php

namespace App\Http\Controllers;

use App\ExternalServices\Asu\Department;
use App\ExternalServices\Asu\Profession;
use App\ExternalServices\Asu\Subjects;
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

    public function getSpecializations(Request $request, $id)
    {
        $professions = new Profession();

        return response()->json(['data' => $professions->getSpecializations($id)]);
    }
    public function getSubjects()
    {
        $subjects = new Subjects();
        return response()->json(['data' => $subjects->getSubjects()]);
    }
}
