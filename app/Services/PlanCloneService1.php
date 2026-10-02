<?php

namespace App\Services;

use App\Models\Cycle;
use App\Models\HoursModules;
use App\Models\Plan;
use App\Models\PlanVerification;
use App\Models\SemestersCredits;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlanCloneService1
{
    /**
     * Clone a plan with its cycles, subjects, hours modules, semester credits,
     * and optionally verification records and status.
     *
     * @param Plan $plan
     * @param User|bool|array|null $user
     * @param bool|array $copyVerification
     * @return Plan
     */
    public function clone(Plan $plan, $user = null, $copyVerification = false): Plan
    {
        if (is_bool($user) || (is_array($user) && !isset($user['id']))) {
            $copyVerification = $user;
            $user = null;
        }

        $shouldCopyVerification = is_array($copyVerification)
            ? (bool) ($copyVerification['copy_verification'] ?? $copyVerification['verification'] ?? false)
            : (bool) $copyVerification;

        return DB::transaction(function () use ($plan, $user, $shouldCopyVerification) {
            $relations = [
                'author',
                'cycles.cycles',
                'cycles.subjects.semestersCredits',
                'cycles.subjects.hoursModules',
            ];

            if ($shouldCopyVerification) {
                $relations[] = 'verification';
            }

            $model = $plan->load($relations);

            $result = Plan::removeVersionFromTitle($plan->title);

            $originalTitle = $plan->title;
            $originalNeedVerification = $plan->need_verification;
            $originalVerificationComments = $plan->verification_comments;

            $plan->need_verification = $shouldCopyVerification ? $originalNeedVerification : false;

            $clonePlan = $plan->duplicate();

            // Restore in-memory attributes of original plan
            $plan->title = $originalTitle;
            $plan->need_verification = $originalNeedVerification;
            $clonePlan->version = is_null($plan->version) ? 2 : $plan->version + 1;
            $clonePlan->title = $clonePlan->generateTitle();
            $clonePlan->parent_id = $plan->id;
            $clonePlan->author_id = $plan->author_id;
            $clonePlan->duplicate_message = null;
            $clonePlan->created_at = now();
            $clonePlan->updated_at = now();

            if ($shouldCopyVerification) {
                $clonePlan->need_verification = $originalNeedVerification;
                $clonePlan->verification_comments = $originalVerificationComments;
            } else {
                $clonePlan->need_verification = false;
                $clonePlan->verification_comments = null;
            }
            $clonePlan->update();

            if ($shouldCopyVerification && $model->verification) {
                foreach ($model->verification as $verification) {
                    PlanVerification::create([
                        'plan_id' => $clonePlan->id,
                        'user_id' => $verification->user_id,
                        'verification_statuses_id' => $verification->verification_statuses_id,
                        'status' => $verification->status,
                        'comment' => $verification->comment,
                    ]);
                }
            }

            foreach ($model->cycles as $cycle) {
                if ($cycle['cycle_id'] == null) {
                    $this->createCycle($cycle, $clonePlan->id, $model->form_organization_id);
                }
            }

            return $clonePlan;
        });
    }

    /**
     * Alias for clone.
     *
     * @param Plan $plan
     * @param User|bool|array|null $user
     * @param bool|array $copyVerification
     * @return Plan
     */
    public function copy(Plan $plan, $user = null, $copyVerification = false): Plan
    {
        return $this->clone($plan, $user, $copyVerification);
    }

    /**
     * Invokable handler.
     *
     * @param Plan $plan
     * @param User|bool|array|null $user
     * @param bool|array $copyVerification
     * @return Plan
     */
    public function __invoke(Plan $plan, $user = null, $copyVerification = false): Plan
    {
        return $this->clone($plan, $user, $copyVerification);
    }

    /**
     * Clone a plan as a project.
     *
     * @param Plan $plan
     * @param bool|array $copyVerification
     * @return Plan
     */
    public function cloneAsProject(Plan $plan, $copyVerification = false): Plan
    {
        $shouldCopyVerification = is_array($copyVerification)
            ? (bool) ($copyVerification['copy_verification'] ?? $copyVerification['verification'] ?? false)
            : (bool) $copyVerification;

        return DB::transaction(function () use ($plan, $shouldCopyVerification) {
            $relations = [
                'cycles.cycles',
                'cycles.subjects.semestersCredits',
                'cycles.subjects.hoursModules',
                'signatures',
            ];

            if ($shouldCopyVerification) {
                $relations[] = 'verification';
            }

            $model = $plan->load($relations);

            $result = Plan::removeVersionFromTitle($plan->title);

            $originalTitle = $plan->title;
            $originalNeedVerification = $plan->need_verification;
            $originalVerificationComments = $plan->verification_comments;

            $plan->title = "Проєкт $result";
            $plan->need_verification = $shouldCopyVerification ? $originalNeedVerification : false;

            $clonePlan = $plan->duplicate();

            $plan->title = $originalTitle;
            $plan->need_verification = $originalNeedVerification;

            $clonePlan->type_id = Plan::PROJECT;
            $clonePlan->parent_id = $plan->id;
            $clonePlan->duplicate_message = null;
            $clonePlan->version = null;
            $clonePlan->created_at = now();
            $clonePlan->updated_at = now();

            if ($shouldCopyVerification) {
                $clonePlan->need_verification = $originalNeedVerification;
                $clonePlan->verification_comments = $originalVerificationComments;
            } else {
                $clonePlan->need_verification = false;
                $clonePlan->verification_comments = null;
            }

            $clonePlan->update();

            $clonePlan->signatures()->each(function ($signature) {
                $signature->delete();
            });

            if ($shouldCopyVerification && $model->verification) {
                foreach ($model->verification as $verification) {
                    PlanVerification::create([
                        'plan_id' => $clonePlan->id,
                        'user_id' => $verification->user_id,
                        'verification_statuses_id' => $verification->verification_statuses_id,
                        'status' => $verification->status,
                        'comment' => $verification->comment,
                    ]);
                }
            }

            foreach ($model->cycles as $cycle) {
                if ($cycle['cycle_id'] == null) {
                    $this->createCycle($cycle, $clonePlan->id);
                }
            }

            return $clonePlan;
        });
    }

    /**
     * Recursively create cycles and copy their subjects.
     *
     * @param mixed $cycle
     * @param int $planId
     * @param int|null $cycleId
     * @return void
     */
    public function createCycle($cycle, int $planId, $form_organization_id, ?int $cycleId = null): void
    {
        $cloneCycle = Cycle::create([
            'title' => $cycle['title'],
            'cycle_id' => $cycleId,
            'list_cycle_id' => $cycle['list_cycle_id'],
            'credit' => $cycle['credit'],
            'plan_id' => $planId,
            'has_discipline' => $cycle['has_discipline'],
        ]);

        $this->copySubjectsWithoutCutting($cycle['subjects'], $form_organization_id, $cloneCycle->id);

        foreach ($cycle['cycles'] as $v) {
            $this->createCycle($v, $planId, $form_organization_id, $cloneCycle->id);
        }
    }

    /**
     * Recursively copy subjects with hours modules and semester credits.
     *
     * @param mixed $subjects
     * @param int $cycleId
     * @param int|null $subjectId
     * @return void
     */
    public function copySubjectsWithoutCutting($subjects, $form_organization_id, int $cycleId, ?int $subjectId = null): void
    {
        foreach ($subjects as $subject) {
            $bsvp = 11157; // 11157- Теоретична підготовка БЗВП
//            1: Модульно-циклова, 3: Модульно-семестрові (form_organization_id)"
            if ($subject['asu_id'] == $bsvp) {
                // 11651- Основи національного спротиву (аудиторна підготовка)
                $note = "Для здобувачів вищої освіти, звільнених від вивчення навчальної дисципліни «Основи національного спротиву (аудиторна підготовка)», викладається навчальна дисципліна «Національна ідентичність»";
                // "faculty_id" => 437 / Навчально-науковий інститут права
                // "department_id" => 433 Кафедра адміністративного, господарського права та фінансово-економічної безпеки

                $cloneSubject = Subject::create([
                    'asu_id' => 11651,
                    'cycle_id' => $cycleId,
                    'selective_discipline_id' => $subject['selective_discipline_id'],
                    'credits' => 3,
                    'hours' => 36,
                    'practices' => 24,
                    'laboratories' => $subject['laboratories'],
                    'faculty_id' => 437,
                    'department_id' => 433,
                    'subject_id' => $subjectId,
                    'note' => $note,
                ]);

                $hasSubSubjects = count($subject->subjects->toArray()) > 0;

                foreach ($subject->hoursModules as $hoursModule) {
                    if ($hoursModule['module'] == 7 && $hoursModule['semester'] == 4 && $form_organization_id == 1) {
                        HoursModules::create([
                            'course' => $hoursModule['course'],
                            'hour' => 3.67,
                            'subject_id' => $cloneSubject->id,
                            'form_control_id' => $hoursModule['form_control_id'],
                            'individual_task_id' => $hoursModule['individual_task_id'],
                            'module' => $hoursModule['module'],
                            'semester' => $hoursModule['semester'],
                        ]);

                    } else if ($hoursModule['module'] == 8 && $hoursModule['semester'] == 4 && $form_organization_id == 1) {
                        HoursModules::create([
                            'course' => $hoursModule['course'],
                            'hour' => 2.67,
                            'subject_id' => $cloneSubject->id,
                            'form_control_id' => $hoursModule['form_control_id'],
                            'individual_task_id' => $hoursModule['individual_task_id'],
                            'module' => $hoursModule['module'],
                            'semester' => $hoursModule['semester'],
                        ]);
                    } else if ($hoursModule['semester'] == 4 && $form_organization_id == 3) {
                        HoursModules::create([
                            'course' => $hoursModule['course'],
                            'hour' => 3.33,
                            'subject_id' => $cloneSubject->id,
                            'form_control_id' => $hoursModule['form_control_id'],
                            'individual_task_id' => $hoursModule['individual_task_id'],
                            'module' => $hoursModule['module'],
                            'semester' => $hoursModule['semester'],
                        ]);
                    } else {
                        HoursModules::create([
                            'course' => $hoursModule['course'],
                            'hour' => $hoursModule['hour'],
                            'subject_id' => $cloneSubject->id,
                            'form_control_id' => $hoursModule['form_control_id'],
                            'individual_task_id' => $hoursModule['individual_task_id'],
                            'module' => $hoursModule['module'],
                            'semester' => $hoursModule['semester'],
                        ]);
                    }

                }

                foreach ($subject->semestersCredits as $semestersCredit) {

                    if ($semestersCredit['semester'] == 4) {
                        SemestersCredits::create([
                            'course' => $semestersCredit['course'],
                            'subject_id' => $cloneSubject->id,
                            'credit' => 3,
                            'semester' => $semestersCredit['semester'],
                        ]);
                    } else {
                        SemestersCredits::create([
                            'course' => $semestersCredit['course'],
                            'subject_id' => $cloneSubject->id,
                            'credit' => $semestersCredit['credit'],
                            'semester' => $semestersCredit['semester'],
                        ]);
                    }

                }
            } else {
                $cloneSubject = Subject::create([
                    'asu_id' => $subject['asu_id'],
                    'cycle_id' => $cycleId,
                    'selective_discipline_id' => $subject['selective_discipline_id'],
                    'credits' => $subject['credits'],
                    'hours' => $subject['hours'],
                    'practices' => $subject['practices'],
                    'laboratories' => $subject['laboratories'],
                    'faculty_id' => $subject['faculty_id'],
                    'department_id' => $subject['department_id'],
                    'subject_id' => $subjectId,
                    'note' => $subject['note'],
                ]);

                $hasSubSubjects = count($subject->subjects->toArray()) > 0;

                foreach ($subject->hoursModules as $hoursModule) {
                    HoursModules::create([
                        'course' => $hoursModule['course'],
                        'hour' => $hoursModule['hour'],
                        'subject_id' => $cloneSubject->id,
                        'form_control_id' => $hoursModule['form_control_id'],
                        'individual_task_id' => $hoursModule['individual_task_id'],
                        'module' => $hoursModule['module'],
                        'semester' => $hoursModule['semester'],
                    ]);
                }

                foreach ($subject->semestersCredits as $semestersCredit) {
                    SemestersCredits::create([
                        'course' => $semestersCredit['course'],
                        'subject_id' => $cloneSubject->id,
                        'credit' => $semestersCredit['credit'],
                        'semester' => $semestersCredit['semester'],
                    ]);
                }
            }

            if ($hasSubSubjects) {
                $this->copySubjectsWithoutCutting(
                    $subject->subjects,
                    $form_organization_id,
                    $cycleId,
                    $cloneSubject->id
                );
            }
        }
    }
}
