<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormStudyRequest;
use App\Models\FormStudy;
use Illuminate\Http\Request;
use App\Http\Resources\FormStudyResource;

class FormStudyController extends Controller
{
    public function index()
    {
        return FormStudyResource::collection(FormStudy::select('title')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreFormStudyRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreFormStudyRequest $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        FormStudy::create($validated);
        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FormStudy  $formStudy
     * @return FormStudyResource
     */
    public function show(FormStudy $formStudy): FormStudyResource
    {
        return new FormStudyResource($formStudy);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  StoreFormStudyRequest $request
     * @param  \App\Models\FormStudy  $formStudy
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreFormStudyRequest $request, FormStudy $formStudy): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validated();
        $formStudy->update($validated);
        return $this->success(__('messages.Updated'), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FormStudy  $formStudy
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(FormStudy $formStudy)
    {
        try {
            $formStudy->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->error(__('messages.Cannot_delete'), 403);
        }
        return $this->success(__('messages.Deleted'), 204);

    }
}
