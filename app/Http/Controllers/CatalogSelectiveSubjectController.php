<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Helpers\Helpers;
use App\Models\SubjectHelper;
use App\Models\EducationLevel;
use App\Models\VerificationStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\CatalogSelectiveSubject;
use App\ExternalServices\Asu\Department;
use App\Http\Resources\FacultiesResource;
use App\Http\Resources\ProfessionsResource;
use App\Http\Controllers\SubjectLanguageController;
use App\Http\Resources\CatalogSelectiveSubjectResource;
use App\Http\Resources\CatalogSelectiveSubjectEditResource;
use App\Http\Resources\CatalogSelectiveSubjectShowResource;
use App\Http\Requests\CatalogSelectiveSubject\StoreSubjectVerificationRequest;
use App\Http\Requests\CatalogSelectiveSubject\ToggleSubjectVerificationRequest;
use App\Http\Requests\CatalogSelectiveSubject\IndexCatalogSelectiveSubjectRequest;
use App\Http\Requests\CatalogSelectiveSubject\StoreCatalogSelectiveSubjectRequest;
use App\Http\Requests\CatalogSelectiveSubject\UpdateCatalogSelectiveSubjectRequest;
use App\Http\Resources\EducationLevelResource;

class CatalogSelectiveSubjectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(CatalogSelectiveSubject::class);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexCatalogSelectiveSubjectRequest $request)
    {
        $validated = $request->validated();

        $perPage = Helpers::getPerPage('items_per_page', $validated);

        $catalog = CatalogSelectiveSubject::with(['selectiveCatalog.group', 'verifications.role'])
            ->select('id', 'title', 'faculty_id', 'department_id', 'catalog_subject_id', 'published', 'user_id', 'need_verification')
            ->ofUserType(Auth::user()->role_id)
            ->filterBy($validated)
            ->orderBy('created_at')
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

        $educationLevels = EducationLevelResource::collection(
            EducationLevel::withTrashed()->select('id', 'title', 'deleted_at')->orderBy('deleted_at')->get()
        );

        $data = [
            'catalogs' => $catalogs->getCatalogs(),
            'subjects' => $asu->getSubjects(),
            'languages' => $language->getList(),
            'educationsLevel' => $educationLevels,
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

        // $validated['user_id'] = Auth::id();

        $subject = CatalogSelectiveSubject::create($validated);

        $subject->languages()->createMany($validated['language']);

        $subject->lecturersSave($validated['lecturers']);
        $subject->practiceSave($validated['practice']);

        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Http\Response
     */
    public function show(CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        $modelWithRelations = $catalogSelectiveSubject->load([
            'languages.language',
            'lecturers',
            'practice',
            'educationLevel',
            'verifications'
        ]);

        return new CatalogSelectiveSubjectShowResource($modelWithRelations);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Http\Response
     */
    public function edit(CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        $modelWithRelations = $catalogSelectiveSubject->load([
            'languages.language',
            'lecturers',
            'practice',
            'educationLevel'
        ]);

        return new CatalogSelectiveSubjectEditResource($modelWithRelations);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCatalogSelectiveSubjectRequest $request, CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        $validated = $request->validated();

        $model = $catalogSelectiveSubject->load([
            'languages',
            'lecturers',
            'practice',
            'educationLevel',
            'verifications'
        ]);

        $model->update($validated);

        $model->verifications()->delete();

        $model->languages()->whereNotIn('id', $this->getIds($validated['language']))->delete();

        foreach ($validated['language'] as $language) {
            if (array_key_exists('title', $language)) {
                unset($language['title']);
            }
            $model->languages()->updateOrCreate($language);
        }

        $model->lecturers()->whereNotIn('id', $this->getIds($validated['lecturers']))->delete();
        $model->updateTeachers($validated['lecturers'], Teacher::LECTOR);

        $model->practice()->whereNotIn('id', $this->getIds($validated['practice']))->delete();
        $model->updateTeachers($validated['practice'], Teacher::PRACTICE);

        return $this->success(__('messages.Updated'));
    }

    protected function getIds($records)
    {
        return array_column($records, 'id');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Http\Response
     */
    public function destroy(CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        $catalogSelectiveSubject->languages()->delete();
        $catalogSelectiveSubject->teachers()->delete();
        $catalogSelectiveSubject->verifications()->delete();
        $catalogSelectiveSubject->delete();

        return $this->success(__('messages.Deleted'), 200);
    }

    public function verification(StoreSubjectVerificationRequest $request,  CatalogSelectiveSubject $catalogSelectiveSubject)
    {

        if (!Gate::allows('can-verification', $catalogSelectiveSubject)) {
            abort(403);
        }

        $validated = $request->validated();

        if (array_key_exists('comment', $validated)) {
            if ($validated['comment'] !== null) {
                $catalogSelectiveSubject->need_verification = false;
                $catalogSelectiveSubject->update();
            }
        }

        if (Auth::user()->role_id === User::ADMIN) {
            $catalogSelectiveSubject->need_verification = true;
            $catalogSelectiveSubject->update();
        }

        $catalogSelectiveSubject->verifications()->updateOrCreate(
            [
                'verification_status_id' => $validated['verification_status_id'],
                'subject_id' => $validated['subject_id']
            ],
            [
                'status' => $validated['status'],
                'comment' => isset($validated['comment']) ? $validated['comment'] : null,
                'user_id' => $validated['user_id'],
            ]
        );

        return $this->success(__('messages.Updated'), 200);
    }

    public function toggleToVerification(ToggleSubjectVerificationRequest $request, CatalogSelectiveSubject $catalogSelectiveSubject)
    {

        if (!Gate::allows('toggle-need-verification', $catalogSelectiveSubject)) {
            abort(403);
        }

        $validated = $request->validated();

        $catalogSelectiveSubject->need_verification = $validated['need_verification'];
        $catalogSelectiveSubject->published = true;

        $catalogSelectiveSubject->update();

        return $this->success(__('messages.Updated'), 200);
    }

    public function getItemsFilters()
    {
        $modelVerificationStatuses = new VerificationStatuses;
        $asu = new Department();
        $user = Auth::user();

        $divisions = VerificationStatuses::select('id', 'title')->where('type', 'subject')->get();
        clock($divisions->toArray());
        $verificationsStatus = $modelVerificationStatuses->getDivisionStatuses();
        $faculties = $asu->getFaculties()->when(
            $user->possibility([User::FACULTY_INSTITUTE, User::DEPARTMENT]),
            fn ($collections) => $collections->filter(fn ($faculty) => $faculty['id'] == $user->faculty_id)
        );

        return response([
            'divisions' => ProfessionsResource::collection($divisions),
            'verificationsStatus' => $verificationsStatus,
            'faculties' => FacultiesResource::collection($faculties)
        ]);
    }
}
