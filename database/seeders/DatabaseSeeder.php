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
            VerificationStatusesSeeder::class,
            UserSeeder::class,
            SelectiveDisciplineSeeder::class,
            IndividualTaskSeeder::class,
            FormControlSeeder::class,
            FormStudySeeder::class,
            EducationLevelSeeder::class,
            FormOrganizationSeeder::class,
            StudyTermSeeder::class,
            PlanSeeder::class,
            ListCycleSeeder::class,
            CycleSeeder::class,
            SubjectSeeder::class,
            SettingSeeder::class,
            PositionSeeder::class,
            NoteSeeder::class,
            // for v2
            SubjectLanguageSeeder::class,
            CatalogHelperTypesSeeder::class,
            SubjectHelperSeeder::class,
            CatalogGroupSeeder::class,
            CatalogSubjectSeeder::class,
            CatalogEducationLevelSeeder::class,
            SubjectVerificationStatusesSeeder::class,
            CatalogVerificationStatusSeeder::class,
            CatalogSpecialitySeed::class,
            CatalogSignatureTypeSeeder::class
        ]);
    }
}
