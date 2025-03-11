<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VerificationStatuses;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\VerificationSubjectStatusesResource;

class VerificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->input('type', VerificationStatuses::TYPE_PLAN);
        $cacheKey = 'verification_statuses_' . $type;

        $data = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($type) {
            return VerificationStatuses::select('id', 'title', 'role_id')->where('type', $type)->orderBy('order')->get();
        });

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

    public function getVerificationCatalogEducationProgramStatuses()
    {
        $statuses = VerificationStatuses::select('id', 'title', 'role_id')->where('type', 'education-program')->get();

        return VerificationSubjectStatusesResource::collection($statuses);
    }
}
