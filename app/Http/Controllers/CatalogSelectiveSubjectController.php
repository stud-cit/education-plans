<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Http\Requests\IndexCatalogSelectiveSubjectRequest;
use Illuminate\Http\Request;
use App\Models\CatalogSelectiveSubject;
use App\Http\Resources\CatalogSelectiveSubjectResource;

class CatalogSelectiveSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexCatalogSelectiveSubjectRequest $request)
    {
        $validated = $request->validated();
        clock($validated);

        $perPage = Helpers::getPerPage('items_per_page', $validated);

        $catalog = CatalogSelectiveSubject
            ::with(['selectiveCatalog.group'])
            ->select('id', 'title', 'faculty_id', 'department_id', 'catalog_subject_id')
            ->filterBy($validated)
            ->paginate($perPage);

        return CatalogSelectiveSubjectResource::collection($catalog);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // TODO: get data for save
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
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Http\Response
     */
    public function show(CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Http\Response
     */
    public function edit(CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        //
    }
}
