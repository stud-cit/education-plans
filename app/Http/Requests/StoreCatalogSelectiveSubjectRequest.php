<?php

namespace App\Http\Requests;

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
            'asu_id' => 'null|string|max:255',
            'title' => 'required|max:255',
            'title_en' => 'required_if:title,null',
            'list_fields_knowledge' => 'required|json', //json
            'faculty_id' => 'required|max:255',
            'department_id' => 'required|max:255',
            'general_competence' => 'required|max:255',
            'learning_outcomes' => 'required|max:255',
            'types_educational_activities' => 'required|max:255',
            'number_acquirers' => 'required|max:255',
            'entry_requirements_applicants',
            'limitation' => 'required|json'
        ];
    }
}
