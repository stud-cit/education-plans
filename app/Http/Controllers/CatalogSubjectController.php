<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Models\CatalogSubject;
use App\Http\Requests\CatalogSubject\{
    IndexCatalogSubjectRequest,
    StoreCatalogRequest,
    PdfCatalogSubjectRequest,
};
use App\Http\Resources\CatalogSubject\{
    CatalogSubjectYearsResource,
    CatalogSubjectDisciplineResource,
    CatalogSubjectGroupResource,
    CatalogSubjectNameResource
};

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
            ->select(['id', 'year', 'group_id'])
            ->filterBy($validated);

        return CatalogSubjectGroupResource::collection($catalog->paginate($perPage));
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
        $validated['selective_discipline_id'] = 1;

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
        $years = CatalogSubject::select('year')->where('group_id', '!=', null)->distinct()->orderBy('year', 'desc')->get();
        return CatalogSubjectYearsResource::collection($years);
    }

    public function getCatalogs()
    {
        $catalogs = CatalogSubject::whereHas('group')->select('id', 'year', 'group_id')->orderBy('year', 'desc')->get();
        clock($catalogs->toArray());
        return CatalogSubjectNameResource::collection($catalogs);
    }

    public function generateSubjectsPDF(PdfCatalogSubjectRequest $request)
    {
        $validated = $request->validated();

        $data = CatalogSubject::with([
            'group',
            'subjects.languages.language',
            'subjects.lecturers',
            'subjects.practice',
            'subjects.educationLevel',
        ])
            ->where('year', $validated['year'])
            ->where('group_id', $validated['group_id'])
            ->select('id', 'year', 'group_id')
            ->first();

        $result = new CatalogSubjectDisciplineResource($data);
        $result->subjects = $result->subjects->filter( fn ($s) => $s->status === 'success');

        return $result;
    }
}
