<?php

namespace App\Http\Controllers;

use App\Models\Cycle;
use App\Http\Resources\CycleResource;
use App\Http\Requests\StoreCycleRequest;
use App\Http\Requests\UpdateCycleRequest;
use App\Http\Requests\StoreSubCycleRequest;
use App\Http\Requests\UpdateSubCycleRequest;

class CycleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        // todo: try catch NotFoundHttpException
        return CycleResource::collection(Cycle::whereNull('cycle_id')->get());
    }

    /**
     * Display a sub listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function subIndex(Cycle $cycle)
    {
         // todo: try catch NotFoundHttpException
        return CycleResource::collection(Cycle::where('cycle_id', $cycle->id)->get());
    }

    /**
     * Store a sub newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCycleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function subStore(StoreSubCycleRequest $request)
    {
        $validated = $request->validated();
        array_push($validated, ['template' => true]);
        clock($validated);
        Cycle::create($validated);

        return response()->json(['message' => __('Created')], 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCycleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCycleRequest $request)
    {
        $validated = $request->validated();

        Cycle::create($validated);

        return response()->json(['message' => __('Created')], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cycle  $cycle
     * @return \Illuminate\Http\Response
     */
    public function show(Cycle $cycle)
    {
        return new CycleResource($cycle);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCycleRequest  $request
     * @param  \App\Models\Cycle  $cycle
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCycleRequest $request, Cycle $cycle)
    {
        $cycle->update($request->validated());

        return response()->json(['message' => __('Updated')], 202);
    }

    /**
     * Update the sub specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSubCycleRequest  $request
     * @param  \App\Models\Cycle  $cycle
     * @return \Illuminate\Http\Response
     */
    public function subUpdate(UpdateSubCycleRequest $request, Cycle $cycle)
    {
        $cycle->update($request->validated());

        return response()->json(['message' => __('Updated')], 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cycle  $cycle
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cycle $cycle)
    {
        $cycle->delete();

        return response()->json(['message' => __('Deleted')], 204);
    }
}
