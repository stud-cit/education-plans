<?php

namespace App\Console\Commands;

use App\Enums\Degree;
use App\Helpers\GenerateCatalogPdf;
use App\Helpers\GeneratePlanPdf as Generate;
use App\Models\Plan;
use App\Services\PlanCloneService2 as PlanCloneService;
use Illuminate\Console\Command;

class CopyPlansCommand4 extends Command
{
    protected $signature = 'plans:copy4 {--dry-run} {--id=}';

    protected $description = 'Command description';

    public function handle(): void
    {
        $id = $this->option('id');

        $plans = Plan::query()
            ->when($id, fn($q) => $q->where('id', $id))
            ->when(! $id, function ($q) {
                return $q->where(['type_id' => Plan::SHORT, 'education_level_id' => Degree::BACHELOR->value, 'year' => '2026']);
            })
            ->verified()
            ->with('author')
            ->get()
            ->groupBy(fn($plan) => Plan::removeVersionFromTitle($plan->title))
            ->map(function ($group) {
                return $group->sortByDesc(fn($plan) => (int) ($plan->version ?? 1))->first();
            })
            ->values();

        $this->table(
            ['id', 'title', 'form_organization_id', 'version',  'year'],
            $plans->map(fn($p) => [
                $p->id,
                $p->title,
                $p->form_organization_id,
                $p->version,
                $p->year,
            ])
        );
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info('Dry run mode. No plans will be copied.');
            return;
        }

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

            $pdfVatalog = new GenerateCatalogPdf($clonedPlan->id);
            $pdfVatalog->generateCatalogSpecialityPdf();
            $pdfVatalog->generateCatalogEducationPdf();
            $bar->advance();
        }

        $bar->setMessage('Готово!');
        $bar->finish();
        $this->newLine(2);

        $this->info('Скопійовані плани (id): ' . implode(', ', $clonedPlans));

        \Log::info('copied_plans1', $clonedPlans);
    }
}
