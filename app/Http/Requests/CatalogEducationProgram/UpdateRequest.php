<?php

namespace App\Http\Requests\CatalogEducationProgram;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'id' => ['required', Rule::unique('catalog_subjects')->ignore($this->id, 'id')],
            'year' => [
                'required', 'date_format:Y',
                Rule::unique('catalog_subjects')->where(function ($query) {
                    return $query->where('year', $this->year)
                        ->where('education_program_id', $this->education_program_id);
                })
            ],
            'education_program_id' => 'required',
            'faculty_id' => 'required',
            'department_id' => 'required',
            'catalog_education_level_id' => 'required|exists:App\Models\CatalogEducationLevel,id'
        ];
    }
}
