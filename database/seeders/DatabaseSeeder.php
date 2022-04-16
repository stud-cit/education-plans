<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CycleSeeder::class,
            SelectiveDisciplineSeeder::class,
            SubjectSeeder::class,
            IndividualTaskSeeder::class,
            FormControlSeeder::class,
            HoursWeekSeeder::class,
            FormStudySeeder::class,
            EducationLevelSeeder::class,
            FormOrganizationSeeder::class,
            TermStudySeeder::class,
            PlanSeeder::class
        ]);
    }
}
