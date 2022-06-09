<?php

namespace App\Http\Controllers;

use App\ExternalServices\Asu\Department;
use App\ExternalServices\Asu\Profession;
use App\ExternalServices\Asu\Qualification;
use App\Helpers\Tree;
use App\Http\Constant;
use App\Http\Requests\indexPlanRequest;
use App\Http\Requests\StoreCycleRequest;
use App\Http\Requests\StoreGeneralPlanRequest;
use App\Http\Requests\StorePlanVerificationRequest;
use App\Http\Requests\UpdateCycleRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Resources\FacultiesResource;
use App\Http\Resources\PlanEditResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\PlanShowResource;
use App\Http\Resources\ProfessionsResource;
use App\Models\Cycle;
use App\Models\HoursModules;
use App\Models\SemestersCredits;
use App\Models\Plan;
use App\Models\Subject;
use Illuminate\Support\Str;

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
        $validated['guid'] = Str::uuid();
        $plan = Plan::create($validated);

        return response()->json(['id' => $plan->id, 'message' => __('messages.Created')], 201);
    }

    /**
     * Display the specified resource.
     */
    public function create()
    {
        $asu = new Department();
        $professions = new Profession();
        $qualifications = new Qualification();
        $formStudy = new  FormStudyController();
        $studyTerm = new StudyTermController();
        $formOrganization = new FormOrganizationController();
        $educationLevel = new EducationLevelController();

        $data = [
            'faculties' => FacultiesResource::collection($asu->getFaculties()),
            'fields_knowledge' => ProfessionsResource::collection($professions->getFieldKnowledge()),
            'qualifications' => ProfessionsResource::collection($qualifications->getQualifications()),
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
        $model = $plan->load([
          'verification',
          'formStudy',
          'educationLevel',
          'formOrganization',
          'studyTerm',
          'cycles.cycles',
          'cycles.subjects.semestersCredits',
          'cycles.subjects.hoursModules.formControl',
          'cycles.subjects.hoursModules.individualTask'
        ]);

        return new PlanShowResource($model);
    }

    /**
     * Display the specified resource.
     *
     * @param Plan $plan
     * @return PlanEditResource
     */
    public function edit(Plan $plan)
    {
        $model = $plan->load([
            'formStudy',
            'educationLevel',
            'formOrganization',
            'studyTerm',
            'cycles.cycles',
            'cycles.subjects.semestersCredits',
            'cycles.subjects.hoursModules.formControl',
            'cycles.subjects.hoursModules.individualTask'
        ]);

        return new PlanEditResource($model);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatePlanRequest  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePlanRequest $request, Plan $plan)
    {
        $validated = $request->validated();

        $plan->update($validated);

        return $this->success( __('messages.Updated'), 201);
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
      $model = $plan->load([
        'cycles.cycles',
        'cycles.subjects.semestersCredits',
        'cycles.subjects.hoursModules',
      ]);
      $clonePlan = $plan->duplicate();
      foreach ($model->cycles as $cycle) {
        if($cycle['cycle_id'] == null) {
          $this->createCycle($cycle, $clonePlan->id);
        }
      }
      return $this->success(__('messages.Copied'), 201);
    }

    function createCycle($cycle, $plan_id, $cycleId = null) {
      $cloneCycle = Cycle::create([
        "title" => $cycle['title'],
        "cycle_id" => $cycleId,
        "credit" => $cycle['credit'],
        "plan_id" => $plan_id
      ]);
      foreach ($cycle['subjects'] as $subject) {
        $cloneSubject = Subject::create([
          "title" => $subject['title'],
          "cycle_id" => $cloneCycle->id,
          "selective_discipline_id" => $subject['selective_discipline_id'],
          "credits" => $subject['credits'],
          "hours" => $subject['hours'],
          "practices" => $subject['practices'],
          "laboratories" => $subject['laboratories']
        ]);
        foreach ($subject->hoursModules as $hoursModule) {
          HoursModules::create([
            "course" => $hoursModule['course'],
            "hour" => $hoursModule['hour'],
            "subject_id" => $cloneSubject->id,
            "form_control_id" => $hoursModule['form_control_id'],
            "individual_task_id" => $hoursModule['individual_task_id'],
            "module" => $hoursModule['module'],
            "semester" => $hoursModule['semester']
          ]);
        }
        foreach ($subject->semestersCredits as $semestersCredit) {
          SemestersCredits::create([
            "course" => $semestersCredit['course'],
            "subject_id" => $cloneSubject->id,
            "credit" => $semestersCredit['credit'],
            "semester" => $semestersCredit['semester']
          ]);
        }
      }
      foreach ($cycle['cycles'] as $v) {
        $this->createCycle($v, $plan_id, $cloneCycle->id);
      }
    }

    public function verification(StorePlanVerificationRequest $request, Plan $plan)
    {
      $validated = $request->validated();
      $plan->verification()->updateOrCreate(
        [
          "plan_id" => $plan->id,
          'verification_statuses_id' => $validated['verification_statuses_id']
        ],
        [
          'user_id' => $validated['user_id'],
          'comment' => isset($validated['comment']) ? $validated['comment'] : null,
          'status' => $validated['status']
        ]
      );
      $this->success(__('messages.Updated'), 200);
    }

    public function cycleStore(StoreCycleRequest $request, Plan $plan)
    {
        $validated = $request->validated();

        $plan->cycles()->create($validated);

        $data = Tree::makeTree($plan->cycles);

        return response()->json(['data' => $data], 201);
    }

    public function cycleUpdate(UpdateCycleRequest $request, Plan $plan, Cycle $cycle)
    {
        $validated = $request->validated();

        if ($plan->id === $cycle->plan_id) {
            $cycle->update($validated);
        }

        return $this->success(__('messages.Updated'), 200);
    }

    public function cycleDestroy(Plan $plan, Cycle $cycle)
    {
        try {
            Cycle::where('id', $cycle->id)->where('plan_id', $plan->id)->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            $this->error($e->getMessage(), $e->getCode());
        }

        return $this->success(__('messages.Deleted'), 200);
    }

    public function cyclesWithSubjects(Plan $plan)
    {
        $model = $plan->load('cycles');

        $cyclesWithSubjects = $model->cycles;

        if ($cyclesWithSubjects->isEmpty()) {
            return $this->error(__('Circles_not_found'), 404);
        }

        $data = Tree::makeTree($cyclesWithSubjects);

        return response()->json(['data' => $data], 200);
    }

    function getProgramsOP() {
      $data = json_decode(file_get_contents('https://op.sumdu.edu.ua/get-programs'), true);
      return response()->json($data, 200);
    }
}
