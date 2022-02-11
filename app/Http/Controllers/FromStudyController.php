<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormStudyRequest;
use App\Models\FormStudy;
use Illuminate\Http\Request;
use App\Http\Resources\FromStudyResource;

class FromStudyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return FromStudyResource::collection(FormStudy::select('title')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreFormStudyRequest $request)
    {
        $validated = $request->validated();
        FormStudy::create($validated);
        return response()->json(['message' => __('Created')], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FormStudy  $formStudy
     * @return \Illuminate\Http\Response
     */
    public function show(FormStudy $formStudy)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FormStudy  $formStudy
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FormStudy $formStudy)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FormStudy  $formStudy
     * @return \Illuminate\Http\Response
     */
    public function destroy(FormStudy $formStudy)
    {
        //
    }
}
