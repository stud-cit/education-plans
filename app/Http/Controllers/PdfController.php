<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Http\Constant;
use App\Models\Subject;
use App\Models\HoursModules;
use Illuminate\Http\Request;
use App\Helpers\GeneratePlanPdf;
use App\Models\CatalogSpeciality;
use Illuminate\Support\Collection;
use App\ExternalServices\Asu\Worker;
use Illuminate\Support\Facades\Gate;
use Barryvdh\Snappy\Facades\SnappyPdf;
use App\Models\CatalogEducationProgram;
use Illuminate\Support\Facades\Storage;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;

class PdfController extends Controller
{

    public function index()
    {
        return Storage::download('doc/manual.pdf');
    }

    public function upload(Request $request)
    {
        if (!Gate::allows('upload-manual')) {
            abort(403);
        }

        $validated = $request->validate([
            'doc' => 'required|file|mimes:pdf',
        ]);

        $request->file('doc')->storeAs('doc', 'manual.pdf');

        return $this->success(__('messages.Updated'));
    }

    public function generatePDF(Request $request, $id)
    {
        $pdf = new GeneratePlanPdf;
        $pdf($id);
    }

    private $totalPlan;
    const FORM_ORGANIZATIONS = [
        'modular_cyclic' => 1,
        'semester' => 3,
    ];

    const FORM_ORGANIZATIONS_TABLE = [
        self::FORM_ORGANIZATIONS['modular_cyclic'] => 2,
        self::FORM_ORGANIZATIONS['semester'] => 1,
    ];


    private $model;
    private $updated_cycles;
    public function pdfview($id)
    {
        $this->model = Plan::with([
            'verification',
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
            'signatures.position'
        ])->verified()->find($id);

        $professions = [
            [
                ['title' => 'Галузь знань', 'colspan' => 6],
                ['key' => $this->model->field_knowledge_id_name, 'acolspan' => 6],
                ['title' => 'Кваліфікація', 'colspan' => 6],
                ['key' => $this->model->qualification_id_name, 'acolspan' => 6],
            ],
            [],
            [
                ['title' => 'Спеціальність', 'colspan' => 6],
                ['key' => $this->model->speciality_id_name, 'acolspan' => 6],
                ['title' => 'Термін навчання', 'colspan' => 6],
                ['key' => $this->model->studyTerm->title, 'acolspan' => 6],
            ],
            [],
            [
                ['title' => 'Спеціалізація', 'colspan' => 6],
                ['key' => $this->model->specialization_id_name, 'acolspan' => 6],
                ['title' => 'Форма навчання', 'colspan' => 6],
                ['key' => $this->model->formStudy->title, 'acolspan' => 6],
            ],
            [],
            [
                ['title' => 'Освітня програма', 'colspan' => 9],
                ['key' => $this->model->education_program_id_name, 'acolspan' => 9],
            ],
            [],
            [
                ['title' => 'Освітній (освітньо-науковий) рівень', 'colspan' => 13],
                ['key' => $this->model->educationLevel->title, 'acolspan' => 13],
                ['title' => 'Рік прийому', 'colspan' => 6],
                ['key' => $this->model->year, 'acolspan' => 6],
            ],
        ];

        $scheduleEducationProcess = json_decode($this->model->schedule_education_process, JSON_OBJECT_AS_ARRAY);
        $hoursWeeksSemesters = json_decode($this->model->hours_weeks_semesters, JSON_OBJECT_AS_ARRAY);
        $this->updateCycles(collect($this->getCyclesRow($this->model->cycles)));

        $data = [
            'options' => [
                'fullColspanTitle' => 54
            ],
            'fullColspanTitle' => 54, // temp
            'fullColspanPlan' => 22,
            'shortColspanPlan' => $this->fill(22 - 13),
            'faculty' => $this->model->facultyName,
            'title' => $this->model->title,
            'educationLevel' => $this->model->educationLevel->title,
            'year' => $this->model->year,
            'professions' => $professions,
            'header' => $scheduleEducationProcess['header'],
            'weeks' => $scheduleEducationProcess['courses'][0],
            'courses' => $scheduleEducationProcess['courses'],
            'notes' => $this->model->notes['notes'],
            'exams_table' => $this->model->getExamsTable($this->model->cycles),
            'summary_data_budget_time' => $this->model->summary_data_budget_time ?
                json_decode($this->model->summary_data_budget_time, JSON_OBJECT_AS_ARRAY) : [],
            'practical_training' => $this->model->practical_training ?
                json_decode($this->model->practical_training, JSON_OBJECT_AS_ARRAY) : [],
            'number_semesters' => $this->model->number_semesters,
            'hoursWeeksSemesters' => $hoursWeeksSemesters,
            'plan' => $this->model,
            'cycles' => $this->updated_cycles,
            'totalPlan' => $this->totalPlan,
            'FORM_ORGANIZATIONS' => self::FORM_ORGANIZATIONS,
            'FORM_ORGANIZATIONS_TABLE' => self::FORM_ORGANIZATIONS_TABLE,
            'count_exams' => $this->model->getCountExams(),
            'count_tests' => $this->getCountTests(),
            'count_coursework' => $this->getCountCoursework(),
            'subject_notes' => $this->getSubjectNotes(),
        ];

        $path = 'plans/';
        $fileName = "{$this->model->guid}.pdf";

        $pdf = SnappyPdf::loadView('pdf.plan', $data);
        $pdf->setPaper('a4')->setOrientation('landscape');
        return $pdf->inline('invoice.pdf');
    }

