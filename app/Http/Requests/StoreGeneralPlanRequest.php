<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGeneralPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|max:255',
            'faculty_id' => 'nullable|numeric',
            'department_id' => 'nullable|numeric',
            'education_level_id' => 'required|numeric',
            'form_study_id' => 'required|numeric',
            'study_term_id' => 'required|numeric',
            'year' => 'required|numeric|date_format:Y',
            'number_semesters' => 'required|numeric',
            'speciality_id' => 'nullable|numeric',
            'specialization_id' => 'nullable|numeric',
            'education_program_id' => 'nullable|numeric',
            'qualification_id' => 'nullable|numeric',
            'field_knowledge_id' => 'numeric',
            'form_organization_id' => 'required|numeric',
            'credits' => 'required|numeric',
            'schedule_education_process' => 'nullable|json',
            'hours_weeks_semesters' => 'nullable|json',
            'published' => 'required'
        ];
    }
}
