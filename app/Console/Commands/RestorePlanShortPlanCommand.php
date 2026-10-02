<?php

namespace App\Console\Commands;

use App\Enums\Degree;
use App\Models\Plan;
use App\Models\ShortenedPlan;
use Illuminate\Console\Command;

class RestorePlanShortPlanCommand extends Command
{
    protected $signature = 'plan:restore-short-plan';

    protected $description = 'Command description';

    public function handle(): void
    {
        $plans = Plan::query()
           ->whereIn('id', [1801, 1802, 1803, 1804, 1805, 1806, 1807, 1808, 1809, 1810, 1811, 1812, 1813, 1814, 1815, 1816, 1817, 1818, 1819, 1820, 1821, 1822, 1823, 1824, 1825, 1826, 1827, 1828, 1829, 1830, 1831, 1832, 1833, 1834, 1835, 1836, 1837])->get();

        $this->table(
            ['id', 'title', 'parent_id','year'],
            $plans->map(fn ($p) => [
                $p->id,
                $p->title,
                $p->parent_id,
                $p->year,
            ])
        );
        $this->newLine();
        $total = count($plans);
        $this->info("Plans: {$total}");
        $this->newLine();
        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(" %current%/%max% [%bar%] %percent:3s%% — %message%\n");
        $bar->setMessage('Починаємо...');
        $bar->start();
        foreach ($plans as $plan) {
            $bar->setMessage("Відновлення посилань: {$plan->title}");
            $bar->display();

            $sh = ShortenedPlan::query()->where('plan_id', $plan->parent_id)->latest()->first();
            if ($sh) {
                $newSh = $sh->replicate();
                $newSh->plan_id = $plan->id;
                $newSh->save();
                $bar->advance();
            }
        }
    }
}