    function fill($length): array
    {
        return array_fill(0, $length, 0);
    }

    function getCountExams()
    {
        $result = [];
        for ($i = 0; $i < $this->model->studyTerm->semesters; $i++) {
            if ($this->model->form_organization_id == 1) {
                array_push($result, '');
            }
            array_push($result, $this->getCountWorks(['form_control_id' => 1], $i + 1));
        }
        return $result;
    }

    function getCountTests()
    {
        $result = [];
        for ($i = 0; $i < $this->model->studyTerm->semesters; $i++) {
            if ($this->model->form_organization_id == 1) {
                array_push($result, '');
            }
            array_push($result, $this->getCountWorks(['form_control_id' => 3, 'form_control_id' => 2], $i + 1));
        }
        return $result;
    }

    function getCountCoursework()
    {
        $result = [];
        for ($i = 0; $i < $this->model->studyTerm->semesters; $i++) {
            if ($this->model->form_organization_id == 1) {
                array_push($result, '');
            }
            array_push($result, $this->getCountWorks(['individual_task_id' => 2], $i + 1));
        }
        return $result;
    }

    function getCountWorks($work, $semester)
    {
        $planId = $this->model->id;
        $count = HoursModules::with('subject')->whereHas('subject', function ($querySubject) use ($planId) {
            $querySubject->with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
                $queryCycle->where('plan_id', $planId);
            });
        })->where($work)->where('semester', $semester)->count();
        return $count;
    }

    private function getCyclesRow($cycles)
    {
        $cyclesArray = [];
        $countCycles = $cycles->count() - 1;

        foreach ($cycles as $it => $cycle) {
            $cycleCopy = $cycle->toArray();
            unset($cycleCopy['subjects']);
            unset($cycleCopy['cycles']);
            $cyclesArray[] = $cycleCopy;

            if (count($cycle->subjects) > 0) {
                $cycle->subjects->transform(function ($subject, $index) {
                    $subject->index = ++$index;
                    $subject->hours_modules = $subject->hoursModules;
                    $subject->list_cycle_id = $subject->cycle->list_cycle_id;
                    $subject->total_classroom = $this->totalClassroom($subject);
                    $subject->semesters_credits_computed = $subject->semestersCredits->pluck('credit', 'semester');
                    $subject->exams_count = $subject->exams->count() ? (int)$subject->exams->first()->semester : '';
                    $subject->test_count = $subject->test->count() ? $subject->test->first()->semester : '';
                    $subject->individual_tasks = $this->getIndividualTasks($subject->hoursModules);
                    $subject->total_volume_hour = $subject->credits * Constant::NUMBER_HOURS_IN_CREDIT;
                    $subject->individual_work = $subject->total_volume_hour - $subject->hours - $subject->practices - $subject->laboratories;

                    $subject->subjects->transform(function ($subject2, $i) {


                        $subject2->hours_modules = $subject2->hoursModules;
                        $subject2->list_cycle_id = $subject2->cycle->list_cycle_id;
                        $subject2->total_classroom = $this->totalClassroom($subject2);
                        $subject2->semesters_credits_computed =
                            $subject2->semestersCredits->pluck('credit', 'semester');
                        $subject2->exams_count = $subject2->exams->count() ?
                            (int)$subject2->exams->first()->semester : '';
                        $subject2->test_count = $subject2->test->count() ?
                            $subject2->test->first()->semester : '';
                        $subject2->individual_tasks = $this->getIndividualTasks($subject2->hoursModules);
                        $subject2->total_volume_hour = $subject2->credits * Constant::NUMBER_HOURS_IN_CREDIT;
                        $subject2->individual_work = $subject2->total_volume_hour - $subject2->hours -
                            $subject2->practices - $subject2->laboratories;

                        return $subject2;
                    });

                    return $subject;
                });

                $cyclesArray = array_merge($cyclesArray, $cycle->subjects->toArray());

                $total = [
                    'total' => true,
                    'parent_id' => is_null($cycle->cycle_id) ? $cycle->id : null,
                    'title' => 'Усього',
                    'credits' => $this->GlobalSumPropertyInArray($cycle->subjects, 'credits'),
                    'total_volume_hour' => $this->GlobalSumPropertyInArray($cycle->subjects, 'total_volume_hour'),
                    'hours' => $this->GlobalSumPropertyInArray($cycle->subjects, 'hours'),
                    'practices' => $this->GlobalSumPropertyInArray($cycle->subjects, 'practices'),
                    'laboratories' => $this->GlobalSumPropertyInArray($cycle->subjects, 'laboratories'),
                    'total_classroom' => $this->GlobalSumPropertyInArray($cycle->subjects, 'total_classroom'),
                    'individual_work' => $this->GlobalSumPropertyInArray($cycle->subjects, 'individual_work'),
                    'hours_modules' => $this->getHoursModulesTotal($cycle->subjects, true),
                    'semesters_credits_computed' => $this->getSumSemestersCredits($cycle->subjects),
                ];

                $cyclesArray[] = $total;
            }

            if (!empty($cycle->cycles->cycles)) {
                $cyclesArray = array_merge($cyclesArray, $this->getCyclesRow($cycle->cycles->cycles));
            }

            $next = $it + 1;
            if ($it <= $countCycles && empty($cycles[$next]['cycle_id'])) {
                $fullTotal = [
                    'total' => true,
                    'full_total' => true,
                    'title' => 'Усього за цикл',
                    'hours_modules' => [], //Todo is if check total
                ];
                $cyclesArray[] = $fullTotal;
            }
        }

        return  $cyclesArray;
    }

    function GlobalSumPropertyInArray(Collection $items, $prop): float
    {
        $sum = $items->sum($prop);

        if ($items->where('subjects', '!=', null)->count() > 0) {
            $items->where('subjects', '!=', null)->each(function ($item) use ($prop, &$sum) {
                $sum += $this->GlobalSumPropertyInArray(collect($item['subjects']), $prop);
            });
        }
        return round($sum, 2);
    }
    public $updated_cycles1 = [];


    public function updateCycles(Collection $cycles)
    {
        $prev = 0;
        $total_cycles = [];

        $this->updated_cycles = $cycles->map(function ($cycle, $index) use (&$prev, &$total_cycles, $cycles) {
            if ($cycle['full_total'] ?? false) {
                $total = $cycles->slice($prev, $index - $prev)->filter(function ($cycle) {
                    return $cycle['total'] ?? false && !array_key_exists('full_total', $cycle);
                });

                $prev = $index;

                $data = [
                    'credits' => $this->GlobalSumPropertyInArray($total, 'credits'),
                    'total_volume_hour' => $this->GlobalSumPropertyInArray($total, 'total_volume_hour'),
                    'hours' => $this->GlobalSumPropertyInArray($total, 'hours'),
                    'practices' => $this->GlobalSumPropertyInArray($total, 'practices'),
                    'laboratories' => $this->GlobalSumPropertyInArray($total, 'laboratories'),
                    'total_classroom' => $this->GlobalSumPropertyInArray($total, 'total_classroom'),
                    'individual_work' => $this->GlobalSumPropertyInArray($total, 'individual_work'),
                    'hours_modules' => $this->getSimpleHoursModulesTotal($total),
                    'semesters_credits_computed' => $this->getSumSemestersCredits($total),
                ];

                $cycle = array_merge($cycle, $data);

                $total_cycles[] = $cycle;
            }


            return $cycle;
        });
        $total_cycles = collect($total_cycles);

        $this->totalPlan = [
            'credits' => $this->GlobalSumPropertyInArray($total_cycles, 'credits'),
            'total_volume_hour' => $this->GlobalSumPropertyInArray($total_cycles, 'total_volume_hour'),
            'hours' => $this->GlobalSumPropertyInArray($total_cycles, 'hours'),
            'practices' => $this->GlobalSumPropertyInArray($total_cycles, 'practices'),
            'laboratories' => $this->GlobalSumPropertyInArray($total_cycles, 'laboratories'),
            'total_classroom' => $this->GlobalSumPropertyInArray($total_cycles, 'total_classroom'),
            'individual_work' => $this->GlobalSumPropertyInArray($total_cycles, 'individual_work'),
            'hours_modules' => $this->getSimpleHoursModulesTotal($total_cycles),
            'semesters_credits_computed' => $this->getSumSemestersCredits($total_cycles),
        ];
    }

    function getSumSemestersCredits($subjects)
    {
        $semestersCredits = [];

        $subjects = collect($subjects);

        $subjects->each(function ($subject) use (&$semestersCredits) {
            if (!empty($subject->subjects)) {
                collect($subject->subjects)->each(function ($subSubject) use (&$semestersCredits) {
                    if ($subSubject->semesters_credits_computed->count() > 0) {
                        foreach ($subSubject->semesters_credits_computed as $key => $semesterCredit) {
                            if (isset($semestersCredits[$key])) {
                                $semestersCredits[$key] += $semesterCredit;
                            } else {
                                $semestersCredits[$key] = $semesterCredit;
                            }
                        }
                    }
                });
            }
            if (!empty($subject->semesters_credits_computed)) {
                foreach ($subject->semesters_credits_computed as $key => $semesterCredit) {
                    if (isset($semestersCredits[$key])) {
                        $semestersCredits[$key] += $semesterCredit;
                    } else {
                        $semestersCredits[$key] = $semesterCredit;
                    }
                }
            }
        });
        return $semestersCredits;
    }

    function getHoursModulesTotal($obj, $inside_hour = false)
    {

        $hours_modules_total = [];

        foreach ($obj as $item) {

            if (!empty($item->hours_modules)) {

                foreach ($item->hours_modules as $index => $hours_module) {
                    if (isset($hours_modules_total[$index])) {
                        $hours_modules_total[$index] += $inside_hour ? $hours_module->hour : $hours_module;
                    } else {
                        $hours_modules_total[$index] = $inside_hour ? $hours_module->hour : $hours_module;
                    }
                }

                if (!empty($item->subjects)) {
                    foreach ($item->subjects as $item2) {
                        if (!empty($item2->hours_modules)) {
                            foreach ($item2->hours_modules as $index => $hours_module) {
                                if (isset($hours_modules_total[$index])) {
                                    $hours_modules_total[$index] += $inside_hour ? $hours_module->hour : $hours_module;
                                } else {
                                    $hours_modules_total[$index] = $inside_hour ? $hours_module->hour : $hours_module;
                                }
                            }
                        }
                    }
                }
            }
        }

        return array_map(fn ($val) => round($val, 2), $hours_modules_total);
    }

    function getSimpleHoursModulesTotal($items)
    {
        $hours_modules_total = [];

        foreach ($items as $item) {
            if (!empty($item['hours_modules'])) {
                foreach ($item['hours_modules'] as $index => $hours_module) {
                    if (isset($hours_modules_total[$index])) {
                        $hours_modules_total[$index] += $hours_module;
                    } else {
                        $hours_modules_total[$index] = $hours_module;
                    }
                }

                if (!empty($item['subjects'])) {
                    foreach ($item['subjects'] as $item2) {
                        if (!empty($item2['hours_modules'])) {
                            foreach ($item2['hours_modules'] as $index => $hours_module) {
                                if (isset($hours_modules_total[$index])) {
                                    $hours_modules_total[$index] += $hours_module;
                                } else {
                                    $hours_modules_total[$index] = $hours_module;
                                }
                            }
                        }
                    }
                }
            }
        }

        return array_map(fn ($val) => round($val, 2), $hours_modules_total);
    }


    function totalClassroom($subject): int
    {
        return $subject->hours + $subject->practices + $subject->laboratories;
    }

    private function getIndividualTasks($hours_modules)
    {

        $individual_tasks = '';
        $hours_modules->groupBy('individualTask.id')->map(function ($individual_task, $key) use (&$individual_tasks) {
            if (in_array($key, [
                Constant::INDIVIDUAL_TASKS['COURSE_WORK'],
                Constant::INDIVIDUAL_TASKS['CONTROL_WORK']
            ])) {
                $individual_tasks .=
                    Constant::INDIVIDUAL_TASKS_SHORT[$key] . '(' .
                    $individual_task->pluck('semester')->join(',') . ') ';
            }
        });
        return trim($individual_tasks);
    }

    function getSubjectNotes()
    {
        $planId = $this->model->id;
        $result = Subject::with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
            $queryCycle->where('plan_id', $planId);
        })->select('note', 'id')->whereNotNull('note')->get();
        return $result->toArray();
    }

    public function catalogPdf(Request $request)
    {
        $plan = Plan::with('studyTerm')->select('id', 'guid', 'year', 'speciality_id', 'education_level_id', 'education_program_id', 'study_term_id')
            ->where('id', $request['id'])->first();

        $endYear = $this->calculateEndYear($plan->year, $plan->studyTerm);

        $catalog = CatalogSpeciality::with(['subjects', 'verifications', 'educationLevel'])
            ->where('selective_discipline_id', CatalogSpeciality::SPECIALITY)
            ->where('speciality_id', $plan['speciality_id'])
            ->where('catalog_education_level_id', $plan['education_level_id'])
            ->whereBetween('year', [$plan['year'],  $endYear])
            ->verified()
            ->orderBy('year', 'asc')
            ->get();

        $data = $this->prepareDate($catalog);

        $pdf = LaravelMpdf::loadView('pdf.speciality', ['data' => $data]);
        return $pdf->download("$plan->guid.pdf");
    }

    public function catalogPdf1(Request $request)
    {
        $plan = Plan::with('studyTerm')
            ->select('id', 'guid', 'year', 'speciality_id', 'education_level_id', 'education_program_id', 'study_term_id')
            ->where('id', $request['id'])->first();

        $endYear = $this->calculateEndYear($plan->year, $plan->studyTerm);

        $catalog = CatalogEducationProgram::with(['subjects', 'verifications', 'educationLevel'])
            ->where('selective_discipline_id', CatalogEducationProgram::EDUCATION_PROGRAM)
            ->where('education_program_id', $plan['education_program_id'])
            ->where('catalog_education_level_id', $plan['education_level_id'])
            ->whereBetween('year', [$plan['year'], $endYear])
            ->verified()
            ->orderBy('year', 'asc')
            ->get();

        $data = $this->prepareDate($catalog);

        $pdf = LaravelMpdf::loadView('pdf.educationProgram', ['data' => $data]);
        return $pdf->download("$plan->guid.pdf");
    }

    private function calculateEndYear($year, $studyTerm): int
    {
        $studyTermYear = $studyTerm->year;
        $studyTermMonth = $studyTerm->month ? 1 : 0;
        return $year + $studyTermYear + $studyTermMonth;
    }

    protected function getShortNames($listNames)
    {
        return $this->getShortName($listNames)->implode(', ');
    }

    protected function getShortName($collection)
    {
        $worker = new Worker();

        return $collection->map(function ($collection) use ($worker) {
            return $worker->getShortName($collection['asu_id']);
        });
    }

    private function prepareDate(Collection $collection): array
    {
        foreach ($collection as $item) {
            $item->faculty = $item->facultyName;
            $item->department = $item->departmentName;
            $item->speciality = $item->specialityIdName;
            $item->educationLevel = $item->educationLevel->title;

            if ($item->count() > 0) {
                foreach ($item->subjects as $subject) {
                    $subject->subjectName = $subject->subjectName;
                    $subject->language = $subject->languages->map(function ($collection) {
                        return $collection['language']['title'];
                    })->implode(', ');
                    $subject->lecturersTitle = $subject->getShortNames($subject->lecturers);
                    $subject->practiceTitle = $subject->getShortNames($subject->practice);
                    $subject->faculty = $subject->facultyName;
                    $subject->department = $subject->departmentName;
                    $subject->listFieldsKnowledgeName = $subject->list_fields_knowledge ? $subject->listFieldsKnowledgeName : null; // TODO: prepare
                    $subject->educationLevel = $subject->catalog_education_level_id ? $subject->educationLevel->title : null;
                    $subject->generalCompetence = $subject->general_competence;
                    $subject->learningOutcomes = $subject->learning_outcomes;
                    $subject->entryRequirementsApplicant = $subject->entry_requirements_applicants;
                    $subject->typesEducationalActivities = $subject->types_educational_activities;
                    $subject->numberAcquirers = $subject->number_acquirers;
                    $subject->limitation = $subject->limitationName;
                }
            }
        }

        return $collection->toArray();
    }
}
