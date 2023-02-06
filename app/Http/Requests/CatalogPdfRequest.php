<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogPdfRequest extends FormRequest
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
            'id' => 'required|exists:App\Models\Plan,id',
            'year' => 'required|date_format:Y',
            'end_year' => 'required|date_format:Y',
            'speciality_id' => 'nullable|required_if:education_program_id,null',
            'education_program_id' => 'required_if:speciality_id,null',
            'education_level' => 'required'
        ];
    }
}
