<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Models\CatalogSubject;
use App\Models\CatalogEducationLevel;
use App\ExternalServices\Asu\Subjects;
use App\Models\CatalogSelectiveSubject;
use App\Http\Controllers\SubjectLanguageController;
use App\Http\Resources\CatalogSelectiveSubjectResource;
use App\Http\Requests\IndexCatalogSelectiveSubjectRequest;
use App\Http\Requests\StoreCatalogSelectiveSubjectRequest;
use App\Models\SubjectHelper;

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
        $asu = new AsuController();
        $language = new SubjectLanguageController();
        $catalogs = new CatalogSubjectController();
        $helpers = SubjectHelper::with('type')->where('selective_discipline_id', 1)->select(
            'id',
            'title',
            'catalog_helper_type_id',
        )->get();

        $data = [
            'catalogs' => $catalogs->getCatalogs(),
            'subjects' => $asu->getSubjects(),
            'languages' => $language->index(),
            'educationsLevel' => CatalogEducationLevel::select('id', 'title')->get(),
            'faculties' => $asu->faculties(),
            'departments' => $asu->getDepartments(),
            'teachers' => $asu->getWorkers(), // TODO: sort
            'helpersGeneralCompetence' => $helpers->where('type.key', 'general_competence')->pluck('title'),
            'helpersResultsOfStudy' => $helpers->where('type.key', 'learning_outcomes')->pluck('title'),
            'helpersTypesTrainingSessions' => $helpers->where('type.key', 'types_educational_activities')->pluck('title'),
            'helpersRequirements' => $helpers->where('type.key', 'entry_requirements_applicants')->pluck('title'),
        ];

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCatalogSelectiveSubjectRequest $request)
    {
        $validated = $request->validated();

        CatalogSelectiveSubject::create($validated);

        $this->success(__('messages.Created'), 201);
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
