<?php

namespace App\Console\Commands;

use App\Models\Subject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class FixSubject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:fix-subject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix subject Basic Military Training';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        define("MODULE_CYCLE", 1); // Модульно-циклова
        $plans = [];

        $subjects = Subject::whereIn('asu_id', [11157, 1048, 9401, 8221])->whereNull('note')
            ->whereHas('cycle.plan', function ($query) {
                $query
                    //->where(['form_organization_id' => MODULE_CYCLE, /* 'version' => 3, */ 'education_level_id' => 2])
                    ->whereIn('year', [2026]);
            })
            ->with([
                'cycle',
                'cycle.plan' => function ($query) {
                    $query->select(['id', 'title', 'form_organization_id', 'updated_at', 'year', 'version']);
                }
            ])->get();


        $subjects = $this->withProgressBar($subjects, function ($subject) use (&$plans) {

            $planId = $subject->cycle->plan->id ?? null;
            $plans[] = $planId;

            switch ($subject->asu_id) {
                case 11157: // theoretical training BCMT
                    $subject->note = 'Для здобувачів, які не вивчають дисципліну «Теоретична підготовка БЗВП», викладається навчальна дисципліна «Національна ідентичність»';
                    break;
                case 1048: // English Language
                case 9401: // English Language (Professional Communication)
                case 8221:
                    $subject->note = 'Для іноземних здобувачів вищої освіти викладається навчальна дисципліна «Українська мова як іноземна»';
                    break;
            }

            $subject->cycle->plan()->update(['updated_at' => now()]);
            $subject->save();

            if (isset($planId)) {
                Artisan::call('plan:generate-pdf-by-id ' . $planId);
            } else {
                Log::error('Plan ID not found for subject', ['subject_id' => $subject->id, 'plan_id' => $planId]);
            }
        });

        $subject_count = $subjects->count();
        Log::info('updated subjecs:', ['count' => $subject_count, 'ids' => array_column($subjects->toArray(), 'id')]);
        $this->info("\n count: " . $subject_count);

        $uniquePlans = array_unique($plans);
        $this->info("plans: " . implode(', ', $uniquePlans));
        Log::info('updated plans:', ['plan_ids' => $uniquePlans]);

        return 0;
    }
}
