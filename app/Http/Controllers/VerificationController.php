<?php

namespace App\Http\Controllers;

use App\Models\VerificationStatuses;
use App\Http\Resources\VerificationSubjectStatusesResource;

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
        $statuses = VerificationStatuses::select('id', 'title', 'role_id')->where('type', 'subject')->get();

        return VerificationSubjectStatusesResource::collection($statuses);
    }

    public function getVerificationCatalogSpecialityStatuses()
    {
        $statuses = VerificationStatuses::select('id', 'title', 'role_id')->where('type', 'speciality')->get();

        return VerificationSubjectStatusesResource::collection($statuses);
    }
}
