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
            UserActivityLogSeeder::class,
            SelectiveDisciplineSeeder::class,
            IndividualTaskSeeder::class,
            FormControlSeeder::class,
            FormStudySeeder::class,
            EducationLevelSeeder::class,
            FormOrganizationSeeder::class,
            PlanSeeder::class,
            CycleSeeder::class,
            SubjectSeeder::class,
            HoursWeekSeeder::class,
            SettingSeeder::class,
            StudyTermSeeder::class,
        ]);
    }
}
