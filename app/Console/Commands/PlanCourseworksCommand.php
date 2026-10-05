<?php

namespace App\Console\Commands;

use App\Models\Plan;
use Illuminate\Console\Command;

class PlanCourseworksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:courseworks {planId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Показати дисципліни з курсовими роботами за семестрами для навчального плану';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $planId = $this->argument('planId');
        $plan = Plan::find($planId);

        if (!$plan) {
            $this->error("План з ID {$planId} не знайдено.");
            return 1;
        }

        $semestersCount = $plan->studyTerm ? $plan->studyTerm->semesters : ($plan->number_semesters ?? 8);
        $this->info("План ID: {$plan->id} | {$plan->title}");
        $this->info("Кількість семестрів: {$semestersCount}");

        for ($semester = 1; $semester <= $semestersCount; $semester++) {
            $subjects = $plan->getCourseWorkSubjects($semester);
            $count = $plan->getCountWorks(['individual_task_id' => 2], $semester);

            if ($count > 0 || $subjects->isNotEmpty()) {
                $this->warn("Семестр {$semester}: знайдено {$count} курсових робіт");
                foreach ($subjects as $title) {
                    $this->line("  - {$title}");
                }
            } else {
                $this->line("Семестр {$semester}: курсових робіт немає");
            }
        }

        $error = $plan->courseWorksHasErrors();
        if ($error) {
            $this->error("Помилка валідації: {$error}");
        } else {
            $this->info("Перевищень кількості курсових робіт немає.");
        }

        return 0;
    }
}
