<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSelectiveDisciplineRequest;
use App\Http\Requests\UpdateSelectiveDisciplineRequest;
use App\Http\Resources\SelectiveDisciplineResource;
use App\Models\SelectiveDiscipline;


class SelectiveDisciplineController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(SelectiveDiscipline::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $disciplines = SelectiveDiscipline::select('id', 'title')->get();

        return SelectiveDisciplineResource::collection($disciplines);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSelectiveDisciplineRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSelectiveDisciplineRequest $request, SelectiveDiscipline $selectiveDiscipline)
    {
        $validated = $request->validated();

        $selectiveDiscipline->update($validated);

        return $this->success(__('messages.Updated'), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SelectiveDiscipline $selectiveDiscipline)
    {
        //
    }
}
