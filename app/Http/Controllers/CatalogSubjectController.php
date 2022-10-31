<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Models\CatalogSubject;
use App\Http\Requests\StoreCatalogRequest;
use App\Http\Requests\IndexCatalogSubjectRequest;
use App\Http\Resources\CatalogSubjectNameResource;
use App\Http\Resources\CatalogSubjectGroupResource;
use App\Http\Resources\CatalogSubjectYearsResource;

class CatalogSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexCatalogSubjectRequest $request)
    {
        $validated = $request->validated();

        $perPage = Helpers::getPerPage('items_per_page', $validated);

        $catalog = CatalogSubject::with(['group'])
            ->select(['id', 'year', 'group_id'])->where('selective_discipline_id', 1);
        return CatalogSubjectGroupResource::collection($catalog->paginate());
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
    public function store(StoreCatalogRequest $request)
    {
        $validated = $request->validated();
        CatalogSubject::create($validated);
        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CatalogSubject  $catalogSubject
     * @return \Illuminate\Http\Response
     */
    public function show(CatalogSubject $catalogSubject)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CatalogSubject  $catalogSubject
     * @return \Illuminate\Http\Response
     */
    public function edit(CatalogSubject $catalogSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CatalogSubject  $catalogSubject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CatalogSubject $catalogSubject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CatalogSubject  $catalogSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(CatalogSubject $catalogSubject)
    {
        //
    }

    /**
     * Get unique years from CatalogSubjects for filters
     *
     * @return \Illuminate\Http\Response
     */
    public function getYears()
    {
        $years = CatalogSubject::select('year')->distinct()->orderBy('year', 'desc')->get();
        return CatalogSubjectYearsResource::collection($years);
    }

    public function getCatalogs()
    {
        $catalogs = CatalogSubject::with('group')->select('id', 'year', 'group_id')->orderBy('year', 'desc')->get();
        return CatalogSubjectNameResource::collection($catalogs);
    }
}
