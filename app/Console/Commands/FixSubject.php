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

        // 11157 Базова загальновійськова підготовкa
        // 11036 Національна ідентичність


        $subjects = Subject::where(['asu_id' => 11157, 'hours' => 38])->orWhere(['asu_id' => 11036, 'hours' => 38])
            ->whereHas('cycle.plan', function ($query) {
                $query->where(['form_organization_id' => MODULE_CYCLE, 'version' => 3, 'education_level_id' => 2])
                    ->whereIn('year', [2024, 2025]);
            })
            ->with([
                'hoursModules' => function ($query) {
                    $query->where('hour', 2.334);
                },
                'cycle',
                'cycle.plan' => function ($query) {
                    $query->select(
                        ['id', 'title', 'form_organization_id', 'updated_at', 'year', 'version']
                    );
                }
            ])->get();


        $subjects = $this->withProgressBar($subjects, function ($subject) {
            $subject->hours = 36;
            $subject->hoursModules()->where('hour', 2.334)->update(['hour' => 2]); // module = 8 for full plan short 4
            $subject->cycle->plan()->update(['updated_at' => now()]);
            $subject->save();

            Artisan::call('plan:generate-pdf-by-id ' . $subject->cycle->plan->id);
        });


        Log::info('updated subjecs:', ['count' => $subjects->count(), 'ids' => array_column($subjects->toArray(), 'id')]);

        $this->info("\n count: " . $subjects->count());
        return 0;
    }
}
