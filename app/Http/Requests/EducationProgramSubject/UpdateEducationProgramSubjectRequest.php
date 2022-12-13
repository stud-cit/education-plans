<?php

namespace App\Http\Requests\EducationProgramSubject;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEducationProgramSubjectRequest extends FormRequest
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
            'id' => [
                'required',
                'exists:App\Models\CatalogSelectiveSubject,id',
                Rule::unique('catalog_selective_subjects')->ignore($this->id)
            ],
            'catalog_subject_id' => 'required|exists:App\Models\CatalogSubject,id',
            'asu_id' => 'required|numeric',
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'language' => 'required',
            'lecturers' => 'required',
            'practice' => 'required',
            'faculty_id' => 'required|numeric',
            'department_id' => 'required|numeric',
            'general_competence' => 'required|max:255',
            'learning_outcomes' => 'required|string|max:255',
            'entry_requirements_applicants' => 'required|string|max:255',
            'types_educational_activities' => 'required|string|max:255',
            'number_acquirers' => 'required|max:255',
            'limitation' => 'required|json',
            'published' => 'nullable|boolean'
        ];
    }
}