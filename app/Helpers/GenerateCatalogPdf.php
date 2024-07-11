<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Plan;
use App\Models\CatalogSpeciality;
use Illuminate\Support\Collection;
use App\ExternalServices\Asu\Worker;
use App\Models\CatalogEducationProgram;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;

class GenerateCatalogPdf
{
    private int $id;
    private int $endYear;
    private Plan $plan;

    public function __construct(int $id)
    {
        $this->id = $id;
        $this->plan = $this->findPlan();
        $this->endYear = Helpers::calculateEndYear($this->plan->year, $this->plan->studyTerm);
    }

    public function GenerateCatalogSpecialityPdf()
    {
        $catalog = CatalogSpeciality::with(['subjects', 'verifications', 'educationLevel'])
            ->where('selective_discipline_id', CatalogSpeciality::SPECIALITY)
            ->where('speciality_id', $this->plan['speciality_id'])
            ->where('catalog_education_level_id', $this->plan['education_level_id'])
            ->whereBetween('year', [$this->plan['year'],  $this->endYear])
            ->verified()
            ->orderBy('year', 'asc')
            ->get();

        if ($catalog->isEmpty()) {
            return;
        }

        $data = $this->prepareDate($catalog);

        $path = $this->createDirIfNotExists('//catalogs//speciality//');
        $fileName = "{$this->plan->guid}.pdf";

        $pdf = LaravelMpdf::loadView('pdf.speciality', ['data' => $data]);
        $pdf->save("{$path}{$fileName}");
    }

    public function generateCatalogEducationPdf()
    {
        $catalog = CatalogEducationProgram::with(['subjects', 'verifications', 'educationLevel'])
            ->where('selective_discipline_id', CatalogEducationProgram::EDUCATION_PROGRAM)
            ->where('education_program_id', $this->plan['education_program_id'])
            ->where('catalog_education_level_id', $this->plan['education_level_id'])
            ->whereBetween('year', [$this->plan['year'], $this->endYear])
            ->verified()
            ->orderBy('year', 'asc')
            ->get();

        if ($catalog->isEmpty()) {
            return;
        }

        $data = $this->prepareDate($catalog);

        $path = $this->createDirIfNotExists('//catalogs//educationProgram//');
        $fileName = "{$this->plan->guid}.pdf";

        $pdf = LaravelMpdf::loadView('pdf.educationProgram', ['data' => $data]);
        $pdf->save("{$path}{$fileName}");
    }

    private function findPlan()
    {
        return Plan::with('studyTerm')
            ->select('id', 'guid', 'year', 'speciality_id', 'education_level_id', 'education_program_id', 'study_term_id')
            ->where('id', $this->id)->first();
    }

    protected function getShortNames($listNames)
    {
        return $this->getShortName($listNames)->implode(', ');
    }

    protected function getShortName($collection)
    {
        $worker = new Worker();

        return $collection->map(function ($collection) use ($worker) {
            return $worker->getShortName($collection['asu_id']);
        });
    }

    private function prepareDate(Collection $collection): array
    {
        foreach ($collection as $item) {
            $item->faculty = $item->facultyName;
            $item->department = $item->departmentName;
            $item->speciality = $item->specialityIdName;
            $item->educationLevel = $item->educationLevel->title;

            if ($item->count() > 0) {
                foreach ($item->subjects as $subject) {
                    $subject->subjectName = $subject->subjectName;
                    $subject->language = $subject->languages->map(function ($collection) {
                        return $collection['language']['title'];
                    })->implode(', ');
                    $subject->lecturersTitle = $subject->getShortNames($subject->lecturers);
                    $subject->practiceTitle = $subject->getShortNames($subject->practice);
                    $subject->faculty = $subject->facultyName;
                    $subject->department = $subject->departmentName;
                    $subject->listFieldsKnowledgeName = $subject->list_fields_knowledge ? $subject->listFieldsKnowledgeName : null;
                    $subject->educationLevel = $subject->catalog_education_level_id ? $subject->educationLevel->title : null;
                    $subject->generalCompetence = $subject->general_competence;
                    $subject->learningOutcomes = $subject->learning_outcomes;
                    $subject->entryRequirementsApplicant = $subject->entry_requirements_applicants;
                    $subject->typesEducationalActivities = $subject->types_educational_activities;
                    $subject->numberAcquirers = $subject->number_acquirers;
                    $subject->limitation = $subject->limitationName;
                }
            }
        }

        return $collection->toArray();
    }

    private function createDirIfNotExists($dir)
    {
        $path = public_path() . $dir;
        $isEducationProgram = is_dir($path);

        if (!$isEducationProgram) {
            mkdir($path);
        }
        return $path;
    }
}
