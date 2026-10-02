<?php

namespace App\Console\Commands;

use App\Enums\Degree;
use App\Helpers\GeneratePlanPdf as Generate;
use App\Models\Plan;
use App\Services\PlanCloneService;
use Illuminate\Console\Command;

class CopyPlansCommand extends Command
{
    protected $signature = 'plans:copy';

    protected $description = 'Command description';

    public function handle(): void
    {
        $plans = Plan::query()
            ->where(['type_id' => Plan::PLAN, 'education_level_id' => Degree::BACHELOR->value, 'year' => '2025'])
            ->verified()
            ->with('author')
            ->get()
            ->groupBy(fn ($plan) => Plan::removeVersionFromTitle($plan->title))
            ->map(function ($group) {
                return $group->sortByDesc(fn ($plan) => (int) ($plan->version ?? 1))->first();
            })
            ->values();

        $this->info("1: Модульно-циклова, 3: Модульно-семестрові (form_organization_id)");
        $this->table(
            ['id', 'title', 'form_organization_id', 'version', 'created_at', 'updated_at'],
            $plans->map(fn ($p) => [
                $p->id,
                $p->title,
                $p->form_organization_id,
                $p->version,
                $p->created_at,
                $p->updated_at,
            ])
        );
        $this->newLine();

        $total = count($plans);
        $this->info("Plans: {$total}");
        $this->newLine();

        $service = app(PlanCloneService::class);
        $clonedPlans = [];

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% — %message%\n");
        $bar->setMessage('Починаємо...');
        $bar->start();

        foreach ($plans as $plan) {
            $bar->setMessage("Клонування: {$plan->title}");
            $bar->display();

            $clonedPlan = $service->clone($plan, copyVerification: true);
            $clonedPlans[] = $clonedPlan->id;

            $bar->setMessage("Генерація PDF: {$clonedPlan->title}");
            $bar->display();

            $pdf = new Generate;
            $pdf($clonedPlan->id);
            $pdf->consoleSave();

            $bar->advance();
        }

        $bar->setMessage('Готово!');
        $bar->finish();
        $this->newLine(2);

        $this->info('Скопійовані плани (id): ' . implode(', ', $clonedPlans));

        \Log::info('copied_plans', $clonedPlans);
    }
}
