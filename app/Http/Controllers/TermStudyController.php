<?php

namespace App\Http\Controllers;

use App\Models\TermStudy;
use App\Http\Resources\TermStudyResource;
use App\Http\Requests\StoreTermStudyRequest;
use App\Http\Requests\UpdateTermStudyRequest;
use App\Http\Resources\TermStudySelectResource;

class TermStudyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TermStudyResource::collection(TermStudy::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTermStudyRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTermStudyRequest $request)
    {
        $validated = $request->validated();
        TermStudy::create($validated);
        return $this->success(__('messages.Created'), 201);
    }   

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TermStudy  $termStudy
     * @return \Illuminate\Http\Response
     */
    public function show(TermStudy $termStudy)
    {
        return new TermStudyResource($termStudy);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTermStudyRequest  $request
     * @param  \App\Models\TermStudy  $termStudy
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTermStudyRequest $request, TermStudy $termStudy)
    {
        $validated = $request->validated();
        $termStudy->update($validated);
        return $this->success(__('messages.Updated'), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TermStudy  $termStudy
     * @return \Illuminate\Http\Response
     */
    public function destroy(TermStudy $termStudy)
    {
        try {
            $termStudy->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->error(__('messages.Cannot_delete'), 403);
        }
        return $this->success(__('messages.Deleted'), 200);
        
    }

    /**
     * list for dropdown 
     *
     * @return \Illuminate\Http\Response
     */
    public function select()
    {
        return TermStudySelectResource::collection(TermStudy::select('id', 'title', 'year', 'month')->get());
    }
}
