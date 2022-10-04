<?php

namespace App\Http\Controllers;

use App\Http\Constant;
use App\Http\Requests\IndexSubjectHelperRequest;
use App\Http\Resources\SubjectHelperResource;
use App\Models\SubjectHelper;
use App\Http\Requests\StoreSubjectHelperRequest;
use App\Http\Requests\UpdateSubjectHelperRequest;

class SubjectHelperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexSubjectHelperRequest $request)
    {
        $validated = $request->validated();
        $perPage = array_key_exists('items_per_page', $validated) ? $validated['items_per_page'] : Constant::PAGINATE;

        return SubjectHelperResource::collection(
            SubjectHelper::select(['id', 'title', 'catalog_helper_type_id'])
                ->filterBy($validated)
                ->with('type')
                ->paginate($perPage)
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSubjectHelperRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSubjectHelperRequest $request)
    {
        $validated = $request->validated();

        SubjectHelper::create([
            'title' => $validated['title'],
            'catalog_helper_type_id' => $validated['type']
        ]);

        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SubjectHelper  $subjectHelper
     * @return \Illuminate\Http\Response
     */
    public function show(SubjectHelper $subjectHelper)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSubjectHelperRequest  $request
     * @param  \App\Models\SubjectHelper  $subjectHelper
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSubjectHelperRequest $request, SubjectHelper $subjectHelper)
    {
        $validated = $request->validated();

        $subjectHelper->update([
            'title' => $validated['title'],
            'catalog_helper_type_id' => $validated['type']
        ]);

        return $this->success(__('messages.Updated'), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubjectHelper  $subjectHelper
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubjectHelper $subjectHelper)
    {
        try {
            $subjectHelper->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
        return $this->success(__('messages.Deleted'), 200);
    }
}
