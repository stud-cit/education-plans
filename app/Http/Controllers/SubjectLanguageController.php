<?php

namespace App\Http\Controllers;

use App\Models\SubjectLanguage;
use App\Http\Resources\SubjectLanguageResource;
use App\Http\Requests\StoreSubjectLanguageRequest;
use App\Http\Requests\UpdateSubjectLanguageRequest;
use App\Http\Resources\SubjectLanguageListResource;

class SubjectLanguageController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SubjectLanguage::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SubjectLanguageResource::collection(
            SubjectLanguage::select(['id', 'title'])->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSubjectLanguageRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSubjectLanguageRequest $request)
    {
        $validated = $request->validated();

        SubjectLanguage::create($validated);

        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSubjectLanguageRequest  $request
     * @param  \App\Models\SubjectLanguage  $subjectLanguage
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSubjectLanguageRequest $request, SubjectLanguage $subjectLanguage)
    {
        $validated = $request->validated();

        $subjectLanguage->update($validated);

        return $this->success(__('messages.Updated'), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubjectLanguage  $subjectLanguage
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubjectLanguage $subjectLanguage)
    {
        try {
            $subjectLanguage->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        return $this->success(__('messages.Deleted'), 200);
    }

    public function getList()
    {
        return SubjectLanguageListResource::collection(
            SubjectLanguage::select(['id', 'title'])->get()
        );
    }
}
