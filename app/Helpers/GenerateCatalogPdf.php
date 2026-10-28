<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Models\Plan;
use App\Models\CatalogSpeciality;
use Illuminate\Support\Collection;
use App\ExternalServices\Asu\Worker;
use App\Models\CatalogEducationProgram;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Spatie\Browsershot\Browsershot;

class GenerateCatalogPdf
{
    private int $endYear;
    private Plan $plan;

    public function __construct(int $id)
    {
        $this->plan = $this->findPlan($id);
        $this->endYear = Helpers::calculateEndYear($this->plan->year, $this->plan->studyTerm);
        ini_set('memory_limit', '512M');
    }

    public function generateCatalogSpecialityPdf(): void
    {
        $catalog = $this->specialityCatalog();

        if ($catalog->isEmpty()) {
            return;
        }

        $data = $this->prepareDate($catalog);

        $this->renderCatalog('pdf.speciality', $data)
            ->save($this->catalogPath('speciality'));
    }

    public function generateCatalogEducationPdf(): void
    {
        $catalog = $this->educationProgramCatalog();

        if ($catalog->isEmpty()) {
            return;
        }

        $data = $this->prepareDate($catalog);

        $this->renderCatalog('pdf.educationProgram', $data)
            ->save($this->catalogPath('educationProgram'));
    }

    public function downloadCatalogSpecialityPdf(): StreamedResponse
    {
        return $this->downloadCatalog('pdf.speciality', $this->specialityCatalog());
    }

    public function downloadCatalogEducationPdf(): StreamedResponse
    {
        return $this->downloadCatalog('pdf.educationProgram', $this->educationProgramCatalog());
    }

    private function findPlan(int $id): Plan
    {
        return Plan::with('studyTerm')
            ->select('id', 'guid', 'year', 'speciality_id', 'education_level_id', 'education_program_id', 'study_term_id')
            ->where('id', $id)->firstOrFail();
    }

    private function downloadCatalog(string $view, Collection $catalog): StreamedResponse
    {
        $pdfContent = $this->renderCatalog($view, $this->prepareDate($catalog))->pdf();

        return response()->streamDownload(function () use ($pdfContent) {
            echo $pdfContent;
        }, "{$this->plan->guid}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function specialityCatalog(): Collection
    {
        return CatalogSpeciality::with(['subjects', 'verifications', 'educationLevel'])
            ->where('selective_discipline_id', CatalogSpeciality::SPECIALITY)
            ->where('speciality_id', $this->plan['speciality_id'])
            ->where('catalog_education_level_id', $this->plan['education_level_id'])
            ->whereBetween('year', [$this->plan['year'], $this->endYear])
            ->verified()
            ->orderBy('year', 'asc')
            ->get();
    }

    private function educationProgramCatalog(): Collection
    {
        return CatalogEducationProgram::with(['subjects', 'verifications', 'educationLevel'])
            ->where('selective_discipline_id', CatalogEducationProgram::EDUCATION_PROGRAM)
            ->where('education_program_id', $this->plan['education_program_id'])
            ->where('catalog_education_level_id', $this->plan['education_level_id'])
            ->whereBetween('year', [$this->plan['year'], $this->endYear])
            ->verified()
            ->orderBy('year', 'asc')
            ->get();
    }

    private function renderCatalog(string $view, array $data): Browsershot
    {
        $pdf = Browsershot::html(view($view, ['data' => $data])->render())
            ->format('A4')
            ->landscape()
            ->margins(15, 15, 15, 15)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->noSandbox()
            ->addChromiumArguments([
                'allow-file-access-from-files',
                'disable-web-security',
            ]);

        if ($nodeBinary = env('BROWSERSHOT_NODE_BINARY', env('NODE_BINARY'))) {
            $pdf->setNodeBinary($nodeBinary);
        }

        if ($npmBinary = env('BROWSERSHOT_NPM_BINARY', env('NPM_BINARY'))) {
            $pdf->setNpmBinary($npmBinary);
        }

        if ($chromePath = env('BROWSERSHOT_CHROME_PATH', env('CHROME_PATH'))) {
            $pdf->setChromePath($chromePath);
        }

        $nodeModulePath = env('BROWSERSHOT_NODE_MODULE_PATH', env('NODE_MODULES_PATH', base_path('node_modules')));
        if ($nodeModulePath) {
            $pdf->setNodeModulePath($nodeModulePath);
        }

        if ($timeout = env('BROWSERSHOT_TIMEOUT')) {
            $pdf->timeout((int) $timeout);
        }

        return $pdf;
    }

    private function catalogPath(string $catalogType): string
    {
        $directory = public_path("catalogs/{$catalogType}");

        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new \RuntimeException("Unable to create catalog directory: {$directory}");
        }

        return "{$directory}/{$this->plan->guid}.pdf";
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

}
