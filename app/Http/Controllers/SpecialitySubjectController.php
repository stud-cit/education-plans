<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Helpers\Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CatalogSelectiveSubject;
use App\Http\Resources\SpecialitySubject\SpecialitySubjectResource;
use App\Http\Requests\SpecialitySubject\IndexSpecialitySubjectRequest;
use App\Http\Requests\SpecialitySubject\StoreSpecialitySubjectRequest;
use App\Http\Requests\SpecialitySubject\UpdateSpecialitySubjectRequest;
use App\Http\Resources\SpecialitySubject\SpecialitySubjectEditResource;
use App\Http\Resources\SpecialitySubject\SpecialitySubjectShowResource;

class SpecialitySubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IndexSpecialitySubjectRequest $request)
    {
        $validated = $request->validated();

        $perPage = Helpers::getPerPage('items_per_page', $validated);

        $catalog = CatalogSelectiveSubject::select(
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

        return SpecialitySubjectResource::collection($catalog);
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
    public function store(StoreSpecialitySubjectRequest $request)
    {
        $validated = $request->validated();

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
        ]);

        return new SpecialitySubjectShowResource($modelWithRelations);
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
        ]);

        return new SpecialitySubjectEditResource($modelWithRelations);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CatalogSelectiveSubject  $catalogSelectiveSubject
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSpecialitySubjectRequest $request, CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        $validated = $request->validated();

        $model = $catalogSelectiveSubject->load([
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
    public function destroy(CatalogSelectiveSubject $catalogSelectiveSubject)
    {
        $catalogSelectiveSubject->languages()->delete();
        $catalogSelectiveSubject->teachers()->delete();
        $catalogSelectiveSubject->delete();

        return $this->success(__('messages.Deleted'), 200);
    }
}