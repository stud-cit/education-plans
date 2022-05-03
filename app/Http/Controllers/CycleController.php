<?php

namespace App\Http\Controllers;

use App\Models\Cycle;
use App\Http\Resources\CycleResource;
use App\Http\Requests\StoreCycleRequest;
use App\Http\Requests\UpdateCycleRequest;

class CycleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Cycle::whereNull('cycle_id')->with('cycles', 'subjects')->get();
        return response()->json($data, 200);
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

        $model = Cycle::create($validated);

        return (new CycleResource($model->fresh()))->response()->setStatusCode(201);
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

        return $this->success(__('messages.Updated'), 202);
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

        return $this->success(__('messages.Deleted'), 200);
    }
}
