<?php

namespace App\Http\Requests\CatalogEducationProgram;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class CopyRequest extends FormRequest
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
            'year' => [
                'required', 'date_format:Y',
                Rule::unique('catalog_subjects')->where(function ($query) {
                    return $query
                        ->where('year', $this->year)
                        ->where('catalog_education_level_id', $this->catalog_education_level_id)
                        ->where('education_program_id', $this->education_program_id);
                })
            ],
            'education_program_id' => 'required|integer'
        ];
    }

    public function messages()
    {
        return [
            'year.unique' => "Такий каталог вже існує!",
        ];
    }
}
