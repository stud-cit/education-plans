<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Http\Resources\SettingResource;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use GrahamCampbell\ResultType\Success;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return SettingResource::collection(Setting::select('id', 'key', 'title', 'value')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSettingRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSettingRequest $request)
    {
        $validated = $request->validated();
        
        Setting::create($validated);
        
        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show(Setting $setting)
    {
        return new SettingResource($setting);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSettingRequest  $request
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSettingRequest $request, Setting $setting)
    {
        $validated = $request->validated();

        $setting->update($validated);
        
        return $this->success(__('messages.Updated'), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        try {
            $setting->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        return $this->success(__('messages.Deleted'), 200);
    }
}
