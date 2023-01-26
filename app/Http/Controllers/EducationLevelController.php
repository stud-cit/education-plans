<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Requests\IndexEducationLevelRequest;
use Illuminate\Http\Request;
use App\Models\EducationLevel;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\EducationLevelResource;
use App\Http\Requests\StoreEducationLevelRequest;

class EducationLevelController extends Controller
{
    public function list()
    {
        return EducationLevelResource::collection(
            EducationLevel::withTrashed()->select('id', 'title', 'deleted_at')->orderBy('deleted_at')->get()
        );
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexEducationLevelRequest $request)
    {
        $validated = $request->validated();

        $perPage = Helpers::getPerPage('items_per_page', $validated);

        return EducationLevelResource::collection(
            EducationLevel::withTrashed()->select('id', 'title', 'deleted_at')->paginate($perPage)
        );
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
        return response()->json(['message' => __('Updated')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  EducationLevel  $educationLevel
     * @return JsonResponse
     */
    public function destroy(EducationLevel $educationLevel): JsonResponse
    {
        try {
            $educationLevel->delete();

            return $this->success(__('messages.Zipped'), 201);
        } catch (\Illuminate\Database\QueryException $exception) {
            return $this->error($exception->getMessage(), $exception->getCode());
        }
    }

    public function restore(Request $request)
    {
        EducationLevel::withTrashed()->where('id', $request->id)->restore();

        return $this->success(__('messages.Unzipped'), 201);
    }
}
