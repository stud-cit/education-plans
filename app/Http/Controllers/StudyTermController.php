<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudyTermRequest;
use App\Http\Requests\UpdateStudyTermRequest;
use App\Models\StudyTerm;
use Illuminate\Http\Request;
use App\Http\Resources\StudyTermResource;
use App\Http\Resources\StudyTermSelectResource;

class StudyTermController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return StudyTermResource::collection(StudyTerm::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreStudyTermRequest $request)
    {

        $validated = $request->validated();

        StudyTerm::create($validated);

        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\StudyTerm  $studyTerm
     * @return \Illuminate\Http\Response
     */
    public function show(StudyTerm $studyTerm)
    {
        return new StudyTermResource($studyTerm);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StudyTerm  $studyTerm
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStudyTermRequest $request, StudyTerm $studyTerm)
    {
        $validated = $request->validated();

        $studyTerm->update($validated);

        return $this->success(__('messages.Updated'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\StudyTerm  $studyTerm
     * @return \Illuminate\Http\Response
     */
    public function destroy(StudyTerm $studyTerm)
    {
        try {
            $studyTerm->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        return $this->success(__('messages.Deleted'), 200);
    }

    public function select() {
        return StudyTermSelectResource::collection(StudyTerm::all());
    }
}
