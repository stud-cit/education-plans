<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Helpers\Tree;
use App\Models\Cycle;
use App\Http\Constant;
use App\Models\Subject;
use App\Helpers\Helpers;
use App\Models\PlanType;
use App\Models\StudyTerm;
use Illuminate\Support\Str;
use App\Models\HoursModules;
use Illuminate\Http\Request;
use App\Models\ShortenedPlan;
use App\ExternalServices\Op\OP;
use App\Helpers\GeneratePlanPdf;
use App\Models\PlanVerification;
use App\Models\SemestersCredits;
use App\Models\CatalogSpeciality;
use App\Helpers\GenerateCatalogPdf;
use App\Http\Resources\PlanResource;
use App\Models\VerificationStatuses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\indexPlanRequest;
use App\Models\CatalogEducationProgram;
use App\ExternalServices\Asu\Department;
use App\ExternalServices\Asu\Profession;
use App\Http\Requests\CatalogPdfRequest;
use App\Http\Requests\StoreCycleRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Resources\PlanEditResource;
use App\Http\Resources\PlanShowResource;
use App\Http\Requests\UpdateCycleRequest;
use App\Http\Resources\FacultiesResource;
use App\ExternalServices\Asu\Qualification;
use App\Http\Resources\ProfessionsResource;
use App\Http\Requests\Plan\DuplicateRequest;
use App\Http\Requests\Plan\ShortPlanRequest;
use App\Http\Requests\Plan\SignedPlanRequest;
use App\Http\Requests\StoreGeneralPlanRequest;
use App\Http\Resources\Api\CatalogPdfResource;
use App\Http\Resources\Plan\ShortPlanResource;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\Plan\SignedPlanResource;
use App\Http\Requests\Plan\SignedPlanByIdRequest;
use App\Http\Resources\Api\EducationPlanResource;
use App\Http\Requests\StorePlanVerificationRequest;
use App\Http\Resources\Api\EducationPlanShowResource;
use App\Http\Resources\Plan\SignedPlanIdSemesterResource;
use App\Http\Resources\CatalogSpeciality\CatalogSpecialityPdfResource;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Plan::class);
    }

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

        $plans = Plan::select(
            'id',
            'type_id',
            'title',
            'version',
            'year',
            'faculty_id',
            'department_id',
            'published',
            'author_id',
            'published',
            'created_at',
            'need_verification',
            'speciality_id',
            'education_level_id',
            'education_program_id',
            'study_term_id',
            'deleted_at'
        )->with(['verification.role', 'verification', 'studyTerm'])
            ->when(!$request->user()->possibility(User::PRIVILEGED_ROLES), fn($query) => $query->published())
            ->ofUserType(Auth::user()->role_id)
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
    public function create() {}

    /**
     * Returns additional data for creating or editing a plan.
     * @return \Illuminate\Http\JsonResponse
     */

    public function additionalDataActionsPlan()
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
            'educations_level' => $educationLevel->list(),
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
            'cycles.subjects.subjects',
            'cycles.subjects.semestersCredits',
            'cycles.subjects.exams',
            'cycles.subjects.test',
            'cycles.subjects.hoursModules.formControl',
            'cycles.subjects.hoursModules.individualTask',
            'signatures'
        ]);

        return (new PlanShowResource($model))->additional(['actions' => [
            'can_generate_short_plan' => Gate::allows('generate-short-plan', $plan)
        ]]);
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
            'verification',
            'formStudy:id,title',
            'educationLevel:id,title,deleted_at',
            'formOrganization:id,title',
            'studyTerm:id,title,year,month,course,module,semesters',
            'cycles.cycles',
            'cycles.subjects.subjects',
            'cycles.subjects.exams',
            'cycles.subjects.semestersCredits',
            'cycles.subjects.hoursModules.formControl',
            'cycles.subjects.hoursModules.individualTask',
            'signatures'
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

        $role_id = Auth::user()->role_id;

        $plan->load(['verification']);

        if ($plan->isNotTemplate()) {
            $validated['title'] = $plan->generateTitle();
        }

        $hasAnyVerification = $plan->verification->isNotEmpty();
        $emptyVerification = $plan->verification->isEmpty();

        if ($plan->isNotShort() && ($role_id == User::FACULTY_INSTITUTE || $role_id == User::DEPARTMENT)) {

            if ($hasAnyVerification) {
                $verifications = PlanVerification::where("plan_id", $plan->id)->get();
                $comments = $verifications->whereNotNull('comment');

                PlanVerification::where("plan_id", $plan->id)->delete();

                $plan->update([
                    'need_verification' => false,
                    'not_conventional' => false,
                    'comment' => null,
                    'verification_comments' => $comments
                ]);
                unset($validated['need_verification'], $validated['not_conventional'], $validated['comment']);
            }

            // fix for bad updated plan
            if ($emptyVerification && $plan->not_conventional && $plan->isClean('not_conventional')) {
                $plan->update(['not_conventional' => false, 'comment' => null]);
                unset($validated['not_conventional'], $validated['comment']);
            }
        }

        $plan->update($validated);

        return $this->success(__('messages.Updated'), 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Plan $plan)
    {
        if ($plan->type_id === Plan::SHORT) {
            $relation = ShortenedPlan::where('plan_id', $plan->id)->first();
            ($relation) ? $relation->delete() : '';
        }

        $plan->delete();
        return response()->json(['message' => __('messages.Deleted')], 204);
    }

    public function restore(Plan $plan)
    {
        $plan->onlyTrashed()->restore();

        $this->success('Відновлено', 200);
    }

    public function downloadPdf(Plan $plan)
    {
        $pdf = new GeneratePlanPdf;
        $pdf($plan->id, false);
        return $pdf->download();
    }

    public function copy(Plan $plan)
    {

        if (!Gate::allows('copy-plan', $plan)) {
            abort(403);
        }

        $model = $plan->load([
            'cycles.cycles',
            'cycles.subjects.semestersCredits',
            'cycles.subjects.hoursModules',
        ]);

        $plan->title = "Копія " . $plan->title;
        $plan->need_verification = false;

        $clonePlan = $plan->duplicate();

        $user = Auth::user();

        if (!$user->possibility(User::PRIVILEGED_ROLES)) {
            $clonePlan->type_id = Plan::PLAN;
        }

        $clonePlan->parent_id = $plan->id;
        $clonePlan->update();

        foreach ($model->cycles as $cycle) {
            if ($cycle['cycle_id'] == null) {
                $this->createCycle($cycle, $clonePlan->id);
            }
        }
        return response()->json($clonePlan);
    }

    public function searchDuplicate(DuplicateRequest $request)
    {
        $present = false;
        $version = 1;
        $validated = $request->validated();

        $plan = Plan::select('id', 'year', 'speciality_id', 'education_program_id', 'study_term_id', 'type_id', 'version')
            ->where([
                ['year', '=', $validated['year']],
                ['speciality_id', '=', $validated['speciality_id']],
                ['education_program_id', '=', $validated['education_program_id']],
                ['study_term_id', '=', $validated['study_term_id']],
                ['type_id', '=', Plan::PLAN]
            ])
            ->where('id', '!=', $validated['id'])
            ->orderBy('version', 'desc')->get();

        if ($plan->count() > 0) {
            $present = true;
            $first = $plan->first();
            $version = $first->version ? $first->version : 1;
        }

        return response()->json([
            'data' => [
                'hasDuplicate' => $present,
                'version' => $version,
            ]
        ]);
    }

    public function markAsDuplicate(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'duplicate_message' => 'required|max:255',
            'version' => 'required|numeric'
        ]);

        $newVersion = $validated['version'] + 1;

        $plan->duplicate_message = $validated['duplicate_message'];
        $plan->version = $newVersion;
        $plan->save();
        $this->success('Ok', 200);
    }

    function createCycle($cycle, $plan_id, $cycleId = null)
    {
        $cloneCycle = Cycle::create([
            "title" => $cycle['title'],
            "cycle_id" => $cycleId,
            "list_cycle_id" => $cycle['list_cycle_id'],
            "credit" => $cycle['credit'],
            "plan_id" => $plan_id,
            "has_discipline" => $cycle['has_discipline']
        ]);
        foreach ($cycle['subjects'] as $subject) {
            $cloneSubject = Subject::create([
                "asu_id" => $subject['asu_id'],
                "cycle_id" => $cloneCycle->id,
                "selective_discipline_id" => $subject['selective_discipline_id'],
                "credits" => $subject['credits'],
                "hours" => $subject['hours'],
                "practices" => $subject['practices'],
                "laboratories" => $subject['laboratories'],
                "faculty_id" => $subject['faculty_id'],
                "department_id" => $subject['department_id']
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

    private $shortedByYear = 1;
    private $formOrganization = 1;

    /**
     * Generate short plan
     *
     * @param Plan $plan
     * @return ShortPlanResource
     */
    public function shortPlan(ShortPlanRequest $request, Plan $plan)
    {
        if (!Gate::allows('generate-short-plan', $plan)) {
            abort(403);
        }

        $validated = $request->validated();

        $this->shortedByYear = $validated['shortened_by_year'];
        $year = $validated['year'];

        $model = $plan->load([
            'cycles.cycles',
            'cycles.subjects.subjects',
            'cycles.subjects.semestersCredits',
            'cycles.subjects.hoursModules',
        ]);

        $clonePlan = $plan->duplicate();
        $this->formOrganization = $clonePlan->form_organization_id;
        $sYear = $clonePlan->studyTerm->year - $this->shortedByYear;
        $month = $clonePlan->studyTerm->month;
        $credits = 60; // TODO: move to admin panel

        $studyTermId = StudyTerm::select('id', 'year', 'month')->where([
            ['year', $sYear],
            ['month', $clonePlan->studyTerm->month]
        ])->value('id');

        if (!$studyTermId) throw ValidationException::withMessages(
            ["Не існує терміну навчання {$sYear}р. {$month}міс.", 'Зверніться до Адміністратора.']
        );

        $clonePlan->type_id = Plan::SHORT;
        $clonePlan->study_term_id = $studyTermId;
        $clonePlan->year = $year;
        $clonePlan->title = $clonePlan->generateTitle();
        $clonePlan->credits -= $credits * $this->shortedByYear;

        $array = json_decode($clonePlan->schedule_education_process, JSON_OBJECT_AS_ARRAY);
        $newScheduleEducationProcess = [];

        foreach ($array['courses'] as $index => $item) {
            if ($index > $this->shortedByYear - 1) {
                $newScheduleEducationProcess[] = $this->cutCourse($item, ['course'], false);
            }
        }

        $array['courses'] = $newScheduleEducationProcess;
        $clonePlan->schedule_education_process = $array;

        $clonePlan->hours_weeks_semesters = $this->cutCourse(
            json_decode($clonePlan->hours_weeks_semesters, JSON_OBJECT_AS_ARRAY),
            ['course', 'semester']
        );

        $clonePlan->summary_data_budget_time = $this->cutCourse(
            json_decode($clonePlan->summary_data_budget_time, JSON_OBJECT_AS_ARRAY),
            ['course'],
        );

        foreach ($model->cycles as $cycle) {
            if ($cycle['cycle_id'] == null) {
                $this->createCycleCutSubject($cycle, $clonePlan->id);
            }
        }

        $isHasErrors = $clonePlan->isHasErrors();

        foreach ($plan->verification as $item) {

            if ($item['verification_statuses_id'] === 12) {
                continue;
            }

            PlanVerification::create([
                'plan_id' => $clonePlan->id,
                'user_id' => $item['user_id'],
                'verification_statuses_id' => $item['verification_statuses_id'],
                'status' => $item['status'],
                'comment' => $item['comment']
            ]);
        }

        $clonePlan->need_verification = true;

        $clonePlan->save();

        ShortenedPlan::create([
            'plan_id' => $clonePlan->id,
            'parent_id' => $plan->id,
            'shortened_by_year' => $this->shortedByYear,
            'year' => $year,
        ]);

        return new ShortPlanResource([
            'id' => $clonePlan->id,
            'title' => $clonePlan->title,
            'shorted_by_year' => $plan->shortedByYear
        ]);
    }

    public function cutCourse(array $data, array $keys, $checkCourse = true): array
    {
        if (!$data) return null;

        $course = $this->shortedByYear;
        $result = [];
        $index = 1;

        foreach ($data as $item) {
            if ($item['course'] <= $course && $checkCourse) {
                continue;
            }

            foreach ($keys as $key) {
                if (array_key_exists($key, $item)) {
                    switch ($key) {
                        case 'course':
                            $item[$key] = $item[$key] - $course;
                            break;
                        case 'semester':
                            if ($this->formOrganization === 1) {
                                $item[$key] = $item[$key] - $course * 2;
                            } else if ($this->formOrganization === 3) {
                                $item[$key] = $item[$key] - $course * 2;
                            }
                            break;
                        case 'module':
                            if ($this->formOrganization === 1) {
                                $item[$key] = $index;
                            } else if ($this->formOrganization === 3) {
                                $item[$key] = $item[$key] - $course * 2;
                            }
                            break;
                    }
                }
            }
            $result[] = $item;
            $index++;
        }

        return  $result;
    }

    function createCycleCutSubject($cycle, $plan_id, $cycleId = null)
    {
        $cloneCycle = Cycle::create([
            "title" => $cycle['title'],
            "cycle_id" => $cycleId,
            "list_cycle_id" => $cycle['list_cycle_id'],
            "credit" => $cycle['credit'],
            "plan_id" => $plan_id,
            "has_discipline" => $cycle['has_discipline']
        ]);

        // SUBJECT
        $this->copySubjects($cycle['subjects'], $cloneCycle->id);

        foreach ($cycle['cycles'] as $cycle) {
            $this->createCycleCutSubject($cycle, $plan_id, $cloneCycle->id);
        }
    }

    private function copySubjects($subjects, $cycleId, $subject_id = null)
    {
        foreach ($subjects as $subject) {

            $cloneSubject = Subject::create([
                "asu_id" => $subject['asu_id'],
                "cycle_id" => $cycleId,
                "selective_discipline_id" => $subject['selective_discipline_id'],
                "credits" => $subject['credits'],
                "hours" => $subject['hours'],
                "practices" => $subject['practices'],
                "laboratories" => $subject['laboratories'],
                "faculty_id" => $subject['faculty_id'],
                "department_id" => $subject['department_id'],
                "subject_id" => $subject_id
            ]);

            $semestersCreditsCollection = $subject->semestersCredits;

            $semestersCredits = $this->cutCourse(
                $semestersCreditsCollection->toArray(),
                ['course', 'semester']
            );

            $hoursModules = $this->cutCourse(
                $subject->hoursModules->toArray(),
                ['course', 'module', 'semester']
            );

            /**
             * array_sum return double
             */
            $sumHour = array_sum(array_column($hoursModules, 'hour'));
            $sumCredit = array_sum(array_column($semestersCredits, 'credit'));
            $sumHourAndCredits = ($sumHour + $sumCredit) == 0;

            $hasSubSubjects = count($subject->subjects->toArray()) > 0;
            $notHasSubject = !$hasSubSubjects;

            if ($sumHourAndCredits && $cloneSubject->notPartSpecialCycle() && $notHasSubject) {
                $cloneSubject->delete();
                continue;
            }

            foreach ($hoursModules as $hoursModule) {
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

            foreach ($semestersCredits as $semestersCredit) {
                SemestersCredits::create([
                    "course" => $semestersCredit['course'],
                    "subject_id" => $cloneSubject->id,
                    "credit" => $semestersCredit['credit'],
                    "semester" => $semestersCredit['semester']
                ]);
            }

            if ($hasSubSubjects) {
                $this->copySubjects(
                    $subject->subjects,
                    $cycleId,
                    $cloneSubject->id
                );
            }
        }
    }
    public function verification(StorePlanVerificationRequest $request, Plan $plan)
    {
        $validated = $request->validated();

        // split two roles
        if ($validated['verification_status_id'] === User::EDUCATIONAL_DEPARTMENT_CHIEF) {
            $validated['verification_status_id'] = User::EDUCATIONAL_DEPARTMENT_DEPUTY;
        }

        /**
         * verification_status_id приходить роль id,
         * а нам потрібно id верифікації
         */
        if (Auth::user()->role_id !== User::ADMIN && Auth::user()->role_id !== User::ROOT) {
            $verificationStatusId = VerificationStatuses::where(
                [
                    ['role_id', $validated['verification_status_id']],
                    ['type', 'plan']
                ]
            )->value('id');

            $validated['verification_status_id'] = $verificationStatusId;
        }

        $plan->verification()->updateOrCreate(
            [
                "plan_id" => $plan->id,
                'verification_statuses_id' => $validated['verification_status_id']
            ],
            [
                'user_id' => $validated['user_id'],
                'comment' => isset($validated['comment']) ? $validated['comment'] : null,
                'status' => $validated['status']
            ]
        );

        if ($plan->approvedPlan) {
            $pdf = new GeneratePlanPdf;
            $pdf($plan->id);
            $pdf->save();
            $catalogPdf = new GenerateCatalogPdf($plan->id);
            $catalogPdf->generateCatalogSpecialityPdf();
            $catalogPdf->generateCatalogEducationPdf();
        }

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

    public function getItemsFilters()
    {
        $modelVerificationStatuses = new VerificationStatuses;
        $asu = new Department();
        $user = Auth::user();

        $divisions = $modelVerificationStatuses::select('id', 'title')->where('id', '!=', $modelVerificationStatuses::OP)->where('type', 'plan')->get();
        $verificationsStatus = $modelVerificationStatuses->getDivisionStatuses();
        $faculties = $asu->getFaculties()->when(
            $user->possibility([User::FACULTY_INSTITUTE, User::DEPARTMENT]),
            fn($collections) => $collections->filter(fn($faculty) => $faculty['id'] == $user->faculty_id)
        );

        return response([
            'divisions' => ProfessionsResource::collection($divisions),
            'verificationsStatus' => $verificationsStatus,
            'faculties' => FacultiesResource::collection($faculties),
            'types' => PlanType::select('id', 'title')
                ->when(
                    $user->possibility(User::GUEST),
                    fn($q) => $q->where('id', '!=', Plan::TEMPLATE)
                )->get(),
        ]);
    }

    public function catalogPdf(CatalogPdfRequest $request)
    {
        $validated = $request->validated();
        if (array_key_exists('speciality_id', $validated)) {

            $catalog = CatalogSpeciality::with(['subjects', 'signatures'])
                ->where('selective_discipline_id', CatalogSpeciality::SPECIALITY)
                ->where('speciality_id', $validated['speciality_id'])
                ->where('catalog_education_level_id', $validated['education_level'])
                ->whereBetween('year', [$validated['year'], $validated['end_year']])
                ->orderBy('year', 'asc')
                ->get();

            $result = CatalogSpecialityPdfResource::collection($catalog);

            return $result->filter(fn($s) => $s->status === 'success');
        } else if (array_key_exists('education_program_id', $validated)) {

            $catalog = CatalogEducationProgram::with(['subjects', 'signatures'])
                ->where('selective_discipline_id', CatalogEducationProgram::EDUCATION_PROGRAM)
                ->where('education_program_id', $validated['education_program_id'])
                ->where('catalog_education_level_id', $validated['education_level'])
                ->whereBetween('year', [$validated['year'], $validated['end_year']])
                ->orderBy('year', 'asc')
                ->get();

            $result = CatalogSpecialityPdfResource::collection($catalog);

            return $result->filter(fn($s) => $s->status === 'success');
        }
    }

    public function getSignedPlans(SignedPlanRequest $request)
    {
        $validated = $request->validated();

        $plans = Plan::with(
            'verification',
            'cycles.cycles'
        )->select(
            'id',
            'title',
            'guid',
            'year',
            'education_program_id',
            'faculty_id',
            'department_id',
            'qualification_id',
            'field_knowledge_id',
            'speciality_id',
            'education_level_id',
            'type_id',
        )->plan()
            ->where('department_id', $validated['department_id'])
            ->verified()
            ->get();

        return SignedPlanResource::collection($plans);
    }

    public function getSignedPlansById(SignedPlanByIdRequest $request)
    {
        $validated = $request->validated();

        $plan = Plan::select(
            'id',
            'title',
            'guid',
            'year',
            'education_program_id',
            'faculty_id',
            'department_id',
            'qualification_id',
            'field_knowledge_id',
            'speciality_id',
            'specialization_id',
            'education_level_id',
            'type_id',
            'study_term_id'
        )->with([
            'studyTerm',
            'verification',
            'cycles.cycles',
            'cycles.subjects.semestersCredits',
        ])->where('id', $validated['id'])->verified()->first();

        if (!$plan) return response(['message' => 'Plan does not approved!'], 200);

        return new SignedPlanIdSemesterResource($plan);
    }

    public function educationPlans()
    {
        $plans = Plan::with('verification')->select(
            'id',
            'title',
            'guid',
            'year',
            'education_program_id',
            'faculty_id',
            'department_id',
            'qualification_id',
            'field_knowledge_id',
            'speciality_id',
            'education_level_id',
            'type_id',
        )->whereIn('type_id', [Plan::PLAN, Plan::SHORT])
            ->verified()
            ->paginate();

        return EducationPlanResource::collection($plans);
    }

    public function educationPlanShow(Request $request, string $guid)
    {
        $model = Plan::with([
            'verification',
            'formStudy:id,title',
            'educationLevel:id,title',
            'formOrganization:id,title',
            'studyTerm:id,title,year,month,course,module,semesters',
            'cycles.cycles',
            'cycles.subjects.subjects',
            'cycles.subjects.semestersCredits',
            'cycles.subjects.exams',
            'cycles.subjects.test',
            'cycles.subjects.hoursModules.formControl',
            'cycles.subjects.hoursModules.individualTask',
            'signatures'
        ])->verified()->where('guid', $guid)->first();

        $endYear = Helpers::calculateEndYear($model->year, $model->studyTerm);

        return (new EducationPlanShowResource($model))->additional(['catalogs' => [
            'educationProgram' => $this->educationProgram($model, $endYear),
            'speciality' => $this->speciality($model, $endYear),
        ]]);
    }

    private function educationProgram($plan, $endYear)
    {
        $catalog = CatalogEducationProgram::with(['subjects', 'verifications', 'educationLevel', 'signatures'])
            ->where('selective_discipline_id', CatalogEducationProgram::EDUCATION_PROGRAM)
            ->where('education_program_id', $plan['education_program_id'])
            ->where('catalog_education_level_id', $plan['education_level_id'])
            ->whereBetween('year', [$plan['year'], $endYear])
            ->verified()
            ->orderBy('year', 'asc')
            ->get();

        return CatalogPdfResource::collection($catalog);
    }

    private function speciality($plan, $endYear)
    {
        $catalog = CatalogSpeciality::with(['subjects', 'verifications', 'educationLevel', 'signatures'])
            ->where('selective_discipline_id', CatalogSpeciality::SPECIALITY)
            ->where('speciality_id', $plan['speciality_id'])
            ->where('catalog_education_level_id', $plan['education_level_id'])
            ->whereBetween('year', [$plan['year'],  $endYear])
            ->verified()
            ->orderBy('year', 'asc')
            ->get();

        return CatalogPdfResource::collection($catalog);
    }
}
