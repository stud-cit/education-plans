<?php

namespace App\Http\Resources;

use App\Helpers\Helpers;
use App\Models\CatalogSpeciality;
use App\Models\CatalogEducationProgram;
use App\Http\Resources\VerificationPlanResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->planTitle,
            'year' => $this->year,
            'study_term_id' => $this->study_term_id,
            'faculty_id' => $this->faculty_id,
            'faculty' => $this->facultyName,
            'short_faculty' => $this->shortFacultyName,
            'department_id' => $this->department_id,
            'department' => $this->departmentName,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'author_id' => $this->author_id,
            'author' => $this->author->name,
            'type_id' => $this->type->title,
            'actions' => $this->actions(),
            'published' => $this->published,
            'need_verification' => $this->need_verification,
            'user_verifications' => VerificationPlanResource::collection($this->user_verifications),
            'verification' => $this->approvedPlan ? __('variables.Verified') : __('variables.NotVerified'),
            'catalog_education_programs' => $this->catalogEducationPrograms(),
            'catalog_speciality' => $this->catalogSpeciality(),
            'deleted_at' => $this->deleted_at
        ];
    }

    protected function catalogSpeciality()
    {
        $endYear = Helpers::calculateEndYear($this->year, $this->studyTerm);

        return CatalogSpeciality::with(['subjects', 'verifications', 'educationLevel'])
            ->where('selective_discipline_id', CatalogSpeciality::SPECIALITY)
            ->where('speciality_id', $this->speciality_id)
            ->where('catalog_education_level_id', $this->education_level_id)
            ->whereBetween('year', [$this->year,  $endYear])
            ->verified()
            ->count() > 0;
    }

    protected function catalogEducationPrograms()
    {
        $endYear = Helpers::calculateEndYear($this->year, $this->studyTerm);

        return CatalogEducationProgram::with(['subjects', 'verifications', 'educationLevel'])
            ->where('selective_discipline_id', CatalogEducationProgram::EDUCATION_PROGRAM)
            ->where('education_program_id', $this->education_program_id)
            ->where('catalog_education_level_id', $this->education_level_id)
            ->whereBetween('year', [$this->year, $endYear])
            ->verified()
            ->count() > 0;
    }
}
