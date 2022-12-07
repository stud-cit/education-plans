<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Helpers\Helpers;
use App\Models\SubjectHelper;
use App\Models\CatalogSpeciality;
use App\Models\SpecialitySubject;
use App\Http\Resources\SpecialitySubject\SpecialitySubjectResource;
use App\Http\Requests\SpecialitySubject\IndexSpecialitySubjectRequest;
use App\Http\Requests\SpecialitySubject\StoreSpecialitySubjectRequest;
use App\Http\Requests\SpecialitySubject\UpdateSpecialitySubjectRequest;
use App\Http\Resources\SpecialitySubject\SpecialitySubjectEditResource;
use App\Http\Resources\SpecialitySubject\SpecialitySubjectShowResource;

class SpecialitySubjectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SpecialitySubject::class);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexSpecialitySubjectRequest $request)
    {
        $validated = $request->validated();

        $perPage = Helpers::getPerPage('items_per_page', $validated);

        $subject = SpecialitySubject::select(
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

        $catalog = CatalogSpeciality::findOrFail($validated['catalogSubject']);

        return SpecialitySubjectResource::collection($subject)->additional([
            'catalog' => [
                'id' => $catalog->id,
                'title' => $catalog->specialityCatalogName,
                'year' => $catalog->year,
                'speciality' => $catalog->specialityIdName,
                'education_level' => $catalog->educationLevel->title,
                'faculty' => $catalog->facultyName,
                'department' => $catalog->departmentName,
                'owners' => $catalog->owners->map(fn ($owner) => ['id' => $owner->department_id])
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
    public function store(StoreSpecialitySubjectRequest $request)
    {
        $validated = $request->validated();

        $subject = SpecialitySubject::create($validated);

        $subject->languages()->createMany($validated['language']);

        $subject->lecturersSave($validated['lecturers']);
        $subject->practiceSave($validated['practice']);

        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SpecialitySubject  $specialitySubject
     * @return \Illuminate\Http\Response
     */
    public function show(SpecialitySubject $specialitySubject)
    {
        $modelWithRelations = $specialitySubject->load([
            'languages.language',
            'lecturers',
            'practice',
        ]);

        return new SpecialitySubjectShowResource($modelWithRelations);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SpecialitySubject  $specialitySubject
     * @return \Illuminate\Http\Response
     */
    public function edit(SpecialitySubject $specialitySubject)
    {
        $modelWithRelations = $specialitySubject->load([
            'languages.language',
            'lecturers',
            'practice',
        ]);

        return new SpecialitySubjectEditResource($modelWithRelations);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SpecialitySubject  $specialitySubject
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSpecialitySubjectRequest $request, SpecialitySubject $specialitySubject)
    {
        $validated = $request->validated();

        $model = $specialitySubject->load([
            'languages',
            'lecturers',
            'practice',
        ]);

        $model->update($validated);

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SpecialitySubject $specialitySubject)
    {
        $specialitySubject->languages()->delete();
        $specialitySubject->teachers()->delete();
        $specialitySubject->delete();

        return $this->success(__('messages.Deleted'), 200);
    }
}
