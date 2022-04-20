<?php

namespace App\Http\Controllers;

use App\ExternalServices\ASU;
use Illuminate\Http\Request;

class AsuController extends Controller
{
    private $asu;

    function __construct() {
        $this->asu  = new ASU;
    }

    public function faculty()
    {
        return response()->json(['data' => $this->asu->getFaculty()]);
    }

    public function departmentById(Request $request)
    {
        $data = $this->asu->getDepartmentsByStructuralId($request->id);
        return response()->json(['data' => $data]);
    }
}
