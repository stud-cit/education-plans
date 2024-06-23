<?php

namespace App\Helpers;

use App\Models\Plan;
use App\Http\Constant;
use App\Models\Subject;
use App\Models\HoursModules;
use Illuminate\Support\Collection;
use Barryvdh\Snappy\Facades\SnappyPdf;

class GeneratePlanPdf
{

    private const FORM_ORGANIZATIONS = [
        'modular_cyclic' => 1,
        'semester' => 3,
    ];

    private const FORM_ORGANIZATIONS_TABLE = [
        self::FORM_ORGANIZATIONS['modular_cyclic'] => 2,
        self::FORM_ORGANIZATIONS['semester'] => 1,
    ];

    private $model;
    private $updated_cycles;
    private $totalPlan;
    private $code = 0;
    private $pdf;

    public function __invoke($id)
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
        ])->select('*')->verified()->find($id);

        $this->generate();
    }

    public function getCode()
    {
        return $this->code;
    }

    public function generate()
    {
        if (!$this->model) {
            $this->code = -1;
            return $this->code;
        }

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
            'count_exams' => $this->getCountExams(),
            'count_tests' => $this->getCountTests(),
            'count_coursework' => $this->getCountCoursework(),
            'subject_notes' => $this->getSubjectNotes(),
        ];

        $this->pdf = SnappyPdf::loadView('pdf.plan', $data);
        $this->pdf->setPaper('a4')->setOrientation('landscape');

        $this->code = 1;
        return $this->code;
    }

    public function consoleSave()
    {
        $path = 'plans/';
        $fileName = "{$this->model->guid}.pdf";
        $publicPath = public_path("{$path}{$fileName}");
        $this->pdf->save($publicPath, true);
    }

    public function save()
    {
        $path = 'plans/';
        $fileName = "{$this->model->guid}.pdf";

        $this->pdf->save("{$path}{$fileName}", true);
    }

    private function fill($length): array
    {
        return array_fill(0, $length, 0);
    }

    private function getCountExams()
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

    private function getCountTests()
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

    private function getCountCoursework()
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

    private function getCountWorks($work, $semester)
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

    private function GlobalSumPropertyInArray(Collection $items, $prop): float
    {
        $sum = $items->sum($prop);

        if ($items->where('subjects', '!=', null)->count() > 0) {
            $items->where('subjects', '!=', null)->each(function ($item) use ($prop, &$sum) {
                $sum += $this->GlobalSumPropertyInArray(collect($item['subjects']), $prop);
            });
        }
        return round($sum, 2);
    }

    private function updateCycles(Collection $cycles)
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

    private function getSumSemestersCredits($subjects)
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

    private function getHoursModulesTotal($obj, $inside_hour = false)
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

    private function getSimpleHoursModulesTotal($items)
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


    private function totalClassroom($subject): int
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

    private function getSubjectNotes()
    {
        $planId = $this->model->id;
        $result = Subject::with('cycle')->whereHas('cycle', function ($queryCycle) use ($planId) {
            $queryCycle->where('plan_id', $planId);
        })->select('note', 'id')->whereNotNull('note')->get();
        return $result->toArray();
    }
}
