<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEducationLevelRequest;
use App\Models\EducationLevel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\EducationLevelResource;

class EducationLevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return EducationLevelResource::collection(EducationLevel::select('title')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreEducationLevelRequest  $request
     * @return JsonResponse
     */
    public function store(StoreEducationLevelRequest $request): JsonResponse
    {
        $validated = $request->validated();

        EducationLevel::create($validated);
        return response()->json(['message' => __('Created')], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  EducationLevel  $educationLevel
     * @return EducationLevelResource
     */
    public function show(EducationLevel $educationLevel): EducationLevelResource
    {
        return new EducationLevelResource($educationLevel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  EducationLevel $educationLevel
     * @return JsonResponse
     */
    public function update(Request $request, EducationLevel $educationLevel): JsonResponse
    {
        $educationLevel->title = $request->title;
        $educationLevel->save();
        return response()->json(['message' => __('Updated')], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EducationLevel  $educationLevel
     * @return JsonResponse
     */
    public function destroy(EducationLevel $educationLevel): JsonResponse
    {
        $educationLevel->delete();
        return response()->json(['message' => __('Deleted')], 204);
    }
}
