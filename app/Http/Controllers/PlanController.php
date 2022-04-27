<?php

namespace App\Http\Controllers;

use App\ExternalServices\ASU;
use App\Http\Constant;
use App\Http\Requests\indexPlanRequest;
use App\Http\Requests\StoreGeneralPlanRequest;
use App\Http\Resources\PlanResource;
use App\Http\Resources\PlanShowResource;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    private function stringToBoolean(string $string): bool
    {
        return filter_var($string, FILTER_VALIDATE_BOOLEAN);
    }

    private function ordering($method): string
    {
        $method = $method ?? 'false';
        $input = $this->stringToBoolean($method);
        return $input ? 'desc' : 'asc';
    }

    public function index(IndexPlanRequest $request)
    {
        $validated = $request->validated();

        $perPage = array_key_exists('items_per_page', $validated) ? $validated['items_per_page'] : Constant::PAGINATE;

        $plans = Plan::select('id', 'title', 'year', 'faculty_id', 'department_id', 'created_at')
            ->filterBy($validated)
            ->when($validated['sort_by'] ?? false, function ($query) use ($validated) {
                return $query->orderBy($validated['sort_by'], $this->ordering($validated['sort_desc']));
            })
            ->paginate($perPage);

        return PlanResource::collection($plans);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGeneralPlanRequest $request)
    {
        $validated = $request->validated();
        dd('error'); //ToDO Store plan
        Plan::create($validated);

        return $this->success(__('messages.Created'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function create()
    {
        $asu = new ASU();
        $formStudy = new  FormStudyController();
        $studyTerm = new StudyTermController();
        $formOrganization = new FormOrganizationController();
        $educationLevel = new EducationLevelController();

        $data = [
            'faculties' => $asu->getFaculty(),
            'specialities' => $formStudy->index(), //ToDo add methods get specialities with asu
            'educational_programs' => $formStudy->index(), //ToDo add methods get educationalPrograms with asu
            'qualifications' => $formStudy->index(), //ToDo add methods get qualifications with asu
            'fields_knowledge' => $formStudy->index(), //ToDo add methods get qualifications with asu
            'forms_study' => $formStudy->index(),
            'terms_study' => $studyTerm->index(),
            'forms_organizationStudy' => $formOrganization->index(),
            'educations_level' => $educationLevel->index(),
        ];

        return response()->json($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $plan)
    {
        $model = $plan->load(['formStudy', 'educationLevel', 'formOrganization']);
        return new PlanShowResource($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Plan $plan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        $plan->delete();
        return response()->json(['message' => __('messages.Deleted')], 204);
    }

    public function copy(Plan $plan)
    {
        $plan->replicateRow();

        return $this->success(__('messages.Copied'), 201);
    }
}
