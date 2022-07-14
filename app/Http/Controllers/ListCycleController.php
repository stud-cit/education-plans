<?php

namespace App\Http\Controllers;

use App\Models\ListCycle;
use Illuminate\Http\Request;
use App\Http\Resources\ListCycleResource;

class ListCycleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ListCycle::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ListCycleResource::collection(ListCycle::select('id', 'title', 'general')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ListCycle  $listCycle
     * @return \Illuminate\Http\Response
     */
    public function show(ListCycle $listCycle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ListCycle  $listCycle
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ListCycle $listCycle)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ListCycle  $listCycle
     * @return \Illuminate\Http\Response
     */
    public function destroy(ListCycle $listCycle)
    {
        //
    }
}
