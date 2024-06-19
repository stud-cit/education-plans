<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Http\Constant;
use App\Models\HoursModules;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\Facade\Option;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PlanShowResource;

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
        $plan = Plan::find($id);
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

        $professions = [
            [
                ['title' => 'Галузь знань', 'colspan' => 6],
                ['key' => $model->field_knowledge_id_name, 'acolspan' => 6],
                ['title' => 'Кваліфікація', 'colspan' => 6],
                ['key' => $model->qualification_id_name, 'acolspan' => 6],
            ],
            [],
            [
                ['title' => 'Спеціальність', 'colspan' => 6],
                ['key' => $model->speciality_id_name, 'acolspan' => 6],
                ['title' => 'Термін навчання', 'colspan' => 6],
                ['key' => $model->studyTerm->title, 'acolspan' => 6],
            ],
            [],
            [
                ['title' => 'Спеціалізація', 'colspan' => 6],
                ['key' => $model->specialization_id_name, 'acolspan' => 6],
                ['title' => 'Форма навчання', 'colspan' => 6],
                ['key' => $model->formStudy->title, 'acolspan' => 6],
            ],
            [],
            [
                ['title' => 'Освітня програма', 'colspan' => 9],
                ['key' => $model->education_program_id_name, 'acolspan' => 9],
            ],
            [],
            [
                ['title' => 'Освітній (освітньо-науковий) рівень', 'colspan' => 13],
                ['key' => $model->educationLevel->title, 'acolspan' => 13],
                ['title' => 'Рік прийому', 'colspan' => 6],
                ['key' => $model->year, 'acolspan' => 6],
            ],
        ];


        $data = [
            'options' => [
                'fullColspanTitle' => 54
            ],
            'fullColspanTitle' => 54, // temp
            'faculty' => $model->facultyName,
            'title' => $model->title,
            'educationLevel' => $model->educationLevel->title,
            'year' => $model->year,
            'professions' => $professions
        ];

        $pdf = Pdf::loadView('pdf.plan', $data);
        return $pdf->download('document.pdf');
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
    public function pdfview(Request $request, $id)
    {
        $this->model = Plan::with([
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
        ])->find($id);

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

        // dd($this->model->cycles->toArray());
        // $temp  = $this->getCyclesRow($this->model->cycles);
        // dd($temp);
        $this->updateCycles(collect($this->getCyclesRow($this->model->cycles)));
        // dd($this->totalPlan);
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
            'plan' => $this->model,
            'cycles' => $this->updated_cycles,
            'totalPlan' => $this->totalPlan,
            'FORM_ORGANIZATIONS' => self::FORM_ORGANIZATIONS,
            'FORM_ORGANIZATIONS_TABLE' => self::FORM_ORGANIZATIONS_TABLE,
            'count_exams' => $this->getCountExams(),
            'count_tests' => $this->getCountTests(),
            'count_coursework' => $this->getCountCoursework(),
        ];
        // dd($cycles['cycles']);
        // return view('pdf.plan', $data);

        $pdf = Pdf::loadView('pdf.plan', $data);
        $pdf->render();
        return $pdf->stream();
        // return $pdf->download('document.pdf');
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

    function getCyclesRow($cycles)
    {
        $cyclesArray = [];
        foreach ($cycles as $it => $cycle) {
            $cycleCopy = $cycle->toArray();
            unset($cycleCopy['subjects']);
            unset($cycleCopy['cycles']);
            $cyclesArray[] = $cycleCopy;

            if (count($cycle->subjects) > 0) {
                $cycle->subjects->transform(function ($item, $index) {
                    $item->index = ++$index;
                    return $item;
                });

                $cycle->subjects->transform(function ($subject) {
                    $subject->hours_modules = $subject->hoursModules;
                    $subject->list_cycle_id = $subject->cycle->list_cycle_id;
                    $subject->total_classroom = $this->totalClassroom($subject);
                    $subject->semesters_credits_computed = $subject->semestersCredits->pluck('credit', 'semester');
                    $subject->exams_count = $subject->exams->count() ? (int)$subject->exams->first()->semester : '';
                    $subject->test_count = $subject->test->count() ? $subject->test->first()->semester : '';
                    $subject->individual_tasks = $this->getIndividualTasks($subject->hoursModules);
                    $subject->total_volume_hour = $subject->credits * Constant::NUMBER_HOURS_IN_CREDIT;
                    $subject->individual_work = $subject->total_volume_hour - $subject->hours - $subject->practices - $subject->laboratories;

                    $subject->subjects->transform(function ($subject2) {
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
                        $subject2->individual_work = $subject2->total_volume_hour - $subject2->hours - $subject2->practices - $subject2->laboratories;
                        $subject2->total_volume_hour = $subject2->credits * Constant::NUMBER_HOURS_IN_CREDIT;

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

            if ($it < $cycles->count() - 1) {
                if ($cycles[$it + 1]['cycle_id'] === null) {
                    $fullTotal = [
                        'total' => true,
                        'full_total' => true,
                        'title' => 'Усього за цикл',
                        // 'hours_modules' => [], //Todo is if check total
                    ];
                    $cyclesArray[] = $fullTotal;
                }
            }
        }

        $fullTotal = [
            'total' => true,
            'full_total' => true,
            'title' => 'Усього за цикл',
            // 'hours_modules' => [], //Todo is if check total
        ];
        $cyclesArray[] = $fullTotal;


        return  $cyclesArray;
    }

    function GlobalSumPropertyInArray(Collection $items, $prop): float
    {
        // Отримуємо суму властивості `$prop` з усіх елементів колекції
        $sum = $items->sum($prop);

        // Перевіряємо, чи існує властивість `subjects` в елементах
        if ($items->where('subjects', '!=', null)->count() > 0) {
            // Рекурсивно обчислюємо суму для вкладених елементів
            $items->where('subjects', '!=', null)->each(function ($item) use ($prop, &$sum) {
                $sum += $this->GlobalSumPropertyInArray(collect($item['subjects']), $prop);
            });
        }
        // Повертаємо суму з двома десятковими знаками
        return round($sum, 2);
    }

    public function updateCycles(Collection $cycles)
    {
        $prev = 0;
        $total_cycles = [];
        $this->updated_cycles = $cycles->map(function ($cycle, $index) use (&$prev, &$total_cycles, $cycles) {
            if (array_key_exists('full_total', $cycle)) {

                $total = $cycles->slice($prev, $index)->filter(function ($cycle) {
                    if (array_key_exists('total', $cycle) && !array_key_exists('full_total', $cycle)) {
                        return $cycle;
                    }
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

                $cycle = array_merge((array) $cycle, $data);

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
        // Initialize the semestersCredits array
        $semestersCredits = [];

        // Convert the input array to a collection
        $subjects = collect($subjects);

        // Iterate through each subject in the collection
        $subjects->each(function ($subject) use (&$semestersCredits) {

            // Process sub-subjects if they exist
            if (isset($subject['subjects'])) {
                collect($subject['subjects'])->each(function ($subSubject) use (&$semestersCredits) {
                    if (!empty($subSubject->semesters_credits_computed)) {
                        foreach ($subSubject->semesters_credits_composed as $key => $semesterCredit) {
                            if (isset($semestersCredits[$key])) {
                                $semestersCredits[$key] += $semesterCredit;
                            } else {
                                $semestersCredits[$key] = $semesterCredit;
                            }
                        }
                    }
                });
            }
            // dd($subject);
            // Process main subject semesters_credits
            // dd($subject->semesters_credits_computed);
            if (!empty($subject->semesters_credits_computed)) {
                // dd($subject->semesters_credits_computed);
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
}
