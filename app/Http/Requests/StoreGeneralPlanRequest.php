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
            'faculty_id' => 'required|numeric',
            'department_id' => 'required|numeric',
            'education_level_id' => 'required|numeric',
            'form_study_id' => 'required|numeric',
            'study_term_id' => 'required|numeric',
            'year' => 'required|numeric|date_format:Y',
            'number_semesters' => 'required|numeric',
            'specialization_id' => 'required|numeric', //specialityId todo
            'specialization' => 'max:255',
            'education_program_id' => 'required|numeric',
            'qualification_id' => 'required|numeric',
            'field_knowledge_id' => 'required|numeric',
            'form_organization_id' => 'required|numeric',
            'credits' => 'required|numeric',
        ];
    }
}
