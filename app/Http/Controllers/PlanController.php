<?php

namespace App\Http\Controllers;

use App\Http\Resources\FacultiesResource;
use App\Models\Plan;
use App\Helpers\Tree;
use App\Models\Cycle;
use App\Models\Subject;
use App\Models\HoursModules;
use App\Http\Constant;
use Illuminate\Http\Request;
use App\ExternalServices\ASU;
use App\Http\Resources\PlanResource;
use App\Http\Requests\indexPlanRequest;
use App\Http\Requests\StoreCycleRequest;
use App\Http\Resources\PlanShowResource;
use App\Http\Requests\UpdateCycleRequest;
use App\Http\Requests\StoreGeneralPlanRequest;
use App\Http\Requests\UpdatePlanRequest;

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

        $plan = Plan::create($validated);

        return response()->json(['id' => $plan->id, 'message' => __('messages.Created')], 201);
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
            'faculties' => FacultiesResource::collection($asu->getFaculties()),
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

        return new PlanShowResource($model);
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

        $plan->save($validated);

        $this->success(__('messages.Updated'), 200);
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
        'cycles.subjects'
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
        foreach ($subject['hours_modules'] as $hoursModules) {
          HoursModules::create([
            "course" => $hoursModules['course'],
            "hour" => $hoursModules['hour'],
            "subject_id" => $cloneSubject->id,
            "form_control_id" => $hoursModules['form_control_id'],
            "individual_task_id" => $hoursModules['individual_task_id'],
            "module" => $hoursModules['module'],
            "semester" => $hoursModules['semester']
          ]);
        }
      }
      foreach ($cycle['cycles'] as $v) {
        $this->createCycle($v, $plan_id, $cloneCycle->id);
      }
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
}
