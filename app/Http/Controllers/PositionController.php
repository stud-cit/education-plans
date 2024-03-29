<?php

namespace App\Http\Controllers;

use App\Http\Requests\{ StorePositionRequest, UpdatePositionRequest };
use App\Http\Resources\PositionResource;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Position::class);
    }

    public function index()
    {
        return PositionResource::collection(Position::select('id', 'position', 'agreed')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePositionRequest $request)
    {
        $validated = $request->validated();

        Position::create($validated);

        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePositionRequest $request, Position $position)
    {
        $validated = $request->validated();

        $position->update($validated);

        return $this->success(__('messages.Updated'), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(Position $position)
    {
        try {
            $position->delete();
        } catch (\Illuminate\Database\QueryException $error) {
            return $this->error($error->getMessage(), $error->getCode());
        }
        return $this->success(__('messages.Deleted'), 200);
    }
}
