<?php

namespace App\Http\Controllers;

use App\ExternalServices\Op\OP;
use Illuminate\Http\Request;

class OpController extends Controller
{
    public function programs(Request $request)
    {
      $model = new OP();
      $data = $model->getPrograms($request);
      return response()->json($data);
    }

    public function programId(Request $request)
    {
      $model = new OP();
      $data = $model->getProgramId($request);
      return response()->json($data);
    }
}
