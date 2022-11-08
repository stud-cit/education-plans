<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogSelectiveSubjectRequest extends FormRequest
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
            'catalog_subject_id' => 'required|exists:App\Models\CatalogSubject,id',
            'catalog_education_level_id' => 'required|exists:App\Models\CatalogEducationLevel,id',
            'asu_id' => [
                'required',
                'numeric',
                Rule::unique('catalog_selective_subjects')->where(function ($query) {
                    return $query->where('asu_id', $this->asu_id)
                        ->where('catalog_subject_id', $this->catalog_subject_id);
                })
            ],
            'title' => 'required|string|max:255', // ?
            'title_en' => 'nullable|string|max:255', // ?
            'language' => 'required', // TODO: how validate?
            'lecturers' => 'required',
            'practice' => 'required',
            'list_fields_knowledge' => 'required|json', //json
            'faculty_id' => 'required|max:255',
            'department_id' => 'required|max:255',
            'general_competence' => 'required|max:255',
            'learning_outcomes' => 'required|string|max:255',
            'entry_requirements_applicants' => 'required|string|max:255',
            'types_educational_activities' => 'required|string|max:255',
            'number_acquirers' => 'required|max:255',
            'entry_requirements_applicants',
            'limitation' => 'required|json' // json
        ];
    }

    public function messages()
    {
        return [
            'asu_id.unique' => 'В даній групі предмет вже існує.',
        ];
    }
}
