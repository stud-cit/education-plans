<?php

namespace App\Http\Requests\SpecialitySubject;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSpecialitySubjectRequest extends FormRequest
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
            'asu_id' => [
                'required',
                'numeric',
                Rule::unique('catalog_selective_subjects')->where(function ($query) {
                    return $query->where('asu_id', $this->asu_id)
                        ->where('catalog_subject_id', $this->catalog_subject_id);
                })
            ],
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'language' => 'required',
            'lecturers' => 'nullable',
            'practice' => 'required',
            'faculty_id' => 'required|numeric',
            'department_id' => 'required|numeric',
            'general_competence' => 'required|max:2000',
            'learning_outcomes' => 'required|string|max:2000',
            'entry_requirements_applicants' => 'required|string|max:2000',
            'types_educational_activities' => 'required|string|max:2000',
            'number_acquirers' => 'required|max:255',
            'limitation' => 'required|json',
            'published' => 'nullable|boolean'
        ];
    }

    // public function messages()
    // {
    //     return [
    //         'asu_id.unique' => 'В даному каталозі дисципліна вже існує.',
    //     ];
    // }
}
