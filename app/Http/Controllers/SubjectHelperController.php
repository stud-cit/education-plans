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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SubjectHelper  $subjectHelper
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubjectHelper $subjectHelper)
    {
        //
    }
}
