<?php

namespace App\Http\Controllers;

use App\ExternalServices\Asu\Department;
use App\Http\Resources\DepartmentsResource;
use Illuminate\Http\Request;

class AsuController extends Controller
{
    private $asu;

    function __construct() {
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
}
