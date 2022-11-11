<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VerificationStatuses;

class VerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = VerificationStatuses::where('type', 'plan')->get();
        return response()->json($data, 200);
    }

    public function getVerificationSubjectStatuses()
    {
        $statuses = VerificationStatuses::select('id', 'title')->where('type', 'subject')->get();

        return response()->json(['data' => $statuses], 200);
    }
}
