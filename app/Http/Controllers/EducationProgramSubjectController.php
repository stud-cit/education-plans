<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use Illuminate\Http\Request;
use App\Models\SubjectHelper;
use Illuminate\Support\Facades\Gate;
use App\Models\CatalogEducationProgram;
use App\Models\EducationProgramSubject;
use App\Http\Resources\EducationProgramSubject\EducationProgramSubjectResource;
use App\Http\Requests\EducationProgramSubject\IndexEducationProgramSubjectRequest;
use App\Http\Requests\EducationProgramSubject\StoreEducationProgramSubjectRequest;
use App\Http\Resources\EducationProgramSubject\EducationProgramSubjectShowResource;

class EducationProgramSubjectController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(EducationProgramSubject::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexEducationProgramSubjectRequest $request)
    {
        $validated = $request->validated();

        $perPage = Helpers::getPerPage('items_per_page', $validated);

        $subject = EducationProgramSubject::select(
            'id',
            'catalog_subject_id',
            'department_id',
            'faculty_id',
            'user_id',
            'title',
            'published',
        )
            // ->ofUserType(Auth::user()->role_id)
            ->filterBy($validated)
            ->paginate($perPage);

        $catalog = CatalogEducationProgram::with('signatures')->findOrFail($validated['catalogSubject']);

        return EducationProgramSubjectResource::collection($subject)->additional([
            'catalog' => [
                'id' => $catalog->id,
                'title' => $catalog->educationProgramCatalogName,
                'year' => $catalog->year,
                'education_program' => $catalog->educationProgramIdName,
                'education_level' => $catalog->educationLevel->title,
                'faculty' => $catalog->facultyName,
                'department' => $catalog->departmentName,
                'faculty_id' => $catalog->faculty_id,
                'department_id' => $catalog->department_id,
                'owners' => $catalog->owners->map(fn ($owner) => ['id' => $owner->department_id]),
                'can_create' => Gate::allows('create-education-program-subject', $catalog->id),
                'can_verification' => Gate::allows('can-verification-education-program-catalog', $catalog),
                'toggle_to_verification' => Gate::allows('toggle-need-verification-education-program-catalog', $catalog),
                'can_setting' => Gate::allows('can-setting-catalog-education-program', $catalog),
                'need_verification' => $catalog->need_verification,
                'verifications' => $catalog->verifications,
                'signatures' => $catalog->signatures
            ],
        ]);
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
        $helpers = SubjectHelper::with('type')->select(
            'id',
            'title',
            'catalog_helper_type_id',
        )->get();

        $data = [
            'subjects' => $asu->getSubjects(),
            'languages' => $language->getList(),
            'departments' => $asu->getDepartments(),
            'teachers' => $asu->getWorkers(),
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
    public function store(StoreEducationProgramSubjectRequest $request)
    {
        if (!Gate::allows('create-education-program-subject', [$request->catalog_subject_id])) {
            abort(403);
        };

        $validated = $request->validated();

        $subject = EducationProgramSubject::create($validated);

        $subject->languages()->createMany($validated['language']);

        $subject->lecturersSave($validated['lecturers']);
        $subject->practiceSave($validated['practice']);

        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EducationProgramSubject  $educationProgramSubject
     * @return \Illuminate\Http\Response
     */
    public function show(EducationProgramSubject $educationProgramSubject)
    {
        $modelWithRelations = $educationProgramSubject->load([
            'languages.language',
            'lecturers',
            'practice',
        ]);

        return new EducationProgramSubjectShowResource($modelWithRelations);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EducationProgramSubject  $educationProgramSubject
     * @return \Illuminate\Http\Response
     */
    public function edit(EducationProgramSubject $educationProgramSubject)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EducationProgramSubject  $educationProgramSubject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EducationProgramSubject $educationProgramSubject)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EducationProgramSubject  $educationProgramSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(EducationProgramSubject $educationProgramSubject)
    {
        //
    }
}
