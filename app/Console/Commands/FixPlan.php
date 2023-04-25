<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixPlan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixed plan';

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
        $plans = \App\Models\Plan::whereNotNull('parent_id')->select('id', 'parent_id');

        $this->table(
            ['id', 'parent_id'],
            $plans->get()->toArray()
        );

        $plans->update(['type_id' => 2]);
    }
}
